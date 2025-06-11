<?php

namespace App\Http\Controllers;

use App\Collections\ClientCollection;
use App\Services\ClientService;
use App\Services\ResponseService;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    public function __construct(private readonly ClientService $clientService) {}

    public function index()
    {
        $clients = $this->clientService->getAllClients();
        return ResponseService::success(ClientCollection::make($clients), Response::HTTP_OK);
    }
}
