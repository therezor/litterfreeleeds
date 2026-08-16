<?php

namespace App\Http\Requests;

use App\Rules\KnownUkPostcode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class JoinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],

            // Reuses the rule the pick form uses, so an unknown postcode is a
            // validation message here rather than the UserObserver's exception.
            'postcode' => ['required', 'string', 'max:8', new KnownUkPostcode],

            'terms' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'postcode' => 'postcode',
            'terms' => 'terms and conditions',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'terms.accepted' => 'Please confirm you have read and agree to how we use your details.',
        ];
    }
}
