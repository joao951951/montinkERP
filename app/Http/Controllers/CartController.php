<?php

namespace App\Http\Controllers;

use App\Cart\CartManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCartItemRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartManager;

    public function __construct(CartManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    public function index()
    {
        $cartItems = $this->cartManager->getCart();
        $cartTotal = $this->cartManager->getTotal();
        
        // Calcula o frete conforme as regras especificadas
        if ($cartTotal >= 200) {
            $shippingCost = 0;
        } elseif ($cartTotal >= 52 && $cartTotal <= 166.59) {
            $shippingCost = 15;
        } else {
            $shippingCost = 20;
        }

        return view('cart.index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'shippingCost' => $shippingCost
        ]);
    }

    public function remove($itemKey)
    {
        $cart = $this->cartManager->getCart();
        
        if (!array_key_exists($itemKey, $cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Item não encontrado no carrinho');
        }

        $this->cartManager->removeItem($itemKey);

        return redirect()->route('cart.index')
            ->with('success', 'Produto removido do carrinho');
    }

    public function updateCartItem(UpdateCartItemRequest $request, $itemKey)
    {
        try {
            $validated = $request->validated();
            $cart = $this->cartManager->getCart();

            if (!array_key_exists($itemKey, $cart)) {
                throw new \Exception('Item não encontrado no carrinho');
            }

            if (isset($cart[$itemKey]['max_available']) && 
                $validated['quantity'] > $cart[$itemKey]['max_available']) {
                throw new \Exception('Quantidade solicitada excede o estoque disponível');
            }

            $cart[$itemKey]['quantity'] = $validated['quantity'];
            $this->cartManager->updateCart($cart);

            return redirect()->route('cart.index')
                ->with('success', 'Quantidade atualizada com sucesso');

        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', $e->getMessage());
        }
    }

    public function clearCart(Request $request)
    {
        dd($request->all());
        try {
            if ($this->cartManager->clearCart()) {
                return redirect()->route('cart.index')
                    ->with('success', 'Carrinho esvaziado com sucesso');
            }

            throw new \Exception('Não foi possível limpar o carrinho');

        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', $e->getMessage());
        }
    }
}