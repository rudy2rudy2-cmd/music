<?php

namespace App\Filament\Resources\Platforms\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

class PlatformForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                TextInput::make('version')
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                FileUpload::make('zip_path')
                    ->label('Platform ZIP File')
                    ->directory('platforms/zips')
                    ->acceptedFileTypes(['application/zip']),
                FileUpload::make('image_path')
                    ->label('Cover Image')
                    ->directory('platforms/images')
                    ->image(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}
