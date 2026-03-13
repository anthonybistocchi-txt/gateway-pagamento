<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService){}
    
    public function store(PurchaseRequest $request): JsonResponse
    {
        $this->purchaseService->store($request->validated());

        return response()->json([
                'status'  => true,
                'message' => 'Purchase created successfully.'
        ], 201);
    }

    public function refund($id): JsonResponse
    {
        $this->purchaseService->processRefund($id);

        return response()->json([
                'status'  => true,
                'message' => 'Refund processed successfully.'
        ], 200);
    }
}
