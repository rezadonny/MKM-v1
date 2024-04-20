<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Museum;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;

use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\MuseumResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MuseumResource\RelationManagers;

class MuseumResource extends Resource
{
    protected static ?string $model = Museum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true) /* form slug akan terisi apabila disorot */

                    /* ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))), */ /* untuk langsung mengisi form slug tapi berubah jika nama diedit */

                    ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state, ?Museum $record) {
                        if ($operation == 'edit') {
                            return;
                        }
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    }),                                         /* ini solusi slug tidak ikut berubah jika nama di edit */

                Forms\Components\Section::make('Alamat')
                    ->description('Silahkan masukan alamat museum.')
                    ->schema([
                        Forms\Components\TextInput::make('alamat')
                            ->required()
                            ->prefixIcon('heroicon-m-map')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kota')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('provinsi')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('kode_pos')
                            ->numeric()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('googlemap')
                            ->maxLength(255)
                            ->default(null),
                    ]),


                Forms\Components\Section::make('Kontak')
                    ->description('Silahkan masukan kontak museum.')
                    ->schema([
                        Forms\Components\TextInput::make('telepon')
                            ->label('Telepon \ Seluler')
                            ->tel()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->prefix('https://')
                            ->maxLength(255)
                            ->default(null),
                    ]),

                Forms\Components\Section::make('Media Sosial')
                    ->description('Silahkan masukan nama akun media sosial yang dimiliki museum.')
                    ->schema([
                        Forms\Components\TextInput::make('medsos_instagram')
                            ->label('')
                            ->url()
                            ->maxLength(255)
                            ->default('https://instagram.com/')
                            ->prefix('Instagram'),
                        Forms\Components\TextInput::make('medsos_twitter')
                            ->label('')
                            ->url()
                            ->maxLength(255)
                            ->default('https://twitter.com/')
                            ->prefix('Twitter'),
                        Forms\Components\TextInput::make('medsos_facebook')
                            ->label('')
                            ->url()
                            ->maxLength(255)
                            ->default('https://facebook.com/')
                            ->prefix('Facebook'),
                        Forms\Components\TextInput::make('medsos_tiktok')
                            ->label('')
                            ->url()
                            ->maxLength(255)
                            ->default('https://tiktok.com/')
                            ->prefix('Tiktok'),
                    ]),

                Forms\Components\Section::make('Tipe Museum')
                    ->description('Silahkan masukan info berdasarkan tipe museum.')
                    ->schema([
                        Forms\Components\Select::make('tipe_koleksi')
                            ->label('Berdasarkan Koleksi')
                            ->options([
                                'Museum Arkeologi' => 'Museum Arkeologi',
                                'Museum Etnografi' => 'Museum Etnografi',
                                'Museum Geologi' => 'Museum Geologi',
                                'Museum Ilmu Pengetahuan (Science)' => 'Museum Ilmu Pengetahuan (Science)',
                                'Museum Industrial (Technology)' => 'Museum Industrial (Technology)',
                                'Museum Militer' => 'Museum Militer',
                                'Museum Sejarah' => 'Museum Sejarah',
                                'Museum Sejaran Alam (Natural History)' => 'Museum Sejaran Alam (Natural History)',
                                'Museum Seni' => 'Museum Seni',
                            ])
                            ->default(null)
                            ->searchable(),
                        Forms\Components\Select::make('tipe_pengelola')
                            ->label('Berdasarkan Pengelola')
                            ->options([
                                'Museum Pemerintah Pusat' => 'Museum Pemerintah Pusat',
                                'Museum Pemerintah Daerah' => 'Museum Pemerintah Daerah',
                                'Museum Tentara' => 'Museum Tentara',
                                'Museum Universitas' => 'Museum Universitas',
                                'Museum Independen' => 'Museum Independen',
                                'Museum Organisasi' => 'Museum Organisasi',
                                'Museum Perusahaan' => 'Museum Perusahaan',
                                'Museum Pribadi' => 'Museum Pribadi',
                            ])
                            ->default(null),
                        Forms\Components\Select::make('tipe_area')
                            ->label('Berdasarkan Area')
                            ->options([
                                'Museum Nasional' => 'Museum Nasional',
                                'Museum Regional / Daerah' => 'Museum Regional / Daerah',
                                'Museum Kota' => 'Museum Kota',
                                'Museum Lokal' => 'Museum Lokal',
                                'Museum Situs' => 'Museum Situs',
                            ])
                            ->default(null),
                        Forms\Components\Select::make('tipe_audience')
                            ->label('Berdasarkan Audience')
                            ->options([
                                'Museum Umum' => 'Museum Umum',
                                'Museum Khusus' => 'Museum Khusus',
                                'Museum Pendidikan' => 'Museum Pendidikan',
                                'Museum Komunitas' => 'Museum Komunitas',
                                'Museum Anak-anak' => 'Museum Anak-anak',
                            ])
                            ->default(null),
                        Forms\Components\Select::make('tipe_pameran')
                            ->label('Berdasarkan Pameran')
                            ->options([
                                'Museum Konvensional' => 'Museum Konvensional',
                                'Museum Bangunan Bersejarah' => 'Museum Bangunan Bersejarah',
                                'Museum Terbuka (Open Air Museum)' => 'Museum Terbuka (Open Air Museum)',
                                'Museum Interaktif (Virtual Museum)' => 'Museum Interaktif (Virtual Museum)',
                            ])
                            ->default(null),
                    ]),

                Forms\Components\FileUpload::make('foto_utama')
                    ->image()
                    ->disk('public')
                    ->default(null),
                Forms\Components\FileUpload::make('logo')
                    ->image()
                    ->disk('public')
                    ->default(null),
                Forms\Components\RichEditor::make('keterangan')
                    ->columnSpanFull()
                    ->default(null),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->default(null)
                    /* ->unique(fn (?string $operation, ?Museum $record) => $operation == 'create') */
                    ->disabled(fn (?string $operation, ?Museum $record) => $operation == 'edit'), /* form input slug tidak bisa diisi manual ada editing/updating data */
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kota')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('provinsi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kode_pos')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telepon')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_instagram')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_twitter')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_facebook')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_tiktok')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('googlemap')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_koleksi')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_pengelola')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_area')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_audience')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_pameran')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('foto_utama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListMuseums::route('/'),
            'create' => Pages\CreateMuseum::route('/create'),
            'edit' => Pages\EditMuseum::route('/{record}/edit'),
        ];
    }
}
