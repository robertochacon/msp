<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
use App\Data\LocalDataQuerys;
use App\Services\PaliServices;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class dataLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-loans';

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
        set_time_limit(600);

        try {

            $ultimoRegistro = Bitacora::where('tipo', 'Credito')->where('estado', true)->latest()->first();
            $fecha = $ultimoRegistro ? $ultimoRegistro->created_at : Carbon::now();

            $data = new LocalDataQuerys();
            $data_movements = $data->loans_movements($fecha);
            $codigos = [];
            $total_success = 0;
            $total_fails = 0;

            $paliService = new PaliServices();

            foreach ($data_movements as $movement) {

                $loan = $data->loans($movement->no_credito);
                $pw = $paliService->sendCredits($loan);

                if ($pw["status"]) {
                    $total_success = $total_success+1;
                }else{
                    $total_fails = $total_fails+1;
                }

                array_push($codigos, ['codigo'=>$pw['data'], 'estado'=>$pw["status"], 'cambios'=>null]);
                $this->info(json_encode($pw));

            }

            $bitacora = new Bitacora();
            $bitacora->total_completados = $total_success;
            $bitacora->total_fallidos = $total_fails;
            $bitacora->descripcion = "Carga de creditos completa. ".count($codigos)." creditos tuvieron efecto.";
            $bitacora->tipo = "Credito";
            $bitacora->estado = true;
            $bitacora->codes = $codigos;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Carga de creditos completa.");

            return 0;

        } catch (\Throwable $th) {

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Error en la carga de creditos.";
            $bitacora->tipo = "Credito";
            $bitacora->estado = false;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Error al cargar movimientos {$th}");
            return 1;
        }
    }
}
