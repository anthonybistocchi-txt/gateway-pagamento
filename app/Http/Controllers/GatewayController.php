<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gateway\GatewayActivateAndDeactivateRequest;
use App\Http\Requests\Gateway\GatewayUpdatePriorityRequest;
use App\Services\Gateways\GatewayConfigurationService;

class GatewayController extends Controller
{
    public function __construct(
        protected GatewayConfigurationService $gatewayConfigurationService,
    ){}

    public function activate(GatewayActivateAndDeactivateRequest $request)
    {
        $this->gatewayConfigurationService->activateGateway($request->validated('id'));

        return response()->json([
            'status'  => true,
            'message' => "gateway activated successfully."
        ]);
    }

    public function deactivate(GatewayActivateAndDeactivateRequest $request)
    {
        $this->gatewayConfigurationService->deactivateGateway($request->validated('id'));

        return response()->json([
            'status'  => true,
            'message' => "gateway deactivated successfully."
        ]);
    }

    public function updatePriority(GatewayUpdatePriorityRequest $request)
    {
        $this->gatewayConfigurationService->updateGatewayPriority($request->validated());

        return response()->json([
            'status'  => true,
            'message' => "Gateway priority updated priority successfully."
        ]);
    }
}
