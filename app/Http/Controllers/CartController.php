<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateShippingRequest;
use App\Http\Requests\UpdateCartItemRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected \App\Cart\CartManager $cartManager) {}

    public function index()
    {
        $cartData = $this->cartManager->getCartWithShipping(session('shipping_cep'));

        return view('cart.index', [
            'cartItems' => $cartData['items'],
            'cartTotal' => $cartData['subtotal'],
            'shippingCost' => $cartData['shipping'],
            'freeShippingThreshold' => $cartData['free_shipping_threshold'],
            'missingForFreeShipping' => $cartData['missing_for_free_shipping']
        ]);
    }

    public function calculateShipping(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|max:10'
        ]);

        $cep = $request->input('cep');
        
        try {
            $addressData = $this->cartManager->searchAddressByZipCode(str_replace('-', '', $cep));
            
            if ($addressData && !isset($addressData['erro'])) {
                $address = sprintf(
                    '%s, %s, %s/%s',
                    $addressData['logradouro'],
                    $addressData['bairro'],
                    $addressData['localidade'],
                    $addressData['uf']
                );

                session([
                    'shipping_cep' => $cep,
                    'shipping_address' => $address
                ]);

                return redirect()->route('cart.index')
                    ->with('success', 'Frete calculado com sucesso!');
            }

            return redirect()->route('cart.index')
                ->with('error', 'CEP não é válido ou não encontrado');

        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Erro ao calcular frete: ' . $e->getMessage());
        }
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

    public function clearCart()
    {
        try {
            $this->cartManager->clearCart();
            return redirect()->route('cart.index')
                ->with('success', 'Carrinho esvaziado com sucesso');
        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', $e->getMessage());
        }
    }
}