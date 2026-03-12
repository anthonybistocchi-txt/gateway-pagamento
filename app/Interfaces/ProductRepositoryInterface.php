<?php

namespace App\Interfaces;

interface ProductRepositoryInterface
{
    public function getProductAndPrice($productId): array;
}