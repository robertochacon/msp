<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
use App\Services\PaliServices;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

            $data = DB::connection('sqlsrv')
            ->select("
                select
                    c.cod_cliente,
                    'X'+c.identificacion,
                    c.nombres,
                    c.apellidos,
                    c.fecha_nac,
                    (select max(t.telefono) from tbl_telefonos t where c.cod_cliente = t.cod_cliente and t.tipo = 'Trabajo' and t.estado <> 'Inactivo') Tel_trab,
                    (select max(t.telefono) from tbl_telefonos t where c.cod_cliente = t.cod_cliente and t.tipo = 'Celular' and t.estado <> 'Inactivo') Celular,
                    (select isnull(max(valor),0) from tbl_estado_resultados e where e.cod_cliente = c.cod_cliente and e.cuenta = 'Ingreso Sueldo') ingresos,
                    c.sexo,
                    (select max(d.direccion) from tbl_direcciones d where c.cod_cliente = d.cod_cliente and (d.tipo = 'CasaV' or d.tipo = 'Casa V' or d.tipo = 'Casa') ) Direccion,
                    (select max(d.direccion) from tbl_direcciones d where c.cod_cliente = d.cod_cliente and (d.tipo = 'Correo' or d.tipo = 'CorreoP')) correo_personal,
                    (select max(d.direccion) from tbl_direcciones d where c.cod_cliente = d.cod_cliente and (d.tipo = 'CorreoE' or d.tipo = 'Correo E' ) ) correo_empresa,
                    c.fecha_registro,
                    c.tipo_cliente,
                    c.cod_lugartrabajo,
                    c.cod_recomendado,
                    c.cod_ejecutivo,
                    c.bco_cta,
                    c.bco_nombre,
                    (isnull((select max(tasa) from tbl_Creditos p where p.cod_cliente = c.cod_cliente),0)/12) tasa_interes,
                    (isnull( (select tasa_interes from tbl_clientes e  where e.cod_cliente = c.cod_lugartrabajo),0)/ 12  ) tasa_interes_empresa,
                    10 tasa_mora,
                    5000 monto_minmo,
                    isnull((select max(monto) from tbl_Creditos p where p.cod_cliente = c.cod_cliente), 25000) monto_maximo,
                    1 plazo_min,
                    24 plaxo_max,
                    15 periodo_pago,
                    15 dia_pago1,
                    30 dia_pago2,
                    isnull((select es_fiador from tbl_clientes e where e.cod_cliente = c.cod_lugartrabajo),'N') autorizar,
                    c.tipo_cuota tipo_cuota,
                    c.cod_frecuencia_pago_cuota  cod_frecuencia_pago
                from tbl_clientes c
                order by 1 desc
            ");

            $data = 'test';
            // $data = json_encode($data);

            $paliService = new PaliServices();
            $pw = $paliService->sendClients($data);

            $this->info($pw);
            return true;


            $bitacora = new Bitacora();
            $bitacora->descripcion = "Carga de clientes completa.";
            $bitacora->save();

            $this->info("Carga de clientes completa");

            return true;

        } catch (\Throwable $th) {

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Error en la carga de clientes aplicada correctamente.";
            $bitacora->save();

            $this->info("Error al cargar clientes");
            return false;
        }
    }
}
