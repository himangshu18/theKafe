<?php

namespace Tests\Feature;

use App\Models\CashSession;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_payments_and_expenses_reconcile_when_the_session_closes(): void
    {
        $owner = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $order = $this->createOrder('25.00');
        $this->actingAs($owner);

        $this->post(route('admin.accounting.cash-sessions.store'), ['opening_cash' => '100.00'])
            ->assertSessionHasNoErrors();
        $session = CashSession::query()->firstOrFail();

        $this->post(route('admin.orders.payments.store', $order), [
            'amount' => '25.00',
            'method' => 'cash',
        ])->assertSessionHasNoErrors();
        $this->post(route('admin.accounting.expenses.store'), [
            'expense_date' => now()->toDateString(),
            'amount' => '10.25',
            'payment_method' => 'cash',
            'vendor' => 'Milk supplier',
        ])->assertSessionHasNoErrors();
        $this->patch(route('admin.accounting.cash-sessions.close', $session), [
            'actual_closing_cash' => '114.75',
        ])->assertSessionHasNoErrors();

        $this->assertSame('paid', $order->refresh()->payment_status);
        $this->assertSame('114.75', $session->refresh()->expected_closing_cash);
        $this->assertSame(2, $session->transactions()->count());
        $this->assertDatabaseHas('cash_transactions', [
            'cash_session_id' => $session->id,
            'type' => 'out',
            'amount' => '10.25',
        ]);
        $this->assertSame(1, OrderPayment::query()->where('order_id', $order->id)->count());
    }

    public function test_cash_payment_is_rejected_without_an_open_session(): void
    {
        $owner = User::factory()->create(['is_admin' => true, 'admin_role' => 'owner']);
        $order = $this->createOrder('25.00');

        $this->actingAs($owner)
            ->from(route('admin.accounting.index'))
            ->post(route('admin.orders.payments.store', $order), [
                'amount' => '25.00',
                'method' => 'cash',
            ])
            ->assertRedirect(route('admin.accounting.index'))
            ->assertSessionHasErrors('cash_session');

        $this->assertSame('unpaid', $order->refresh()->payment_status);
        $this->assertDatabaseCount('order_payments', 0);
        $this->assertDatabaseCount('cash_transactions', 0);
    }

    private function createOrder(string $total): Order
    {
        return Order::create([
            'public_ref' => 'KFTESTACCOUNTING001',
            'customer_name' => 'Test Customer',
            'customer_phone' => '9876543210',
            'total_amount' => $total,
            'discount_amount' => '0.00',
            'status' => 'accepted',
            'source' => 'website',
            'order_type' => 'pickup',
            'payment_status' => 'unpaid',
            'idempotency_key' => 'test-accounting-idempotency-key',
            'received_at' => now(),
        ]);
    }
}
