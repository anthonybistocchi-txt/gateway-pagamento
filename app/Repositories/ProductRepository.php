<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function getProductPrice($productId): Product
    {
       return Product::select('amount')->findOrFail($productId);

    }
}