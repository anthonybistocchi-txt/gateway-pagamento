<?php

namespace App\Services\Gateways;

use App\Repositories\GatewayConfigurationRepositoryInterface;

class GatewayConfigurationService
{
    public function __construct(
        protected GatewayConfigurationRepositoryInterface $gatewayRepository,
        protected BearerTokenGatewayService               $bearerTokenGatewayService,
        protected HeaderAuthGatewayService                $headerAuthGatewayService
    ){}

    public function activateGateway($id)
    {
        return $this->gatewayRepository->activate($id);
    }

    public function deactivateGateway($id)
    {
        return $this->gatewayRepository->deactivate($id);
    }

    public function updateGatewayPriority($id, $priority)
    {
        return $this->gatewayRepository->updatePriority($id, $priority);
    }

}