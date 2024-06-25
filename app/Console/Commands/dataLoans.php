<?php

namespace App\Console\Commands;

use App\Models\Bitacora;
use App\Services\PaliServices;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        try {

            $data = DB::connection('sqlsrv')
            ->select("
            select
                c.cod_cliente,
                c.num_credito,
                'RD$',
                c.monto,
                c.monto,
                (c.monto - (select sum(p.capital) from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito)) monto_pagado,
                (select Max(n.cuota) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P' ) monto_cuota,
                c.estado,
                c.fecha_aprobado,
                (select Max(m.fecha) from tbl_Creditos_movimientos m where c.num_credito = m.no_credito and m.tipo_movi in (4, 5)) fecha_ult_pago,
                (select Min(n.fecha_cuota) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P' ) fecha_prox_pago,
                'PRESTAMOS PERSONALES',
                cuotas,
                (select count(*) from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito
                                                        and p.estado = 'C') cuotas_pagadas,
                periodo_pago,
                isnull((select isnull(sum(isnull(p.capital,0) + isnull(p.interes,0) + isnull(p.comision,0) + isnull(p.mora,0)),0)
                    from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito
                                                        and p.estado = 'P' and p.fecha_cuota < '2024-06-19'),0) saldo_pagar,
                (select isnull(sum(isnull(p.capital,0)),0) from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito
                                                        and p.estado = 'P' and p.fecha_cuota > '2024-06-18') capital_no_vencido,
                isnull((select Max(n.interes) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P' ),0) interes_cuota,
                isnull((select Max(n.comision) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P'),0 ) comision_cuota,
                c.destino_fondos
            from tbl_creditos c
            where fecha_aprobado is not null
            order by 2 desc
            ");

            $data = json_encode($data);
            // echo $data;
            $this->info($data);
            return 0;

            //$data_array = [];

            //$pw = (new PaliServices())->sendCredits($data_array);

        } catch (\Throwable $th) {

            $bitacora = new Bitacora();
            $bitacora->descripcion = "Error en la carga de creditos.";
            $bitacora->save();

            $this->info($data);
            return false;

        }
    }
}
