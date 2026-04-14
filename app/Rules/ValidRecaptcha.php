<?php
namespace App\Rules;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidRecaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    
    public function __construct(
        private readonly ? string $ip = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || $value === '') {
            $fail('Invalid reCAPTCHA token.');
            return;
        }

        $recaptchaService = new RecaptchaService();

        if (!$recaptchaService->verify($value, $this->ip)) {
            $fail('reCAPTCHA verification failed. Please try again.');
        }
    }
}
