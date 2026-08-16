<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SetPasswordRequest extends FormRequest
{
    /**
     * Authorisation is the session capability SetPasswordController checks, not
     * a policy — there is no authenticated user at this point to write one
     * against.
     */
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
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'The two passwords do not match.',
        ];
    }
}
