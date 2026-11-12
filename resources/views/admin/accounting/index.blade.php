@extends('layouts.admin')

@section('heading', 'Accounting')

@section('content')
<div class="space-y-6">
    <form method="GET" action="{{ route('admin.accounting.index') }}" class="flex flex-wrap items-end gap-3 rounded-xl bg-white p-4 shadow-sm">
        <div>
            <label for="from" class="block text-xs font-medium text-gray-600">From</label>
            <input id="from" type="date" name="from" value="{{ $from }}" class="mt-1 rounded-md border-gray-300 text-sm">
        </div>
        <div>
            <label for="to" class="block text-xs font-medium text-gray-600">To</label>
            <input id="to" type="date" name="to" value="{{ $to }}" class="mt-1 rounded-md border-gray-300 text-sm">
        </div>
        <button class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white">Apply</button>
        <a href="{{ route('admin.accounting.export', ['from' => $from, 'to' => $to]) }}" class="rounded-md border border-kafe-200 px-4 py-2 text-sm font-semibold text-kafe-800">Export CSV</a>
    </form>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Recorded sales</p><p class="mt-2 text-2xl font-bold">{{ config('kafe.currency') }}{{ $salesTotal }}</p></div>
        <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Expenses</p><p class="mt-2 text-2xl font-bold">{{ config('kafe.currency') }}{{ $expenseTotal }}</p></div>
        <div class="rounded-xl bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Net recorded</p><p class="mt-2 text-2xl font-bold">{{ config('kafe.currency') }}{{ $netTotal }}</p></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Record an offline payment</h2>
            <div class="mt-4 max-h-80 space-y-4 overflow-auto">
                @forelse($unpaidOrders as $order)
                    @php($paid = (float) $order->payments->sum('amount'))
                    <form method="POST" action="{{ route('admin.orders.payments.store', $order) }}" class="rounded-lg border border-gray-100 p-3">
                        @csrf
                        <div class="flex justify-between gap-3 text-sm">
                            <span class="font-medium">{{ $order->public_ref }} · {{ $order->customer_name }}</span>
                            <span>Due {{ config('kafe.currency') }}{{ number_format((float) $order->total_amount - $paid, 2) }}</span>
                        </div>
                        <div class="mt-3 grid gap-2 sm:grid-cols-3">
                            <input name="amount" type="number" min="0.01" step="0.01" required placeholder="Amount" class="rounded-md border-gray-300 text-sm">
                            <select name="method" required class="rounded-md border-gray-300 text-sm">
                                @foreach(['cash', 'upi', 'card', 'other'] as $method)<option value="{{ $method }}">{{ strtoupper($method) }}</option>@endforeach
                            </select>
                            <button class="rounded-md bg-kafe-800 px-3 py-2 text-xs font-semibold text-white">Record payment</button>
                        </div>
                    </form>
                @empty
                    <p class="text-sm text-gray-500">There are no unpaid orders.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Cash session</h2>
            @if($cashSession)
                <p class="mt-2 text-sm text-gray-600">Opened {{ $cashSession->opened_at->format('d M Y, h:i A') }} with {{ config('kafe.currency') }}{{ number_format((float) $cashSession->opening_cash, 2) }}.</p>
                <form method="POST" action="{{ route('admin.accounting.cash-sessions.close', $cashSession) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')
                    <div><label class="block text-xs font-medium text-gray-600">Counted closing cash</label><input name="actual_closing_cash" type="number" min="0" step="0.01" required class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                    <div><label class="block text-xs font-medium text-gray-600">Notes</label><textarea name="closing_notes" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea></div>
                    <button class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white">Close cash session</button>
                </form>
            @else
                <p class="mt-2 text-sm text-gray-600">No cash session is open.</p>
                <form method="POST" action="{{ route('admin.accounting.cash-sessions.store') }}" class="mt-4 flex gap-2">
                    @csrf
                    <input name="opening_cash" type="number" min="0" step="0.01" required placeholder="Opening cash" class="min-w-0 flex-1 rounded-md border-gray-300 text-sm">
                    <button class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white">Open session</button>
                </form>
            @endif
        </section>

        <section class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Record an expense</h2>
            <form method="POST" action="{{ route('admin.accounting.expenses.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <input name="expense_date" type="date" value="{{ now()->toDateString() }}" required class="rounded-md border-gray-300 text-sm">
                <input name="amount" type="number" min="0.01" step="0.01" required placeholder="Amount" class="rounded-md border-gray-300 text-sm">
                <select name="expense_category_id" class="rounded-md border-gray-300 text-sm">
                    <option value="">No category</option>
                    @foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                </select>
                <select name="payment_method" required class="rounded-md border-gray-300 text-sm">
                    @foreach(['cash', 'upi', 'card', 'other'] as $method)<option value="{{ $method }}">{{ strtoupper($method) }}</option>@endforeach
                </select>
                <input name="vendor" maxlength="120" placeholder="Vendor" class="rounded-md border-gray-300 text-sm">
                <input name="reference" maxlength="120" placeholder="Reference" class="rounded-md border-gray-300 text-sm">
                <textarea name="notes" rows="2" maxlength="1000" placeholder="Notes" class="rounded-md border-gray-300 text-sm sm:col-span-2"></textarea>
                <button class="rounded-md bg-kafe-800 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Save expense</button>
            </form>
            <form method="POST" action="{{ route('admin.accounting.categories.store') }}" class="mt-4 flex gap-2 border-t pt-4">
                @csrf
                <input name="name" required maxlength="120" placeholder="New expense category" class="min-w-0 flex-1 rounded-md border-gray-300 text-sm">
                <button class="rounded-md border border-kafe-200 px-3 py-2 text-xs font-semibold text-kafe-800">Add category</button>
            </form>
        </section>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Payments in period</h2>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($payments as $payment)
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-2"><span>{{ $payment->paid_at->format('d M, h:i A') }} · {{ $payment->order->public_ref }} · {{ strtoupper($payment->method) }}</span><span>{{ config('kafe.currency') }}{{ number_format((float) $payment->amount, 2) }}</span></div>
                @empty<p class="text-gray-500">No recorded payments for this period.</p>@endforelse
            </div>
        </section>
        <section class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Expenses in period</h2>
            <div class="mt-3 space-y-2 text-sm">
                @forelse($expenses as $expense)
                    <div class="flex justify-between gap-3 border-b border-gray-100 pb-2"><span>{{ $expense->expense_date->format('d M Y') }} · {{ $expense->category?->name ?? $expense->vendor ?? 'Expense' }}</span><span>{{ config('kafe.currency') }}{{ number_format((float) $expense->amount, 2) }}</span></div>
                @empty<p class="text-gray-500">No expenses for this period.</p>@endforelse
            </div>
        </section>
    </div>
</div>
@endsection
