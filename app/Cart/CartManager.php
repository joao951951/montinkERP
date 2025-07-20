<?php

namespace App\Cart;

use App\Services\ShippingService;
use App\Services\ViaCepService;

class CartManager
{
    private string $sessionKey = 'shopping_cart';
    private ShippingService $shippingService;
    private ViaCepService $viaCepService;

    public function __construct(ShippingService $shippingService, ViaCepService $viaCepService)
    {
        $this->startSession();
        $this->shippingService = $shippingService;
        $this->viaCepService = $viaCepService;
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getCart(): array
    {
        return $_SESSION[$this->sessionKey] ?? [];
    }

    public function addItem(array $itemData): array
    {
        $cart = $this->getCart();
        $key = $this->generateItemKey($itemData['product_id'], $itemData['variation_id'] ?? null);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $itemData['quantity'];
        } else {
            $cart[$key] = [
                'product_id' => $itemData['product_id'],
                'variation_id' => $itemData['variation_id'] ?? null,
                'variation_name' => $itemData['variation_name'] ?? null,
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'quantity' => $itemData['quantity'],
                'max_available' => $itemData['max_available'] ?? null,
            ];
        }

        $_SESSION[$this->sessionKey] = $cart;
        return $cart[$key];
    }

    public function updateCart(array $cart): void
    {
        $_SESSION[$this->sessionKey] = $cart;
        session_write_close();
    }

    public function removeItem(string $itemKey): void
    {
        $cart = $this->getCart();
        unset($cart[$itemKey]);
        $_SESSION[$this->sessionKey] = $cart;
    }

    public function clearCart(): void
    {
        unset($_SESSION[$this->sessionKey]);
    }

    public function getTotal(): float
    {
        return array_reduce($this->getCart(), function($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function calculateShipping(?string $cep = null): float
    {
        return $this->shippingService->calculateShipping($this->getTotal(), $cep);
    }

    public function getShippingData(?string $cep = null): array
    {
        $shippingCost = $this->calculateShipping($cep);
        $subtotal = $this->getTotal();

        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
            'free_shipping_threshold' => 200,
            'missing_for_free_shipping' => max(0, 200 - $subtotal),
        ];
    }

    public function searchAddressByZipCode(string $cep): ?array
    {
        return $this->viaCepService->getAddresByCEP($cep);
    }

    public function getCartWithShipping(?string $cep = null): array
    {
        $shippingData = $this->getShippingData($cep);

        return [
            'items' => $this->getCart(),
            'subtotal' => $shippingData['subtotal'],
            'shipping' => $shippingData['shipping_cost'],
            'total' => $shippingData['total'],
            'currency' => 'BRL',
            'free_shipping_threshold' => $shippingData['free_shipping_threshold'],
            'missing_for_free_shipping' => $shippingData['missing_for_free_shipping'],
        ];
    }

    private function generateItemKey(int $productId, ?int $variationId): string
    {
        return $productId . '_' . ($variationId ?? 'base');
    }
}