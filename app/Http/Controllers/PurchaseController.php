<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseDetailsRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService){}
    public function index():JsonResponse
    {
        $data = $this->purchaseService->getAll();

        return response()->json([
            'status'  => true,
            'message' => 'Purchases retrieved successfully',
            'data'    => $data
        ]);
    }
   

    public function details(PurchaseDetailsRequest $request):JsonResponse
    {
        $data = $this->purchaseService->details($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Purchase details retrieved successfully',
            'data'    => $data
        ]);
    }
}
