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

    protected function showPageHeaderActions(): bool
    {
        return false;
    }

    protected function getActions(): array
    {
        return [
            Action::make('load_clients')
                ->label('Cargar clientes')
                ->requiresConfirmation()
                ->icon('heroicon-s-arrow-path')
                ->action(function(){
    
                    try {
    
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
    
                }),

            Action::make('load_loans')
                ->label('Cargar creditos')
                ->requiresConfirmation()
                ->icon('heroicon-s-arrow-path')
                ->action(function(){
    
                    try {
 
                        Artisan::call('app:data-loans');
    
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
    
                }),

            Action::make('load_loans_movements')
                ->label('Cargar movimientos')
                ->requiresConfirmation()
                ->icon('heroicon-s-arrow-path')
                ->action(function(){
    
                    try {
    
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
    
                })

        ];
    }

}
