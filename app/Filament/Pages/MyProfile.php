<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class MyProfile extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.my-profile';
    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];
    public $qrCodeUrl = null;

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'two_factor_enabled' => !empty($user->google2fa_secret),
        ]);

        if (!empty($user->google2fa_secret)) {
            $google2fa = app('pragmarx.google2fa');
            $this->qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
                Toggle::make('two_factor_enabled')
                    ->label('Enable 2FA')
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $google2fa = app('pragmarx.google2fa');
                            $secret = $google2fa->generateSecretKey();
                            Auth::user()->update(['google2fa_secret' => $secret]);
                        } else {
                            Auth::user()->update(['google2fa_secret' => null]);
                        }
                        $this->redirect(static::getUrl());
                    }),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Profile')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();
        Auth::user()->update([
            'name' => $state['name'],
            'email' => $state['email'],
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Profile updated')
            ->success()
            ->send();
    }
}
