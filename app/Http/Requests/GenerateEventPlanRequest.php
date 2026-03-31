<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
            'eventType'  => ['required', 'string', 'in:birthday-party,wedding,engagement,corporate-event,baby-shower'],
            'city'       => ['required', 'string', 'in:ottawa,toronto,montreal,vancouver,calgary'],
            'guestCount' => ['required', 'integer', 'min:1', 'max:1000'],
            'budget'     => ['required', 'numeric', 'min:100'],
            'dietary'    => ['required', 'string', 'in:halal,vegetarian,vegan,gluten-free,none'],
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
