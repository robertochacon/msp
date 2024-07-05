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
                where m.no_credito = d.credito and m.num_recibo = d.recibo) as comision,
                
                max(m.comentario) as comentario,
                
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