<?php

namespace App\Http\Controllers;

use App\Services\Gateways\GatewayConfigurationService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    public function __construct(
        protected GatewayConfigurationService $gatewayConfigurationService,
    ){}

    public function activate($id)
    {
        $this->gatewayConfigurationService->activateGateway($id);

        return response()->json([
            'status'  => true,
            'message' => "Gateway $id activated successfully."
        ]);
    }

    public function deactivate($id)
    {
        $this->gatewayConfigurationService->deactivateGateway($id);

        return response()->json([
            'status'  => true,
            'message' => "Gateway $id deactivated successfully."
        ]);
    }

    public function updatePriority(Request $request, $id)
    {
        $priority = $request->input('priority');

        $this->gatewayConfigurationService->updateGatewayPriority($id, $priority);

        return response()->json([
            'status'  => true,
            'message' => "Gateway $id priority updated to priority $priority successfully."
        ]);
    }
}
