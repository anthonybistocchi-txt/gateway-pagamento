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
        return Client::select(
            'purchases.id as purchase_id',
            'clients.name', 
            'clients.email', 
            'purchases.amount as purchase_amount', 
            'purchases.status as purchase_status',
            'products.name as product_name', 
            )
            ->with(['purchases:id,client_id,product_id,amount,status', 'transactions:id,client_id,gateway_id,amount,status'])
            ->findOrFail($data['id']);
    }

    
}