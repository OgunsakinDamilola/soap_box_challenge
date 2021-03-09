<?php

namespace App\Http\Requests;

class CreateWorkspaceRequest extends CustomFormRequest
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
            'name' => 'required|string|max:255|unique:workspaces,name',
            'description' => 'present',
            'owner.email' => 'required|email|max:255',
            'owner.first_name' => 'required|string|max:255',
            'owner.last_name' => 'required|string|max:255',
            'owner.password' => 'required|string|confirmed|min:6'
        ];
    }
}
