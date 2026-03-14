<?php

namespace App\Http\Controllers;

use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Nette\Utils\Json;

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
   

    public function details()
    {
        $data = $this->purchaseService->details(request()->all());

        return response()->json([
            'status'  => true,
            'message' => 'Purchase details retrieved successfully',
            'data'    => $data
        ]);
    }
}
