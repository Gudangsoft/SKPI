<?php

namespace App\Filament\Pages\Auth;

use App\Support\MathCaptcha;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /** @var array{0: int, 1: int} */
    public array $captchaNumbers = [1, 1];

    public function mount(): void
    {
        $this->regenerateCaptcha();

        parent::mount();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getCaptchaFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getCaptchaFormComponent(): Component
    {
        [$a, $b] = $this->captchaNumbers;

        return TextInput::make('captcha')
            ->label("Verifikasi: berapa {$a} + {$b}?")
            ->numeric()
            ->required();
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        if (! MathCaptcha::check('admin_login', $data['captcha'] ?? null)) {
            $this->regenerateCaptcha();

            throw ValidationException::withMessages([
                'data.captcha' => 'Jawaban verifikasi salah, silakan coba lagi.',
            ]);
        }

        return parent::authenticate();
    }

    protected function regenerateCaptcha(): void
    {
        $this->captchaNumbers = MathCaptcha::generate('admin_login');
    }
}
