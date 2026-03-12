<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function getProductAndPrice($productId): array
    {
        $product = Product::findOrFail($productId);

        return [
            'product'  => $product,
            'amount'   => $product->amount,
        ];
    }
}