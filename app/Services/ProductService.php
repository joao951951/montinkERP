<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Variation;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'price' => $data['price'],
                'active' => $data['active'] ?? true
            ]);

            $this->syncInventory($product, $data);

            return $product;
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'name' => $data['name'],
                'price' => $data['price'],
                'active' => $data['active'] ?? true
            ]);

            $this->syncInventory($product, $data);

            return $product;
        });
    }

    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->variations()->delete();
            $product->inventory()->delete();
            $product->delete();
        });
    }

    private function syncInventory(Product $product, array $data): void
    {
        $this->removeMissingVariations($product, $data['variations'] ?? []);
        $this->syncVariations($product, $data['variations'] ?? []);
        $this->syncBaseInventory($product, $data);
    }

    /**
     * Get the list of products
     */
    public function getProductList(array $filters = [])
    {
        return Product::with(['inventory', 'variations.inventory'])
            ->withSum('inventory', 'quantity')
            ->when($filters['search'] ?? null, function($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Remove variations that are no longer in the form
     */
    private function removeMissingVariations(Product $product, array $variationsData)
    {
        $existingVariationIds = collect($variationsData)
            ->filter(fn($v) => isset($v['id']))
            ->pluck('id')
            ->toArray();

        $product->variations()
            ->whereNotIn('id', $existingVariationIds)
            ->delete();
    }

    /**
     * Sync the product's base inventory
     */
    private function syncBaseInventory(Product $product, array $validated)
    {
        if (empty($validated['variations'])) {
            $product->inventory()->updateOrCreate(
                ['product_id' => $product->id],
                ['quantity' => $validated['quantity']]
            );
        }
    }
    /**
     * Sync product variations
     */
    private function syncVariations(Product $product, array $variationsData): void
    {
        foreach ($variationsData as $variationData) {
            $this->updateOrCreateVariation($product, $variationData);
        }
    }

    /**
     * Update or create a variation with your inventory
     */
    private function updateOrCreateVariation(Product $product, array $variationData): void
    {
        $variation = isset($variationData['id']) 
            ? $this->updateExistingVariation($variationData)
            : $this->createNewVariation($product, $variationData);

        $this->syncVariationInventory($product, $variation, $variationData['quantity']);
    }

    /**
     * Update an existing variation
     */
    private function updateExistingVariation(array $variationData): Variation
    {
        $variation = Variation::findOrFail($variationData['id']);
        $variation->update([
            'name' => $variationData['name'],
            'price_adjustment' => $variationData['price_adjustment'] ?? 0
        ]);
        return $variation;
    }

    /**
     * Creates a new variation
     */
    private function createNewVariation(Product $product, array $variationData): Variation
    {
        return Variation::create([
            'product_id' => $product->id,
            'name' => $variationData['name'],
            'price_adjustment' => $variationData['price_adjustment'] ?? 0
        ]);
    }

    /**
     * Sync inventory for a variation
     */
    private function syncVariationInventory(Product $product, Variation $variation, int $quantity): void
    {
        $variation->inventory()->updateOrCreate(
            ['product_id' => $product->id, 'variation_id' => $variation->id],
            ['quantity' => $quantity]
        );
    }
}