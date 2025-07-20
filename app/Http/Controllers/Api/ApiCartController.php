<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Cart\CartManager;
use App\Http\Requests\Api\AddToCartRequest;
use App\Http\Requests\Api\UpdateCartRequest;
use App\Models\Product;
use App\Models\Variation;
use Illuminate\Http\JsonResponse;

class ApiCartController extends Controller
{
    public function __construct(private CartManager $cart) {}

    public function getCartItems(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $this->cart->getCart(),
                'meta' => [
                    'total_items' => count($this->cart->getCart()),
                    'cart_total' => $this->cart->getTotal(),
                    'currency' => 'BRL'
                ]
            ]
        ]);
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        $product = Product::findOrFail($request->product_id);
        $variation = $request->variation_id 
            ? Variation::findOrFail($request->variation_id)
            : null;

        $item = $this->cart->addItem([
            'product_id' => $product->id,
            'variation_id' => $variation?->id,
            'variation_name' => $variation?->name,
            'name' => $product->name,
            'price' => $this->calculatePrice($product, $variation),
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item adicionado ao carrinho',
            'data' => [
                'item' => $item,
                'cart_summary' => [
                    'total_items' => count($this->cart->getCart()),
                    'cart_total' => $this->cart->getTotal()
                ]
            ]
        ], 201);
    }

    public function update(UpdateCartRequest $request, string $itemKey): JsonResponse
    {
        $cart = $this->cart->getCart();
        
        if (!isset($cart[$itemKey])) {
            return $this->notFoundResponse();
        }

        $cart[$itemKey]['quantity'] = $request->quantity;
        $this->cart->updateCart($cart);

        return response()->json([
            'success' => true,
            'message' => 'Item atualizado',
            'data' => [
                'item' => $cart[$itemKey],
                'new_quantity' => $request->quantity
            ]
        ]);
    }

    public function destroy(string $itemKey): JsonResponse
    {
        $cart = $this->cart->getCart();
        
        if (!isset($cart[$itemKey])) {
            return $this->notFoundResponse();
        }

        $this->cart->removeItem($itemKey);

        return response()->json([
            'success' => true,
            'message' => 'Item removido do carrinho',
            'data' => [
                'removed_item_key' => $itemKey,
                'remaining_items' => count($this->cart->getCart())
            ]
        ]);
    }

    public function clear(): JsonResponse
    {
        $this->cart->clearCart();

        return response()->json([
            'success' => true,
            'message' => 'Carrinho esvaziado',
            'data' => []
        ]);
    }

    private function calculatePrice(Product $product, ?Variation $variation): float
    {
        return $product->price + ($variation?->price_adjustment ?? 0);
    }

    private function notFoundResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Item não encontrado no carrinho'
        ], 404);
    }
}