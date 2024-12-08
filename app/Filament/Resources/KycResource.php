<?php

namespace App\Filament\Resources;

use App\Events\KycUserVerified;
use App\Filament\Resources\KycResource\Pages;
use App\Filament\Resources\KycResource\RelationManagers;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Kyc;
use App\Notifications\SendKycApprovedNotification;
use App\Notifications\SendKycRejectNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KycResource extends Resource
{
    protected static ?string $model = Kyc::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'All KYC Requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('document_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('selfie')
                    ->maxLength(255),
                Forms\Components\TextInput::make('front')
                    ->maxLength(255),
                Forms\Components\TextInput::make('back')
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->searchable(),
                ImageColumn::make('selfie')  // Display selfie image
                    ->label('Selfie')
                    ->disk('public') // Ensure you're using the correct disk where images are stored
                    ->height(50),
                ImageColumn::make('front')  // Display front document image
                    ->label('Document Front')
                    ->disk('public')
                    ->height(50),
                ImageColumn::make('back')  // Display back document image
                    ->label('Document Back')
                    ->disk('public')
                    ->height(50),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
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
                Action::make('approve')
                    ->label(__('Approve'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Kyc $kyc) {
                        $kyc->status = 'approved';
                        $kyc->save();
                        // marking all transactions as approved
                        event(new KycUserVerified($kyc->user));

                        // Sending Notification
                        $kyc->user->notify(new SendKycApprovedNotification($kyc));

                        Notification::make()
                            ->title("KYC Approved")
                            ->body(__('Your KYC has been approved.'))
                            ->color('success')
                            ->send();
                    })->visible(fn(Kyc $kyc) => $kyc->status == 'pending'),
                Action::make('reject')
                    ->label(__('Reject'))
                    ->color('danger')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Kyc $kyc) {
                        $kyc->status = 'rejected';
                        $kyc->save();

                        // kyc reject email
                        $kyc->user->notify(new SendKycRejectNotification($kyc));

                        Notification::make()
                            ->title("KYC Rejected")
                            ->body(__('Your KYC has been rejected.'))
                            ->color('success')
                            ->send();
                    })->visible(fn(Kyc $kyc) => $kyc->status == 'pending'),
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
            'index' => Pages\ListKycs::route('/'),
            'create' => Pages\CreateKyc::route('/create'),
            'edit' => Pages\EditKyc::route('/{record}/edit'),
        ];
    }
}
