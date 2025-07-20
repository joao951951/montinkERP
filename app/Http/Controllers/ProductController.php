<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService ){}

    public function index(Request $request)
    {
        $products = $this->productService->getProductList($request->all());

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        try {
            $this->productService->createProduct($validated);
            return redirect()->route('products.index')
                ->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar produto: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        return view('products.form', [
            'product' => $product->load(['variations.inventory', 'inventory'])
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate($this->validationRules($product));

        try {
            $this->productService->updateProduct($product, $validated);
            return redirect()->route('products.index')
                ->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->productService->deleteProduct($product);
            return redirect()->route('products.index')
                ->with('success', 'Produto removido com sucesso!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao remover produto: ' . $e->getMessage());
        }
    }

    private function validationRules(?Product $product = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'active' => ['boolean'],
            'quantity' => ['required_without:variations', 'integer', 'min:0'],
            'variations' => ['nullable', 'array'],
            'variations.*.name' => ['required_with:variations', 'string', 'max:255'],
            'variations.*.price_adjustment' => ['nullable', 'numeric', 'min:0'],
            'variations.*.quantity' => ['required_with:variations', 'integer', 'min:0'],
        ];

        $rules['name'][] = $product 
            ? Rule::unique('products')->ignore($product->id)
            : 'unique:products';

        return $rules;
    }
}