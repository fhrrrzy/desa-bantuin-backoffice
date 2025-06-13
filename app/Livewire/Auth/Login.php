<?php

namespace App\Livewire\Auth;

use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;
use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends BaseLogin
{
    public $phone_number = ''; // Change email to phone_number

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('phone_number') // Use phone_number
                ->label(__('Phone Number')) // Customize label
                ->required()
                ->autocomplete('tel'), // Hint for phone number input
            TextInput::make('password')
                ->label(__('filament-panels::pages/auth/login.fields.password.label'))
                ->password()
                ->required(),
            Checkbox::make('remember')
                ->label(__('filament-panels::pages/auth/login.fields.remember.label')),
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            throw ValidationException::withMessages([
                'phone_number' => __('filament-panels::pages/auth/login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]),
            ]);
        }

        $data = $this->form->getState();

        // Attempt to authenticate with phone_number instead of email
        if (! Filament::auth()->attempt([
            'phone_number' => $data['phone_number'],
            'password' => $data['password'],
        ], $data['remember'])) {
            throw ValidationException::withMessages([
                'phone_number' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    // You might also need to override throwFailureValidationException if you want to customize the error message location
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.phone_number' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
