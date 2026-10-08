<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;

class WhatsAppMessageBuilder
{
    public function orderUrl(Order $order): string
    {
        return $this->buildUrl($this->formatOrder($order));
    }

    public function bookingUrl(Booking $booking): string
    {
        return $this->buildUrl($this->formatBooking($booking));
    }

    public function buildUrl(string $message): string
    {
        $number = config('kafe.whatsapp_number');
        $encoded = rawurlencode($message);

        return "https://wa.me/{$number}?text={$encoded}";
    }

    /** Open a chat with the owner (optional starter message). */
    public function directChatUrl(?string $message = null): string
    {
        $message ??= 'Hi '.config('kafe.name').'! I would like to place an order.';

        return $this->buildUrl($message);
    }

    public function formatOrder(Order $order): string
    {
        $order->loadMissing('items.menuItem');

        $lines = [
            '🍽 *New order — '.config('kafe.name').'*',
            '',
            "Order #: {$order->id}",
            "Customer: {$order->customer_name}",
            "Phone: {$order->customer_phone}",
        ];

        if ($order->customer_note) {
            $lines[] = "Note: {$order->customer_note}";
        }

        $lines[] = '';
        $lines[] = '*Items:*';

        foreach ($order->items as $item) {
            $name = $item->menuItem?->name ?? 'Item';
            $lines[] = "• {$name} × {$item->quantity} — ".config('kafe.currency').number_format($item->line_total, 2);
        }

        $lines[] = '';
        $lines[] = '*Total: '.config('kafe.currency').number_format($order->total_amount, 2).'*';

        return implode("\n", $lines);
    }

    public function formatBooking(Booking $booking): string
    {
        $partyLabel = config("kafe.party_types.{$booking->party_type}", $booking->party_type);

        $lines = [
            '🎉 *Party booking — '.config('kafe.name').'*',
            '',
            "Name: {$booking->customer_name}",
            "Phone: {$booking->customer_phone}",
            "Occasion: {$partyLabel}",
            "Date: {$booking->event_date->format('d M Y')}",
            "Time: {$booking->event_time}",
            "Guests: {$booking->guest_count}",
        ];

        if ($booking->message) {
            $lines[] = '';
            $lines[] = "Message: {$booking->message}";
        }

        return implode("\n", $lines);
    }
}
