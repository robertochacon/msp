<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
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

            $ultimoRegistro = Bitacora::where('estado', true)->latest()->first();
            $fecha = $ultimoRegistro ? $ultimoRegistro->created_at : Carbon::now();

            $data = LocalDataQuerys::clients($fecha);

            $paliService = new PaliServices();

            foreach ($data as $client) {
                $clientData = json_encode($client);
                $pw = $paliService->sendClients($client);
                $this->info(json_encode($pw));
            }

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Carga de clientes completa. ".count($data)." clientes tuvieron efecto.";
            $bitacora->estado = true;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Carga de clientes completa.");

            return true;

        } catch (\Throwable $th) {

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Error en la carga de clientes.";
            $bitacora->estado = false;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Error al cargar clientes {$th}");
            return false;
        }
    }
}
