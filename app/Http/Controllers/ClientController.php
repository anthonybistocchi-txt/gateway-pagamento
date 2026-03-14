<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientIdRequest;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function __construct(protected ClientService $clientService){}
    
    public function index(): JsonResponse
    {
        $data = $this->clientService->getClients();
    
        return response()->json([
            'status'  => true,
            'message' => 'Clients retrieved successfully',
            'data'    => $data
        ]);
    }

    public function details(ClientIdRequest $request): JsonResponse
    {
        $data = $this->clientService->getClientDetails($request->validated()['id']);

        return response()->json([
            'status'  => true,
            'message' => 'Client details retrieved successfully',
            'data'    => $data
        ]);
    }

}
