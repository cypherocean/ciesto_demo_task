<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
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
    public function rules(){
        if($this->method() == 'PATCH'){
            return [
                'name' => 'required',
                'address' => 'required',
                'email' => 'required | email |unique:shop,email,'.$this->id
            ];
        }else{
            return [
                'name' => 'required',
                'address' => 'required',
                'email' => 'required | email |unique:shop',
            ];
        }
    }

    public function messages(){
        return [
            'name.required' => 'Please enter name',
            'address.required' => 'Please enter address',
            'email.required' => 'Please enter email',
            'email.email' => 'Please enter valid email',
            'email.unique' => 'Please enter unique email',
        ];
    }
}
