<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Http\Requests\AddToCartRequest;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function show(): JsonResponse
    {
        return response()->json([
            'cart' => $this->cartService->getCart(),
            'total' => $this->cartService->getTotal()
        ]);
    }

    public function addItem(AddToCartRequest $request): JsonResponse
    {
        $item = $this->cartService->addItem(
            $request->validated()
        );

        return response()->json([
            'message' => 'Item adicionado ao carrinho',
            'item' => $item,
            'cart_total' => $this->cartService->getTotal()
        ], 201);
    }

    public function removeItem(string $itemId): JsonResponse
    {
        $this->cartService->removeItem($itemId);

        return response()->json([
            'message' => 'Item removido do carrinho',
            'cart_total' => $this->cartService->getTotal()
        ]);
    }
}