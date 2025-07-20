<?php

namespace App\Http\Controllers;

use App\Cart\CartManager;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    protected $cartManager;

    public function __construct(CartManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    public function index()
    {
        // dd($this->cartManager->getCart());
        $cartItems = $this->cartManager->getCart();
        $cartTotal = $this->cartManager->getTotal();
        $itemCount = count($cartItems);

        return view('cart.index', [
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'itemCount' => $itemCount
        ]);
    }
}