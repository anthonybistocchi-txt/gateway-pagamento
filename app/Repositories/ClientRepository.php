<?php

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository implements ClientRepositoryInterface
{
    public function getClientById(int $clientId): Client
    {
        return Client::findOrFail($clientId);
    }

    public function getAllClients(): Collection
    {
        return Client::all();
    }

    public function getClientDetails(array $data): Client
    {
        return Client::with([
            'purchases'
        ])->findOrFail($data['id']);
    }

    
}