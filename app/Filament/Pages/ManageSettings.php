<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;

class ManageSettings extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Settings';
    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site_name', config('app.name')),
            'logo' => Setting::get('logo'),
            'primary_color' => Setting::get('primary_color', '#4f46e5'),
            'meta_title' => Setting::get('meta_title', 'My Software Store'),
            'meta_description' => Setting::get('meta_description', 'Best software platforms for your business.'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Settings')
                    ->schema([
                        TextInput::make('site_name')
                            ->required(),
                        FileUpload::make('logo')
                            ->image()
                            ->directory('branding'),
                        ColorPicker::make('primary_color'),
                    ]),
                Section::make('SEO Settings')
                    ->schema([
                        TextInput::make('meta_title'),
                        Textarea::make('meta_description'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::set($key, $value);
        }

        \Filament\Notifications\Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
