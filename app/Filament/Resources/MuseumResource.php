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

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\MuseumResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MuseumResource\RelationManagers;

class MuseumResource extends Resource
{
    protected static ?string $model = Museum::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('nama')
                    ->label('Nama Museum')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
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

                Forms\Components\Tabs::make('Tabs')
                    ->tabs([

                        Tabs\Tab::make('Alamat')
                            ->icon('heroicon-m-map-pin')
                            ->schema([
                                Forms\Components\Section::make('Alamat')
                                    ->description('Silahkan masukan alamat museum.')
                                    /* ->icon('heroicon-m-map') */
                                    /* ->aside() */
                                    ->schema([
                                        Forms\Components\TextInput::make('alamat')
                                            ->label('Alamat')
                                            ->default(null)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('kota')
                                            ->label('Kabupaten / Kota')
                                            ->default(null)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('provinsi')
                                            ->label('Provinsi')
                                            ->maxLength(255)
                                            ->default(null),
                                        Forms\Components\TextInput::make('kode_pos')
                                            ->label('Kode Pos')
                                            ->maxLength(255)
                                            ->default(null),
                                    ]),
                            ]),

                        Tabs\Tab::make('Kontak')
                            ->icon('heroicon-m-device-phone-mobile')
                            ->schema([
                                Forms\Components\Section::make('Kontak')
                                    ->description('Silahkan masukan kontak museum.')
                                    ->schema([
                                        Forms\Components\TextInput::make('telepon')
                                            ->label('Telepon / Seluler')
                                            ->tel()
                                            ->maxLength(255)
                                            ->default(null),
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255)
                                            ->default(null),
                                        Forms\Components\TextInput::make('website')
                                            ->label('Website')
                                            ->url()
                                            ->prefix('URL')
                                            ->maxLength(255)
                                            ->default('https://museum.com'),
                                        Forms\Components\TextInput::make('googlemap')
                                            ->label('Google Map Link')
                                            ->maxLength(255)
                                            ->default(null),
                                    ]),
                            ]),

                        Tabs\Tab::make('Media Sosial')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Forms\Components\Section::make('Media Sosial')
                                    ->description('Silahkan masukan nama akun media sosial yang dimiliki museum.')
                                    ->schema([
                                        Forms\Components\TextInput::make('medsos_instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->maxLength(255)
                                            ->default('https://instagram.com/'),
                                        Forms\Components\TextInput::make('medsos_twitter')
                                            ->label('Twitter')
                                            ->url()
                                            ->maxLength(255)
                                            ->default('https://twitter.com/'),
                                        Forms\Components\TextInput::make('medsos_facebook')
                                            ->label('Facebook')
                                            ->url()
                                            ->maxLength(255)
                                            ->default('https://facebook.com/'),
                                        Forms\Components\TextInput::make('medsos_tiktok')
                                            ->label('Tiktok')
                                            ->url()
                                            ->maxLength(255)
                                            ->default('https://tiktok.com/'),
                                    ]),
                            ]),
                    ]),

                Tabs::make('Tabs')
                    ->tabs([

                        Tabs\Tab::make('Foto')
                            ->icon('heroicon-m-camera')
                            ->schema([
                                Section::make('Foto dan Logo')
                                    ->description('Silahkan unggah file foto dan logo museum')
                                    ->schema([
                                        Forms\Components\FileUpload::make('foto_utama')
                                            ->label('Foto')
                                            ->image()
                                            ->disk('public')
                                            ->directory('museum-foto')
                                            ->visibility('private')
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                null,
                                                '16:9',
                                                '4:3',
                                                '1:1',
                                            ])
                                            ->openable()
                                            ->default(null),
                                        Forms\Components\FileUpload::make('logo')
                                            ->label('Logo')
                                            ->image()
                                            ->disk('public')
                                            ->directory('museum-logo')
                                            ->visibility('private')
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                null,
                                                '16:9',
                                                '4:3',
                                                '1:1',
                                            ])
                                            ->default(null),
                                    ])
                            ]),

                        Tabs\Tab::make('Tipe')
                            ->icon('heroicon-m-squares-2x2')
                            ->schema([
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
                                            ->default(null)
                                            ->searchable(),
                                        Forms\Components\Select::make('tipe_area')
                                            ->label('Berdasarkan Area')
                                            ->options([
                                                'Museum Nasional' => 'Museum Nasional',
                                                'Museum Regional / Daerah' => 'Museum Regional / Daerah',
                                                'Museum Kota' => 'Museum Kota',
                                                'Museum Lokal' => 'Museum Lokal',
                                                'Museum Situs' => 'Museum Situs',
                                            ])
                                            ->default(null)
                                            ->searchable(),
                                        Forms\Components\Select::make('tipe_audience')
                                            ->label('Berdasarkan Audience')
                                            ->options([
                                                'Museum Umum' => 'Museum Umum',
                                                'Museum Khusus' => 'Museum Khusus',
                                                'Museum Pendidikan' => 'Museum Pendidikan',
                                                'Museum Komunitas' => 'Museum Komunitas',
                                                'Museum Anak-anak' => 'Museum Anak-anak',
                                            ])
                                            ->default(null)
                                            ->searchable(),
                                        Forms\Components\Select::make('tipe_pameran')
                                            ->label('Berdasarkan Pameran')
                                            ->options([
                                                'Museum Konvensional' => 'Museum Konvensional',
                                                'Museum Bangunan Bersejarah' => 'Museum Bangunan Bersejarah',
                                                'Museum Terbuka (Open Air Museum)' => 'Museum Terbuka (Open Air Museum)',
                                                'Museum Interaktif (Virtual Museum)' => 'Museum Interaktif (Virtual Museum)',
                                            ])
                                            ->default(null)
                                            ->searchable(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Keterangan')
                            ->icon('heroicon-m-newspaper')
                            ->schema([
                                Section::make('Keterangan')
                                    ->description('Silahkan masukan informasi sejarah singkat museum')
                                    ->schema([
                                        Forms\Components\RichEditor::make('keterangan')
                                            ->label('Sejaran Singkat')
                                            ->columnSpanFull()
                                            ->default(null)
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('museum-ket')
                                            ->fileAttachmentsVisibility('private'),
                                        Forms\Components\DatePicker::make('tanggal_berdiri')
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->closeOnDateSelection()
                                            ->label('Tanggal Berdiri')
                                            ->default(null),
                                        Forms\Components\TextInput::make('pengelola')
                                            ->label('Pengelola')
                                            ->default(null)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Tag')
                                            ->required()
                                            ->maxLength(255)
                                            ->default(null)
                                            /* ->unique(fn (?string $operation, ?Museum $record) => $operation == 'create') */
                                            ->disabled(fn (?string $operation, ?Museum $record) => $operation == 'edit'), /* form input slug tidak bisa diisi manual ada editing/updating data */
                                    ])
                            ]),

                    ]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No.')
                    ->rowIndex()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Museum')
                    ->limit(40)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kota')
                    ->label('Kab. / Kota')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('provinsi')
                    ->label('Provinsi')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kode_pos')
                    ->label('Kode Pos')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telepon')
                    ->label('Telpon / Seluler')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('website')
                    ->label('Website')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_instagram')
                    ->label('Instagram')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_twitter')
                    ->label('Twitter')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_facebook')
                    ->label('Facebook')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('medsos_tiktok')
                    ->label('Tiktok')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('googlemap')
                    ->label('Google Map Link')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_koleksi')
                    ->label('Tipe Koleksi')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_pengelola')
                    ->label('Tipe Pengelola')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_area')
                    ->label('Tipe Area')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_audience')
                    ->label('Tipe Audience')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tipe_pameran')
                    ->label('Tipe Pameran')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('foto_utama')
                    ->label('Foto Utama')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('logo')
                    ->label('Logo')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_berdiri')
                    ->label('Tanggal Berdiri')
                    ->searchable()
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengelola')
                    ->label('Pengelola')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Tag')
                    ->limit(40)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Data Diperbarui')
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
                    Tables\Actions\DeleteBulkAction::make() /* untuk menghapus file upload dari storage pada bulkaction */
                        ->after(function (Collection $record) {
                            foreach ($record as $key => $value) {
                                if ($value->foto_utama) {
                                    Storage::disk('public')->delete($value->foto_utama);
                                }

                                if ($value->logo) {
                                    Storage::disk('public')->delete($value->logo);
                                }
                            }
                        }),
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
