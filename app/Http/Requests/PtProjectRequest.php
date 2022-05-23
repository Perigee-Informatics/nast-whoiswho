<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class PtProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id_check = $this->request->get('id') ? ",".$this->request->get('id') : ",NULL";
        return [
            'client_id' => 'required',
            'name_lc' => 'required|max:200|unique:pt_project,name_lc'.$id_check,
            'description_lc' => 'required|max:2000',
            'remarks' => 'max:500',
            'category_id' => 'required',
            'unit_type' => 'required',
            'quantity' => 'required',
            'source_federal_amount' => 'required',
            'source_local_level_amount' => 'required',

        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
