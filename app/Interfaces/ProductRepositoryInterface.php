<?php

namespace App\Interfaces;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function getProductPrice($productId): Product;
    public function getProducts(): Collection;
    public function getProductById($id): Product;
    public function createProduct($data): bool;
    public function updateProduct($data): bool;
    public function deleteProduct($id): bool;
}