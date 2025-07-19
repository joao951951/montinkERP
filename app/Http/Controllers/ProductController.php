<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variation;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['inventory', 'variations.inventory'])
            ->orderBy('name')
            ->paginate(10);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request);

        return DB::transaction(function () use ($validatedData) {
            try {
                $product = $this->createProduct($validatedData);
                
                $this->handleInventory($product, $validatedData);

                return redirect()
                    ->route('products.index')
                    ->with('success', 'Produto cadastrado com sucesso!');

            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Erro ao cadastrar produto: ' . $e->getMessage());
            }
        });
    }

    /**
     * Validates request
     */
    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean',
            'quantity' => 'required|integer|min:0',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required_with:variations|string|max:255',
            'variations.*.price_adjustment' => 'nullable|numeric|min:0',
            'variations.*.quantity' => 'required_with:variations|integer|min:0'
        ]);
    }

    /**
     * Create the product
     */
    protected function createProduct(array $validatedData): Product
    {
        return Product::create([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'active' => $validatedData['active'] ?? true
        ]);
    }

    /**
     * Gerencia o estoque do produto e variações
     */
    protected function handleInventory(Product $product, array $validatedData): void
    {
        if (empty($validatedData['variations'])) {
            $this->createBaseInventory($product, $validatedData['quantity']);
            return;
        }

        foreach ($validatedData['variations'] as $variationData) {
            $this->createProductVariation($product, $variationData);
        }
    }

    /**
     * Cria o estoque base para produtos sem variações
     */
    protected function createBaseInventory(Product $product, int $quantity): void
    {
        Inventory::create([
            'product_id' => $product->id,
            'quantity' => $quantity
        ]);
    }

    /**
     * Cria uma variação do produto com seu estoque
     */
    protected function createProductVariation(Product $product, array $variationData): void
    {
        $variation = Variation::create([
            'product_id' => $product->id,
            'name' => $variationData['name'],
            'price_adjustment' => $variationData['price_adjustment'] ?? 0
        ]);

        Inventory::create([
            'product_id' => $product->id,
            'variation_id' => $variation->id,
            'quantity' => $variationData['quantity']
        ]);
    }

}