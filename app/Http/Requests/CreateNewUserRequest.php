<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewUserRequest extends CustomFormRequest
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
            'users.*.email' => 'required|email|max:255',
            'users.*.first_name' => 'required|string|max:255',
            'users.*.last_name' => 'required|string|max:255',
            'users.*.password' => 'required|string|confirmed|min:6'
        ];
    }
}
