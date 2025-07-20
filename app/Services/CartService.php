<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Variation;
use App\Exceptions\CartException;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const SESSION_KEY = 'shopping_cart';

    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function getTotal(): float
    {
        return array_reduce($this->getCart(), function($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function addItem(array $data): array
    {
        $product = Product::findOrFail($data['product_id']);
        $variation = $data['variation_id'] 
            ? Variation::where('id', $data['variation_id'])
                ->where('product_id', $product->id)
                ->firstOrFail()
            : null;

        $this->validateStock($product, $data['quantity'], $variation);

        $cartItem = (object) [
            'product'=> $product,
            'variation'=> $variation,
            'quantity'=> $data['quantity']
        ];

        $cart = $this->getCart();
        $key = $this->generateItemKey($cartItem);

        $cart[$key] = $this->formatItem($cartItem);
        Session::put(self::SESSION_KEY, $cart);

        return $cart[$key];
    }

    public function removeItem(string $itemId): void
    {
        $cart = $this->getCart();

        if (!array_key_exists($itemId, $cart)) {
            throw new CartException('Item não encontrado no carrinho', 404);
        }

        unset($cart[$itemId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    private function validateStock(Product $product, int $quantity, ?Variation $variation): void
    {
        $available = $variation 
            ? ($variation->inventory->quantity ?? 0)
            : ($product->inventory->quantity ?? 0);

        if ($quantity > $available) {
            throw new CartException("Quantidade indisponível em estoque ($available disponíveis)");
        }
    }

    private function generateItemKey(Object $item): string
    {
        return $item->product->id.'_'.($item->variation?->id ?? 'base');
    }

    private function formatItem($item): array
    {
        return [
            'product_id' => $item->product->id,
            'variation_id' => $item->variation?->id,
            'name' => $item->product->name,
            'variation_name' => $item->variation?->name,
            'price' => $this->calculatePrice($item),
            'quantity' => $item->quantity,
            'image' => $item->product->image,
            'max_available' => $this->getAvailableStock($item)
        ];
    }

    private function calculatePrice(Object $item): float
    {
        $price = $item->product->price;
        
        if ($item->variation) {
            $price += $item->variation->price_adjustment;
        }
        
        return $price;
    }

    private function getAvailableStock(Object $item): int
    {
        return $item->variation 
            ? ($item->variation->inventory->quantity ?? 0)
            : ($item->product->inventory->quantity ?? 0);
    }
}