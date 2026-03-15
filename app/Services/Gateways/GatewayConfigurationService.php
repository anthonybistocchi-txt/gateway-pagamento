<?php

namespace App\Services\Gateways;

use App\Interfaces\GatewayConfigurationRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($data) {

            $gatewayToUpdate = $this->gatewayRepository->getGatewayById($data['id']);

            $oldPriority = $gatewayToUpdate->priority;

            if ($oldPriority === $data['priority']) {
                return true; // No change needed
            }

            $gatewayPriorityExisting = $this->gatewayRepository->getGatewayByPriority($data['priority']);

            if ($gatewayPriorityExisting) {
                $this->gatewayRepository->updatePriority($gatewayPriorityExisting->id, $oldPriority);
            }

            return $this->gatewayRepository->updatePriority($data['id'], $data['priority']);
        });
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