<?php

namespace App\Interfaces;

use App\Models\Gateway;

interface GatewayConfigurationRepositoryInterface
{
    public function activate(int $gatewayId): bool;
    public function deactivate(int $gatewayId): bool;
    public function updatePriority(int $gatewayId, int $priority): bool;
    public function getGatewayById(int $gatewayId);
    public function getGatewayByPriority(int $priority): ?Gateway;
    public function getActivesGatewaysOrderByPriority();
}