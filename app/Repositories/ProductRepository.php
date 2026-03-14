<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getProductPrice($productId): Product
    {
       return Product::select('amount')->findOrFail($productId);

    }

    public function getProducts(): Collection
    {
        return Product::all();
    }

    public function getProductById($id): Product
    {
        return Product::findOrFail($id);
    }   

    public function createProduct($data): bool
    {
        return Product::create($data) ? true : false;
    }

    public function updateProduct($data): bool
    {
        $product = Product::findOrFail($data['id']);
        return $product->update($data) ? true : false;
    }

    public function deleteProduct($id): bool
    {
        $product = Product::findOrFail($id);
        return $product->delete() ? true : false;
    }

}