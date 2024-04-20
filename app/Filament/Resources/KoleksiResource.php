<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Koleksi;

use Filament\Forms\Set;
use Illuminate\Support\Str;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KoleksiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KoleksiResource\RelationManagers;

class KoleksiResource extends Resource
{
    protected static ?string $model = Koleksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('museum_id')
                    ->relationship('museum', 'nama')
                    ->required(),
                Forms\Components\TextInput::make('no_reg')
                    ->maxLength(255)
                    ->unique()
                    ->default(null),
                Forms\Components\TextInput::make('no_inv')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur:true) /* form slug akan terisi apabila disorot */
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))), /* untuk langsung mengisi form slug  */
                Forms\Components\TextInput::make('slug')
                    ->required()
                    /* ->unique() */
                    ->disabled() /* form input slug tidak bisa diisi manual */
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('museum.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_reg')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('no_inv')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListKoleksis::route('/'),
            'create' => Pages\CreateKoleksi::route('/create'),
            'edit' => Pages\EditKoleksi::route('/{record}/edit'),
        ];
    }
}
