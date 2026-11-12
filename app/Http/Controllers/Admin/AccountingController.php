<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingController extends Controller
{
    public function index(Request $request, AccountingService $accounting): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = Carbon::parse($validated['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now()->toDateString())->endOfDay();
        $salesCents = OrderPayment::query()
            ->whereBetween('paid_at', [$from, $to])
            ->get(['amount'])
            ->sum(fn (OrderPayment $payment): int => $accounting->toCents((string) $payment->amount));
        $expenseCents = Expense::query()
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->get(['amount'])
            ->sum(fn (Expense $expense): int => $accounting->toCents((string) $expense->amount));

        return view('admin.accounting.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'salesTotal' => $accounting->formatCents($salesCents),
            'expenseTotal' => $accounting->formatCents($expenseCents),
            'netTotal' => $accounting->formatCents($salesCents - $expenseCents),
            'payments' => OrderPayment::query()->with('order')->whereBetween('paid_at', [$from, $to])->latest('paid_at')->limit(50)->get(),
            'expenses' => Expense::query()->with('category')->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])->latest('expense_date')->limit(50)->get(),
            'categories' => ExpenseCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'cashSession' => CashSession::query()->whereNull('closed_at')->latest('opened_at')->first(),
            'unpaidOrders' => Order::query()->with('payments')->whereIn('payment_status', ['unpaid', 'partial'])->latest()->limit(50)->get(),
        ]);
    }

    public function storePayment(Request $request, Order $order, AccountingService $accounting): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'method' => ['required', 'in:cash,card,upi,other'],
            'reference' => ['nullable', 'string', 'max:120'],
        ]);

        $accounting->recordPayment($order, $validated, $request->user());

        return back()->with('success', 'Offline payment recorded.');
    }

    public function storeExpense(Request $request, AccountingService $accounting): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['nullable', 'integer', 'exists:expense_categories,id'],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'payment_method' => ['required', 'in:cash,card,upi,other'],
            'vendor' => ['nullable', 'string', 'max:120'],
            'reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $accounting->recordExpense($validated, $request->user());

        return back()->with('success', 'Expense recorded.');
    }

    public function storeExpenseCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:expense_categories,name'],
        ]);

        ExpenseCategory::create($validated);

        return back()->with('success', 'Expense category added.');
    }

    public function openCashSession(Request $request, AccountingService $accounting): RedirectResponse
    {
        $validated = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
        ]);

        $accounting->openCashSession($request->user(), $validated['opening_cash']);

        return back()->with('success', 'Cash session opened.');
    }

    public function closeCashSession(Request $request, CashSession $cashSession, AccountingService $accounting): RedirectResponse
    {
        $validated = $request->validate([
            'actual_closing_cash' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'closing_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $accounting->closeCashSession(
            $cashSession,
            $request->user(),
            $validated['actual_closing_cash'],
            $validated['closing_notes'] ?? null
        );

        return back()->with('success', 'Cash session closed.');
    }

    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = Carbon::parse($validated['from'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($validated['to'] ?? now()->toDateString())->endOfDay();
        $payments = OrderPayment::query()->with('order')->whereBetween('paid_at', [$from, $to])->orderBy('paid_at')->get();
        $expenses = Expense::query()->with('category')->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])->orderBy('expense_date')->get();

        return response()->streamDownload(function () use ($payments, $expenses): void {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['type', 'date', 'reference', 'description', 'method', 'amount']);
            foreach ($payments as $payment) {
                fputcsv($file, ['sale', $payment->paid_at->toDateTimeString(), $payment->order->public_ref, 'Order payment', $payment->method, $payment->amount]);
            }
            foreach ($expenses as $expense) {
                fputcsv($file, ['expense', $expense->expense_date->toDateString(), $expense->reference, $expense->category?->name ?: $expense->vendor, $expense->payment_method, '-'.$expense->amount]);
            }
            fclose($file);
        }, sprintf('the-kafe-accounting-%s-to-%s.csv', $from->toDateString(), $to->toDateString()), [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
