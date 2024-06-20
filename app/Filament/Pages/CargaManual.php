<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class CargaManual extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-arrow-path';

    protected static string $view = 'filament.pages.carga-manual';

    protected ?string $subheading = 'Carga manual de datos del sistema financiero hacia Pali Web.';

    protected function getActions(): array
    {
        return [
            Action::make('Historial de carga')
                ->icon('heroicon-o-eye')
                ->url('bitacoras')
        ];
    }

    public function cargaClients(): Action
    {
        return Action::make('Cargar clientes')
            ->requiresConfirmation()
            ->icon('heroicon-s-arrow-path')
            ->action(function(){

                try {

                    Notification::make()
                    ->title('Carga de clientes en proceso')
                    ->info()
                    ->send();

                    Artisan::call('app:data-clients');

                    Notification::make()
                    ->title('Carga completa.')
                    ->success()
                    ->send();

                } catch (\Throwable $th) {

                    Notification::make()
                    ->title('Error al cargar clientes.')
                    ->danger()
                    ->send();

                }

            });
    }

    public function cargaCredits(): Action
    {
        return Action::make('Cargar creditos')
            ->requiresConfirmation()
            ->icon('heroicon-s-arrow-path')
            ->action(function(){

                try {

                    Notification::make()
                    ->title('Carga de creditos en proceso')
                    ->info()
                    ->send();

                    Artisan::call('app:data-crdits');

                    Notification::make()
                    ->title('Carga completa.')
                    ->success()
                    ->send();

                } catch (\Throwable $th) {

                    Notification::make()
                    ->title('Error al cargar creditos.')
                    ->danger()
                    ->send();

                }

            });
    }

    public function cargaMovements(): Action
    {
        return Action::make('Cargar movimientos')
            ->requiresConfirmation()
            ->icon('heroicon-s-arrow-path')
            ->action(function(){

                try {

                    Notification::make()
                    ->title('Carga de movimientos en proceso')
                    ->info()
                    ->send();

                    Artisan::call('app:data-movements');

                    Notification::make()
                    ->title('Carga completa.')
                    ->success()
                    ->send();

                } catch (\Throwable $th) {

                    Notification::make()
                    ->title('Error al cargar movimientos.')
                    ->danger()
                    ->send();

                }

            });
    }


}
