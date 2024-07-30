<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Bitacora;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $clientes = Bitacora::where('tipo','Cliente')->get()->count();
        $creditos = Bitacora::where('tipo','Credito')->get()->count();
        $movimiento = Bitacora::where('tipo','Movimiento')->get()->count();

        return [
            Stat::make('Cargas de clientes', $clientes)
                ->description('Total de cargas de clientes')
                ->descriptionIcon('heroicon-m-users')
                ->url("admin/carga-manual")
                ->color('info')
                ->chart([1,1]),
            Stat::make('Cargas de creditos', $creditos)
                ->description('Total de cargas de creditos')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->url("admin/carga-manual")
                ->color('info')
                ->chart([1,1]),
            Stat::make('Carga de movimientos', $movimiento)
                ->description('Total de cargas de movimientos')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->url("admin/carga-manual")
                ->color('info')
                ->chart([1,1]),
        ];
    }
}
