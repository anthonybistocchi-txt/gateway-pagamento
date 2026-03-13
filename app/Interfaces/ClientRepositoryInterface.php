<?php

namespace App\Interfaces;

use App\Models\Client;

interface ClientRepositoryInterface
{
    public function getClientById(int $clientId): Client;
}