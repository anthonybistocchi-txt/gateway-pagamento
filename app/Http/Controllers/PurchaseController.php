<?php

namespace App\Http\Controllers;

use App\Http\Requests\Purchase\PurchaseRefundRequest;
use App\Http\Requests\Purchase\PurchaseStoreRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService){}
    
    public function store(PurchaseStoreRequest $request): JsonResponse
    {
        $this->purchaseService->store($request->validated());

        return response()->json([
                'status'  => true,
                'message' => 'Purchase created successfully.'
        ], 201);
    }

    public function refund(PurchaseRefundRequest $request): JsonResponse
    {
        $this->purchaseService->processRefund($request->validated()['id']);

        return response()->json([
                'status'  => true,
                'message' => 'Refund processed successfully.'
        ], 200);
    }
}
