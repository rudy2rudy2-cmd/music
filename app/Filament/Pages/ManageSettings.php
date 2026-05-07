<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ManageSettings extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'logo' => Setting::get('logo'),
            'header_color' => Setting::get('header_color', '#ffffff'),
            'footer_color' => Setting::get('footer_color', '#f3f4f6'),
            'site_name' => Setting::get('site_name', 'My Platform Store'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Branding')
                    ->schema([
                        TextInput::make('site_name')
                            ->required(),
                        FileUpload::make('logo')
                            ->image()
                            ->directory('settings'),
                    ]),
                Section::make('Colors')
                    ->schema([
                        ColorPicker::make('header_color'),
                        ColorPicker::make('footer_color'),
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
        try {
            $data = $this->form->getState();

            foreach ($data as $key => $value) {
                Setting::set($key, $value);
            }

            Notification::make()
                ->title('Settings saved successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error saving settings')
                ->danger()
                ->send();
        }
    }
}
