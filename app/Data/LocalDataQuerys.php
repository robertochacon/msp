<?php

namespace App\Data;

use App\Models\Bitacora;
use App\Services\PaliServices;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LocalDataQuerys {

    protected $connection;

    public function __construct() {
        $this->connection = DB::connection('sqlsrv');
    }

    public function clients($fecha){

        return $this->connection->select("
            SELECT
                c.cod_cliente AS id,
                c.identificacion AS cedula,
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
                c.cod_frecuencia_pago_cuota AS cod_frecuencia_pago,
                isnull((select e.restringido from tbl_empleados e where e.cod_cliente = c.cod_ejecutivo),'N') AS restringido
            FROM tbl_clientes c 
            WHERE c.fecha_actualizacion > ?
            ORDER BY 1 DESC
        ",[$fecha]);

    }

    public function loans($num_credito){

        $fecha_hoy = date('Y-m-d');
        $fecha_manana = date('Y-m-d', strtotime('+1 day'));

        return $this->connection->selectOne("
            select
            c.cod_cliente as client_id,
            c.num_credito as num_credito,
            'RD$' as moneda,
            c.monto as monto_aprobado,
            c.monto as monto_desembolsado,
            isnull((c.monto - (select sum(p.capital) from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito)),0) as monto_pagado,
            isnull((select Max(n.cuota) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P' ),0) as monto_cuota,
            c.estado as estado,
            c.fecha_aprobado as fecha_desembolso,
            (select Max(m.fecha) from tbl_Creditos_movimientos m where c.num_credito = m.no_credito and m.tipo_movi in (4, 5)) as fecha_ult_pago,
            (select Min(n.fecha_cuota) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P' ) as fecha_prox_pago,
            'PRESTAMOS PERSONALES' as tipo_credito,
            cuotas as cantidad_cuota,
            (select count(*) from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito and p.estado = 'C') as cuotas_pagadas,
            periodo_pago as periodo_pago,
            isnull((select isnull(sum(isnull(p.capital,0) + isnull(p.interes,0) + isnull(p.comision,0) + isnull(p.mora,0)),0)
                from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito and p.estado = 'P' and p.fecha_cuota < ?),0) as saldo_pagar,
            (select isnull(sum(isnull(p.capital,0)),0) from tbl_creditos_plan_pagos p where c.num_credito = p.no_credito and p.estado = 'P' and p.fecha_cuota > ?) as capital_no_vencido,
            isnull((select Max(n.interes) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P' ),0) as interes_cuota,
            isnull((select Max(n.comision) from tbl_Creditos_plan_pagos n where c.num_credito = n.no_credito and n.estado = 'P'),0 ) as seguro_cuota,                                        
            c.destino_fondos as proposito
            from tbl_creditos c
            where fecha_aprobado is not null and num_credito = ?
        ",[$fecha_manana, $fecha_hoy, $num_credito]);

    }

    public function loans_movements($fecha){

        return $this->connection->select("
            select m.no_credito
            from (
                select
                    m.no_credito
                from tbl_creditos_movimientos m 
                where m.monto_movimiento > 0
                and m.num_recibo is not null
                and m.tipo_movi in ('2','4', '5', '6', '9')
                and m.fecha_actualizacion > ?
                group by m.no_credito, m.num_recibo ) m
            group by m.no_credito
        ",[$fecha]);

    }

    public function movements($fecha){

        return $this->connection->select("
            select
                m.no_credito,
                m.num_recibo,
                max(m.fecha) as fecha,
                sum(isnull(m.monto_movimiento, 0)) as monto,

                (select isnull(sum(d.Capital_aplicado), 0) 
                from tbl_creditos_mov_desglose_pagos d
                where m.no_credito = d.credito and m.num_recibo = d.recibo) as capital,
                
                (select isnull(sum(d.interes_aplicado), 0) 
                from tbl_creditos_mov_desglose_pagos d
                where m.no_credito = d.credito and m.num_recibo = d.recibo) as interes,
                
                (select isnull(sum(d.mora_aplicada), 0) 
                from tbl_creditos_mov_desglose_pagos d
                where m.no_credito = d.credito and m.num_recibo = d.recibo) as mora,
                
                (select isnull(sum(d.comision_aplicada), 0) 
                from tbl_creditos_mov_desglose_pagos d
                where m.no_credito = d.credito and m.num_recibo = d.recibo) as otros,
                
                max(m.comentario) as descripcion,
                
                (select min(d.no_cuota) 
                from tbl_creditos_mov_desglose_pagos d
                where m.no_credito = d.credito and m.num_recibo = d.recibo) as cuota,
                
                isnull((select max(d.capital_anterior) 
                        from tbl_creditos_mov_desglose_pagos d
                        where m.no_credito = d.credito and m.num_recibo = d.recibo), 0) as capital_anterior,
                        
                max(m.usuario) as usuario
                
            from tbl_creditos_movimientos m 
            where m.monto_movimiento > 0
            and m.num_recibo is not null
            and m.tipo_movi in ('4', '5', '6', '9')
            and m.fecha_actualizacion > ?
            group by m.no_credito, m.num_recibo 
            order by fecha desc
        ",[$fecha]);

    }

}