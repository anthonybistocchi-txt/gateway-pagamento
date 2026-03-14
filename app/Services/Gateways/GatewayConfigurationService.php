<?php

namespace App\Services\Gateways;

use App\Interfaces\GatewayConfigurationRepositoryInterface;

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

    public function updateGatewayPriority(array $data)
    {
        return $this->gatewayRepository->updatePriority($data['id'], $data['priority']);
    }

    public function getActivesGatewaysOrderByPriority()
    {
        $activeGateways = $this->gatewayRepository->getActivesGatewaysOrderByPriority();

        if ($activeGateways->isEmpty()) {
            throw new \Exception('No active gateways available.');
        }

        return $activeGateways;
    }

}