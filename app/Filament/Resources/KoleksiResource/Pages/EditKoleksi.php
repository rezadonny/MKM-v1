<?php

namespace App\Filament\Resources\KoleksiResource\Pages;

use App\Filament\Resources\KoleksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKoleksi extends EditRecord
{
    protected static string $resource = KoleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
