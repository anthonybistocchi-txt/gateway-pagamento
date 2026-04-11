<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\TransactionRefundRequest;
use App\Http\Requests\Transaction\TransactionStoreRequest;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService) {}

    public function store(TransactionStoreRequest $request): JsonResponse
    {
        $result = $this->transactionService->store($request->validated());

        return response()->json($result->payload, $result->httpStatus);
    }

    public function refund(TransactionRefundRequest $request): JsonResponse
    {
        $result = $this->transactionService->processRefund($request->validated()['id']);

        return response()->json($result->payload, $result->httpStatus);
    }
}
