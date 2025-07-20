<?php

namespace App\Services;

class ShippingService
{
    public function calculateShipping(float $cartTotal, ?string $cep = null): float
    {
        if ($cartTotal >= 200) {
            return 0;
        }

        if (empty($cep)) {
            return 20;
        }

        return $this->getShippingCost($cep, $cartTotal);
    }

    protected function getShippingCost(string $cep, float $cartTotal): float
    {
        return $cartTotal >= 52 && $cartTotal <= 166.59 ? 15 : 20;
    }
}