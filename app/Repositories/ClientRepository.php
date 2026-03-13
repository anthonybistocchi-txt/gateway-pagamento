<?php

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;

class ClientRepository implements ClientRepositoryInterface
{
    public function getClientById(int $clientId): Client
    {
        return Client::findOrFail($clientId);
    }
}