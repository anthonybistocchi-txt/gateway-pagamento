<?php

namespace App\Interfaces;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface
{
    public function getClientById(int $clientId): Client;
    public function getAllClients(): Collection;
    public function getClientDetails(array $data): Client;
}