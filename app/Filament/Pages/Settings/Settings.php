<?php

namespace App\Filament\Pages\Settings;
 
use Closure;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Actions\ButtonAction;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TimePicker;
 
class Settings extends BaseSettings
{
    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            Section::make('Configuracion del job')
                            ->description('Aqui puedes modificar los parametos y ejecucion del job para la carga automatica')
                            ->schema([
                                Fieldset::make('Rango de horas para ejecutar el job')
                                ->schema([
                                    TimePicker::make('hora_inicio')
                                    ->prefixIcon('heroicon-m-check-circle')
                                    ->prefixIconColor('success')
                                    ->label('Hora de inicio'),
                                    TimePicker::make('hora_fin')
                                    ->prefixIcon('heroicon-m-check-circle')
                                    ->prefixIconColor('success')
                                    ->label('Hora final'),
                                ])
                                ->columns(2)
                            ]),
                        ]),
                ]),
        ];
    }

    public function getFormActions(): array
    {
        return [];
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('save')
                ->label('Guardar cambios')
                ->action('save'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Parametros';
    }

    public function getTitle(): string
    {
        return 'Parametros generales';
    }

}