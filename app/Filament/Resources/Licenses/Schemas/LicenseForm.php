<?php

namespace App\Filament\Resources\Licenses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class LicenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('platform_id')
                    ->relationship('platform', 'name')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->nullable(),
                TextInput::make('license_key')
                    ->required()
                    ->default(fn () => strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)))
                    ->unique(ignoreRecord: true),
                TextInput::make('domain')
                    ->maxLength(255),
                DateTimePicker::make('activated_at'),
                DateTimePicker::make('expires_at'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'suspended' => 'Suspended',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }
}
