<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
use App\Models\Settings;
use App\Data\LocalDataQuerys;
use App\Services\PaliServices;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class dataClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            $ultimoRegistro = Bitacora::where('tipo', 'Cliente')->where('estado', true)->latest()->first();
            $fecha = $ultimoRegistro ? $ultimoRegistro->created_at : Carbon::now();

            $data = new LocalDataQuerys();
            $data = $data->clients($fecha);
            $codigos = [];

            $paliService = new PaliServices();

            foreach ($data as $client) {

                $client_pw = $paliService->getClient($client->id);

                $changes = [];

                if ($client_pw["status"]) {
                    foreach ($client as $key => $value) {
                        if ($key !== 'fecha_registro' && array_key_exists($key, $client_pw["data"]) && $client_pw["data"][$key] != $value) {
                            $changes[$key] = $client_pw["data"][$key] ." - ".$value;
                        }
                    }
                }

                $pw = $paliService->sendClients($client);
                array_push($codigos, ['codigo'=>$pw['data'], 'estado'=>$pw["status"], 'cambios'=>$changes]);
                $this->info(json_encode($pw));
            }

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Carga de clientes completa. ".count($data)." clientes tuvieron efecto.";
            $bitacora->tipo = "Cliente";
            $bitacora->estado = true;
            $bitacora->codes = $codigos;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Carga de clientes completa.");

            return 0;

        } catch (\Throwable $th) {

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Error en la carga de clientes.";
            $bitacora->tipo = "Cliente";
            $bitacora->estado = false;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Error al cargar clientes {$th}");
            return 1;
        }
    }
}
