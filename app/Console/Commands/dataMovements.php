<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
use App\Data\LocalDataQuerys;
use App\Services\PaliServices;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class dataMovements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-movements';

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

            $ultimoRegistro = Bitacora::where('tipo', 'Movimiento')->where('estado', true)->latest()->first();
            $fecha = $ultimoRegistro ? $ultimoRegistro->created_at : Carbon::now();

            $data = new LocalDataQuerys();
            $data = $data->movements($fecha);

            $paliService = new PaliServices();

            foreach ($data as $movement) {
                $pw = $paliService->sendCreditsMovements($movement);
                $this->info(json_encode($pw));
            }

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Carga de movimientos completa. ".count($data)." movimientos tuvieron efecto.";
            $bitacora->tipo = "Movimiento";
            $bitacora->estado = true;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Carga de movimientos completa.");

            return true;

        } catch (\Throwable $th) {

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Error en la carga de movimientos.";
            $bitacora->tipo = "Movimiento";
            $bitacora->estado = false;
            $bitacora->created_at = date('Y-m-d H:i:s');
            $bitacora->save();

            $this->info("Error al cargar movimientos {$th}");
            return false;
        }
    }
}
