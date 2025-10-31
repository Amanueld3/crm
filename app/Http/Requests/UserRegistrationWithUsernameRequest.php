<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegistrationWithUsernameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userInformationRequest = new UserInformationRequest;

        return array_merge($userInformationRequest->informationRules(), [
            'phone' => 'nullable|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|unique:users,username',
            'roles' => 'required|array',
        ]);
    }
}
