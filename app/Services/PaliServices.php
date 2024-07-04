<?php

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Bitacora;

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
                '/api/upload_clients', json_encode(["data"=>$data])
            )->json();

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Response:".json_encode($response);
            $bitacora->estado = true;
            $bitacora->save();

            // return $response;
        } catch (GuzzleException|\Exception $e) {
            Log::error($e->getMessage());

            $bitacora = new Bitacora();
            $bitacora->descripcion = "erro service:".$e->getMessage();
            $bitacora->estado = false;
            $bitacora->save();

            // return ['msg' => $e->getMessage()];
        }
    }

    public function sendCredits($data)
    {
        try {
            $response = $this->client->post(
                'api/upload_data', $data
            )->json();
            return $response;
        } catch (GuzzleException|\Exception $e) {
            Log::error($e->getMessage());
            return ['msg' => $e->getMessage()];
        }
    }

}
