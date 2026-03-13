<?php

namespace App\Repositories;

use App\Models\Gateway;

interface GatewayConfigurationRepositoryInterface
{
    public function activate(int $gatewayId): bool;
    public function deactivate(int $gatewayId): bool;
    public function updatePriority(int $gatewayId, int $priority): bool;
}