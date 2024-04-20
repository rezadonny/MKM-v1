<?php

namespace App\Filament\Resources\KoleksiResource\Pages;

use App\Filament\Resources\KoleksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKoleksis extends ListRecords
{
    protected static string $resource = KoleksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
