<?php

namespace App\Services;

use App\Models\CashSession;
use App\Models\CashTransaction;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountingService
{
    /**
     * @param  array{amount: string, method: string, reference?: ?string}  $details
     */
    public function recordPayment(Order $order, array $details, User $actor): OrderPayment
    {
        return DB::transaction(function () use ($order, $details, $actor): OrderPayment {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $paidCents = $lockedOrder->payments()
                ->get(['amount'])
                ->sum(fn (OrderPayment $payment): int => $this->toCents((string) $payment->amount));
            $remainingCents = $this->toCents((string) $lockedOrder->total_amount) - $paidCents;
            $amountCents = $this->toCents($details['amount']);

            if ($amountCents > $remainingCents) {
                throw ValidationException::withMessages([
                    'amount' => 'The payment cannot exceed the unpaid order balance.',
                ]);
            }

            $payment = $lockedOrder->payments()->create([
                'method' => $details['method'],
                'amount' => $this->formatCents($amountCents),
                'reference' => $details['reference'] ?? null,
                'recorded_by' => $actor->id,
                'paid_at' => now(),
            ]);

            $lockedOrder->update([
                'payment_status' => $paidCents + $amountCents >= $this->toCents((string) $lockedOrder->total_amount)
                    ? 'paid'
                    : 'partial',
            ]);

            if ($details['method'] === 'cash') {
                $this->recordCashTransaction(
                    'in',
                    $amountCents,
                    sprintf('Payment for order %s', $lockedOrder->public_ref),
                    $actor
                );
            }

            return $payment;
        });
    }

    /**
     * @param array{
     *     expense_category_id?: ?int,
     *     expense_date: string,
     *     amount: string,
     *     payment_method: string,
     *     vendor?: ?string,
     *     reference?: ?string,
     *     notes?: ?string
     * } $details
     */
    public function recordExpense(array $details, User $actor): Expense
    {
        return DB::transaction(function () use ($details, $actor): Expense {
            $expense = Expense::create($details + ['created_by' => $actor->id]);

            if ($expense->payment_method === 'cash') {
                $this->recordCashTransaction(
                    'out',
                    $this->toCents((string) $expense->amount),
                    sprintf('Expense: %s', $expense->vendor ?: 'Unspecified vendor'),
                    $actor
                );
            }

            return $expense;
        });
    }

    public function openCashSession(User $actor, string $openingCash): CashSession
    {
        return DB::transaction(function () use ($actor, $openingCash): CashSession {
            $lock = DB::table('cash_session_locks')->where('id', 1)->lockForUpdate()->first();
            if (! $lock) {
                throw new \LogicException('The cash-session guard row is missing. Run the database migrations.');
            }

            if (CashSession::query()->whereNull('closed_at')->lockForUpdate()->exists()) {
                throw ValidationException::withMessages([
                    'cash_session' => 'A cash session is already open.',
                ]);
            }

            return CashSession::create([
                'opened_by' => $actor->id,
                'opening_cash' => $openingCash,
                'opened_at' => now(),
            ]);
        });
    }

    public function closeCashSession(CashSession $session, User $actor, string $actualCash, ?string $notes = null): CashSession
    {
        return DB::transaction(function () use ($session, $actor, $actualCash, $notes): CashSession {
            $lockedSession = CashSession::query()->lockForUpdate()->findOrFail($session->id);
            if ($lockedSession->closed_at) {
                throw ValidationException::withMessages([
                    'cash_session' => 'This cash session has already been closed.',
                ]);
            }

            $incomingCents = $lockedSession->transactions()
                ->where('type', 'in')
                ->get(['amount'])
                ->sum(fn (CashTransaction $transaction): int => $this->toCents((string) $transaction->amount));
            $outgoingCents = $lockedSession->transactions()
                ->where('type', 'out')
                ->get(['amount'])
                ->sum(fn (CashTransaction $transaction): int => $this->toCents((string) $transaction->amount));
            $expectedCents = $this->toCents((string) $lockedSession->opening_cash) + $incomingCents - $outgoingCents;

            $lockedSession->update([
                'closed_by' => $actor->id,
                'expected_closing_cash' => $this->formatCents($expectedCents),
                'actual_closing_cash' => $actualCash,
                'closing_notes' => $notes,
                'closed_at' => now(),
            ]);

            return $lockedSession;
        });
    }

    private function recordCashTransaction(string $type, int $amountCents, string $description, User $actor): void
    {
        $session = CashSession::query()
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->lockForUpdate()
            ->first();

        if (! $session) {
            throw ValidationException::withMessages([
                'cash_session' => 'Open a cash session before recording a cash payment or expense.',
            ]);
        }

        CashTransaction::create([
            'cash_session_id' => $session->id,
            'created_by' => $actor->id,
            'type' => $type,
            'amount' => $this->formatCents($amountCents),
            'description' => $description,
        ]);
    }

    public function toCents(string $amount): int
    {
        if (! preg_match('/^(-?)(\d+)(?:\.(\d{1,2}))?$/', $amount, $matches)) {
            throw new \InvalidArgumentException('Amounts must have at most two fractional digits.');
        }

        $cents = ((int) $matches[2] * 100) + (int) str_pad($matches[3] ?? '0', 2, '0');

        return ($matches[1] ?? '') === '-' ? -$cents : $cents;
    }

    public function formatCents(int $amount): string
    {
        $absoluteAmount = abs($amount);
        $formatted = intdiv($absoluteAmount, 100).'.'.str_pad((string) ($absoluteAmount % 100), 2, '0', STR_PAD_LEFT);

        return $amount < 0 ? '-'.$formatted : $formatted;
    }
}
