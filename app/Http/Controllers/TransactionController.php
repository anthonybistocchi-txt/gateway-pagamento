<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\TransactionRefundRequest;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService){}
    
    public function store(TransactionStoreRequest $request): JsonResponse
    {
        $this->transactionService->store($request->validated());

        return response()->json([
                'status'  => true,
                'message' => 'Transaction created successfully.'
        ], 201);
    }

    public function refund(TransactionRefundRequest $request): JsonResponse
    {
        $this->transactionService->processRefund($request->validated()['id']);

        return response()->json([
                'status'  => true,
                'message' => 'Refund processed successfully.'
        ], 200);
    }
}
