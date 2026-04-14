<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public function verify(string $token, ?string $ip = null): bool
    {
        $result = Http::asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $ip,
            ])
            ->json();

        return (bool) ($result['success'] ?? false);
    }
}
