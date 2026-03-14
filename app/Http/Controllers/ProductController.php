<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductIdRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService ){}
    public function index():JsonResponse
    {
        $data = $this->productService->getProducts();

        return response()->json([
            'status'  => true,
            'message' => 'Products retrieved successfully',
            'data'    => $data
        ]);
    }

    public function show(ProductIdRequest $request): JsonResponse
    {
        $data = $this->productService->getProductsById($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Product retrieved successfully',
            'data'    => $data
        ]);
    }

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $this->productService->storeProduct($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
        ]);
    }

    public function update(ProductUpdateRequest $request, $id): JsonResponse
    {
        $this->productService->updateProduct($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Product updated successfully',
        ]);
    }

    public function destroy(ProductIdRequest $request): JsonResponse
    {
        $this->productService->deleteProduct($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}
