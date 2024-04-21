<?php

namespace App\Filament\Resources\MuseumResource\Pages;

use Filament\Actions;
use App\Models\Museum;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\MuseumResource;

class EditMuseum extends EditRecord
{
    protected static string $resource = MuseumResource::class;

    /*     protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    } */

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make() /* untuk menghapus file upload dari storage */
                ->after(
                    function (Museum $record) {
                        if ($record->foto_utama) {
                            Storage::disk('public')->delete($record->foto_utama);
                        }

                        if ($record->logo) {
                            Storage::disk('public')->delete($record->logo);
                        }
                    }
                ),
        ];
    }
}
