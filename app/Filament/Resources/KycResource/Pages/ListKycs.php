<?php

namespace App\Filament\Resources\KycResource\Pages;

use App\Filament\Resources\KycResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;

class ListKycs extends ListRecords
{
    protected static string $resource = KycResource::class;

    public function getTabs(): array
    {
        return [
            'ApprovedKyc' => Tab::make('Approved KYC')->modifyQueryUsing(function ($query) {
                return $query->where('status', 'approved');
            }),
            'OnlyAdmins' => Tab::make('Pending KYC')->modifyQueryUsing(function ($query) {
                return $query->where('status', 'pending');
            }),
            'RejectedKyc' => Tab::make('Rejected KYC')->modifyQueryUsing(function ($query) {
                return $query->where('status', 'rejected');
            }),
            'All' => Tab::make('All KYC'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
