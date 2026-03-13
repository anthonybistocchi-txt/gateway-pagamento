<?php

namespace App\Repositories;

use App\Models\Gateway;

class GatewayRepositoryConfiguration implements GatewayConfigurationRepositoryInterface
{
    public function activate(int $gatewayId): bool
    {
        return Gateway::where('id', $gatewayId)->update(['is_active' => 1]);
    }

    public function deactivate(int $gatewayId): bool
    {
        return Gateway::where('id', $gatewayId)->update(['is_active' => 0]);
    }

    public function updatePriority(int $gatewayId, int $priority): bool
    {
        return Gateway::where('id', $gatewayId)->update(['priority' => $priority]);
    }

    public function getActivesGatewaysOrderByPriority()
    {
        return Gateway::select(['id', 'priority'])->where('is_active', 1)->orderBy('priority')->get();
    }
}