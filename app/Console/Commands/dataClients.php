<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
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

            // $data = DB::connection('sqlsrv')
            // ->select("
            //     select
            //         c.cod_cliente,
            //         'X'+c.identificacion,
            //         c.nombres,
            //         c.apellidos,
            //         c.fecha_nac,
            //         (select max(t.telefono) from tbl_telefonos t where c.cod_cliente = t.cod_cliente and t.tipo = 'Trabajo' and t.estado <> 'Inactivo') Tel_trab,
            //         (select max(t.telefono) from tbl_telefonos t where c.cod_cliente = t.cod_cliente and t.tipo = 'Celular' and t.estado <> 'Inactivo') Celular,
            //         (select isnull(max(valor),0) from tbl_estado_resultados e where e.cod_cliente = c.cod_cliente and e.cuenta = 'Ingreso Sueldo') ingresos,
            //         c.sexo,
            //         (select max(d.direccion) from tbl_direcciones d where c.cod_cliente = d.cod_cliente and (d.tipo = 'CasaV' or d.tipo = 'Casa V' or d.tipo = 'Casa') ) Direccion,
            //         (select max(d.direccion) from tbl_direcciones d where c.cod_cliente = d.cod_cliente and (d.tipo = 'Correo' or d.tipo = 'CorreoP')) correo_personal,
            //         (select max(d.direccion) from tbl_direcciones d where c.cod_cliente = d.cod_cliente and (d.tipo = 'CorreoE' or d.tipo = 'Correo E' ) ) correo_empresa,
            //         c.fecha_registro,
            //         c.tipo_cliente,
            //         c.cod_lugartrabajo,
            //         c.cod_recomendado,
            //         c.cod_ejecutivo,
            //         c.bco_cta,
            //         c.bco_nombre,
            //         (isnull((select max(tasa) from tbl_Creditos p where p.cod_cliente = c.cod_cliente),0)/12) tasa_interes,
            //         (isnull( (select tasa_interes from tbl_clientes e  where e.cod_cliente = c.cod_lugartrabajo),0)/ 12  ) tasa_interes_empresa,
            //         10 tasa_mora,
            //         5000 monto_minmo,
            //         isnull((select max(monto) from tbl_Creditos p where p.cod_cliente = c.cod_cliente), 25000) monto_maximo,
            //         1 plazo_min,
            //         24 plaxo_max,
            //         15 periodo_pago,
            //         15 dia_pago1,
            //         30 dia_pago2,
            //         isnull((select es_fiador from tbl_clientes e where e.cod_cliente = c.cod_lugartrabajo),'N') autorizar,
            //         c.tipo_cuota tipo_cuota,
            //         c.cod_frecuencia_pago_cuota cod_frecuencia_pago
            //     from tbl_clientes c where c.fecha_actualizacion is not null
            //     order by 1 desc
            // ");

            $data = DB::connection('sqlsrv')
            ->select("
                SELECT
                    c.cod_cliente AS id,
                    'X' + c.identificacion AS cedula,
                    c.nombres AS nombre,
                    c.apellidos AS apellido,
                    c.fecha_nac AS fecha_n,
                    (SELECT MAX(t.telefono) 
                    FROM tbl_telefonos t 
                    WHERE c.cod_cliente = t.cod_cliente 
                    AND t.tipo = 'Trabajo' 
                    AND t.estado <> 'Inactivo') AS telefono,
                    (SELECT MAX(t.telefono) 
                    FROM tbl_telefonos t 
                    WHERE c.cod_cliente = t.cod_cliente 
                    AND t.tipo = 'Celular' 
                    AND t.estado <> 'Inactivo') AS celular,
                    (SELECT ISNULL(MAX(valor), 0) 
                    FROM tbl_estado_resultados e 
                    WHERE e.cod_cliente = c.cod_cliente 
                    AND e.cuenta = 'Ingreso Sueldo') AS ingresos,
                    c.sexo AS sexo,
                    (SELECT MAX(d.direccion) 
                    FROM tbl_direcciones d 
                    WHERE c.cod_cliente = d.cod_cliente 
                    AND (d.tipo = 'CasaV' OR d.tipo = 'Casa V' OR d.tipo = 'Casa')) AS direccion,
                    (SELECT MAX(d.direccion) 
                    FROM tbl_direcciones d 
                    WHERE c.cod_cliente = d.cod_cliente 
                    AND (d.tipo = 'Correo' OR d.tipo = 'CorreoP')) AS email_personal,
                    (SELECT MAX(d.direccion) 
                    FROM tbl_direcciones d 
                    WHERE c.cod_cliente = d.cod_cliente 
                    AND (d.tipo = 'CorreoE' OR d.tipo = 'Correo E')) AS email_empresa,
                    c.fecha_registro AS fecha_registro,
                    c.tipo_cliente AS tipo_cliente,
                    c.cod_lugartrabajo AS cod_lugartrabajo,
                    c.cod_recomendado AS cod_recomendado,
                    c.cod_ejecutivo AS cod_ejecutivo,
                    c.bco_cta AS banco_cuenta,
                    c.bco_nombre AS banco_nombre,
                    (ISNULL((SELECT MAX(tasa) 
                            FROM tbl_Creditos p 
                            WHERE p.cod_cliente = c.cod_cliente), 0) / 12) AS tasa_interes,
                    (ISNULL((SELECT tasa_interes 
                            FROM tbl_clientes e 
                            WHERE e.cod_cliente = c.cod_lugartrabajo), 0) / 12) AS tasa_interes_empresa,
                    10 AS tasa_penalidad,
                    5000 AS monto_minimo,
                    ISNULL((SELECT MAX(monto) 
                            FROM tbl_Creditos p 
                            WHERE p.cod_cliente = c.cod_cliente), 25000) AS monto_maximo,
                    1 AS plazo_minimo,
                    24 AS plazo_maximo,
                    15 AS periodo_cuota,
                    15 AS dia_pago1,
                    30 AS dia_pago2,
                    ISNULL((SELECT es_fiador 
                            FROM tbl_clientes e 
                            WHERE e.cod_cliente = c.cod_lugartrabajo), 'N') AS autorizar,
                    c.tipo_cuota AS tipo_cuota,
                    c.cod_frecuencia_pago_cuota AS cod_frecuencia_pago
                FROM tbl_clientes c 
                WHERE c.fecha_actualizacion > ?
                ORDER BY 1 DESC
            ",[$fecha]);

            $paliService = new PaliServices();

            foreach ($data as $client) {
                $clientData = json_encode($client);
                $pw = $paliService->sendClients($client);
            }

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Carga de clientes completa. ".count($data)." clientes tuvieron efecto.";
            $bitacora->estado = true;
            $bitacora->save();

            // $this->info(json_enconde($data));
            $this->info("Carga de clientes completa.");

            return true;

        } catch (\Throwable $th) {

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Error en la carga de clientes.";
            $bitacora->estado = false;
            $bitacora->save();

            $this->info("Error al cargar clientes {$th}");
            return false;
        }
    }
}
