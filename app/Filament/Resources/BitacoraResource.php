<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BitacoraResource\Pages;
use App\Filament\Resources\BitacoraResource\RelationManagers;
use App\Models\Bitacora;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Novadaemon\FilamentPrettyJson\PrettyJson;

class BitacoraResource extends Resource
{
    protected static ?string $model = Bitacora::class;

    protected static ?string $navigationIcon = 'heroicon-m-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('total_completados')
                ->columnSpan(1),
                Forms\Components\TextInput::make('total_fallidos')
                ->columnSpan(1),
                Forms\Components\TextInput::make('descripcion')
                    ->columnSpanFull()
                    ->required(),
                PrettyJson::make('codes')
                    ->label('Detalles de la ejecucion:')
                    ->columnSpanFull(),
                // Forms\Components\Repeater::make('codes')
                //     ->label('Registros')
                //     ->schema([
                //         Forms\Components\TextInput::make('codigo')->label("Mensaje"),
                //         Forms\Components\Toggle::make('estado')
                //         ->onColor('success')
                //         ->offColor('warning'),
                //         Forms\Components\Textarea::make('cambios')
                //         ->json()
                //     ])
                //     ->columnSpanFull()
                //     ->grid(1)
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable(),
                Tables\Columns\IconColumn::make('estado')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBitacoras::route('/'),
            'create' => Pages\CreateBitacora::route('/create'),
            'view' => Pages\ViewBitacora::route('/{record}'),
            'edit' => Pages\EditBitacora::route('/{record}/edit'),
        ];
    }
}
