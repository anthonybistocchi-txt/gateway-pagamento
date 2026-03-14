<?php

namespace App\Services;

use App\Interfaces\ClientRepositoryInterface;

class ClientService
{
    public function __construct(protected ClientRepositoryInterface $clientRepository){}
    
    public function getClients()
    {
    
        return $this->clientRepository->getAllClients();
    }

    public function getClientDetails($id)
    {
        return $this->clientRepository->getClientDetails(['id' => $id]);
    }

}