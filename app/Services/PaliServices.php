<?php

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaliServices
{
    private $client;

    public function __construct()
    {
        $this->client = Http::baseUrl(env('BASE_URL_PALI'))
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function sendClients($data)
    {
        try {
            $response = $this->client->post(
                '/api/upload_clients', ["data"=>$data]
            )->json();
            return $response;
        } catch (GuzzleException|\Exception $e) {
            Log::error($e->getMessage());
            return ['msg' => $e->getMessage()];
        }
    }

    public function sendCredits($data)
    {
        try {
            $response = $this->client->post(
                '/api//api/upload_loans', ["data"=>$data]
            )->json();
            return $response;
        } catch (GuzzleException|\Exception $e) {
            Log::error($e->getMessage());
            return ['msg' => $e->getMessage()];
        }
    }

    public function sendCreditsMovements($data)
    {
        try {
            $response = $this->client->post(
                '/api/upload_movements', ["data"=>$data]
            )->json();
            return $response;
        } catch (GuzzleException|\Exception $e) {
            Log::error($e->getMessage());
            return ['msg' => $e->getMessage()];
        }
    }

}
