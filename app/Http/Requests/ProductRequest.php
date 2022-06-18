<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use DB;

class ProductRequest extends FormRequest
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
        $id = $this->id;
        // DB::enableQueryLog();
        $shop_id = $this->shop_id;
        if($this->method() == 'PATCH'){
            return [
                'name' => [
                    'required',
                    Rule::unique('products')->where(function ($query) use($shop_id) {
                        return  $query->where(['shop_id' => $shop_id]);
                        // DD(DB::getQueryLog() , $id ,$shop_id);
                    })->ignore($id)
                ],
                'shop_id' => 'required'
            ];
        }else{
            return [
                'name' => [
                    'required',
                    Rule::unique('products')->where(function ($query) use($shop_id) {
                        return $query->where('shop_id', $shop_id);
                    }),
                ],

                'shop_id' => 'required'
            ];
        }
    }

    public function messages(){
        return [
            'name.required' => 'Please enter name',
            'name.unique' => 'Please enter unique name',
            'shop_id.required' => 'Please select shop',
        ];
    }
}
