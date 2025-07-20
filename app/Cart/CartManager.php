<?php

namespace App\Cart;

class CartManager
{
    private string $sessionKey = 'shopping_cart';

    public function __construct()
    {
        $this->startSession();
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
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'quantity' => $itemData['quantity'],
            ];
        }

        $_SESSION[$this->sessionKey] = $cart;
        return $cart[$key];
    }

    public function updateCart(array $cart): void
    {
        $_SESSION[$this->sessionKey] = $cart;
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

    private function generateItemKey(int $productId, ?int $variationId): string
    {
        return $productId . '_' . ($variationId ?? 'base');
    }
}