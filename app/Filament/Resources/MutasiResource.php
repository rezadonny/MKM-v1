<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MutasiResource\Pages;
use App\Filament\Resources\MutasiResource\RelationManagers;
use App\Models\Mutasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MutasiResource extends Resource
{
    protected static ?string $model = Mutasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('koleksi_id')
                    ->relationship('koleksi', 'id')
                    ->required(),
                Forms\Components\TextInput::make('jenis_mutasi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('koleksi.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_mutasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListMutasis::route('/'),
            'create' => Pages\CreateMutasi::route('/create'),
            'edit' => Pages\EditMutasi::route('/{record}/edit'),
        ];
    }
}
