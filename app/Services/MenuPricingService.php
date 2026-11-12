<?php

namespace App\Services;

use App\Models\MenuItem;

class MenuPricingService
{
    /**
     * @return array{price_cents: int, discount_cents: int, offer_id: ?int}
     */
    public function bestPrice(MenuItem $item, int $regularPriceCents): array
    {
        $bestPriceCents = $regularPriceCents;
        $discountCents = 0;
        $offerId = null;
        $now = now();

        foreach ($item->todaySpecials as $special) {
            if ($special->isAvailableAt($now) && $special->special_price !== null) {
                $specialPrice = max(0, $this->toCents($special->special_price));
                if ($specialPrice < $bestPriceCents) {
                    $bestPriceCents = $specialPrice;
                    $discountCents = $regularPriceCents - $specialPrice;
                    $offerId = null;
                }
            }
        }

        foreach ($item->offers as $offer) {
            if (! $offer->isAvailableAt($now)) {
                continue;
            }

            $value = $this->toCents($offer->discount_value);
            $candidateDiscount = match ($offer->discount_type) {
                'percentage' => intdiv($regularPriceCents * min($value, 10000), 10000),
                'fixed' => min($value, $regularPriceCents),
                default => 0,
            };
            $candidatePrice = $regularPriceCents - $candidateDiscount;

            if ($candidatePrice < $bestPriceCents) {
                $bestPriceCents = $candidatePrice;
                $discountCents = $candidateDiscount;
                $offerId = $offer->id;
            }
        }

        return [
            'price_cents' => $bestPriceCents,
            'discount_cents' => $discountCents,
            'offer_id' => $offerId,
        ];
    }

    public function toCents(string|int $amount): int
    {
        if (! preg_match('/^(-?)(\d+)(?:\.(\d{1,2}))?$/', (string) $amount, $matches)) {
            throw new \InvalidArgumentException('Prices must be decimal amounts with at most two fractional digits.');
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
