<?php
namespace App\Http\Requests;

use App\Rules\ValidRecaptcha;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GenerateEventPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'eventType'  => ['required', 'string', 'exists:event_types,slug'],
            'city'       => ['required', 'string', 'exists:cities,slug'],
            'guestCount' => ['required', 'integer', 'min:1', 'max:1000'],
            'budget'     => ['required', 'numeric', 'min:100'],
            'dietary'    => ['required', 'string', 'exists:dietary_preferences,slug'],
            'recaptchaToken' => [
                'required',
                'string',
                new ValidRecaptcha($this->ip()),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422));
    }

}
