<?php

namespace App\Services;

use App\Interfaces\ProductRepositoryInterface;

class ProductService
{
    public function __construct(protected ProductRepositoryInterface $productRepository){}

    public function getProducts()
    {
       return $this->productRepository->getProducts();
    }

    public function getProductsById($id)
    {
        return $this->productRepository->getProductById($id);
    }

    public function storeProduct($data)
    {
        return $this->productRepository->createProduct($data);
    }

    public function updateProduct($data)
    {
        return $this->productRepository->updateProduct($data);
    }

    public function deleteProduct($id)
    {
        return $this->productRepository->deleteProduct($id);
    }
}