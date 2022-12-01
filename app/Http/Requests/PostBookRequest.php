<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostBookRequest extends FormRequest
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
        // @TODO implement
        return [
            'isbn'=>'required|string|min:13|max:13|unique:books',
            'title'=>'required|string|min:1',
            'description'=>'required|string|min:1',
            'authors'=>'required|array|min:1',
            'authors.*'=>'required|integer|exists:authors,id',
            'published_year' => 'required|integer|between:1900 ,2020',
        ];
    }

    public function messages()
    {
        return [
            'isbn.required' => 'Isbn is required',
            'isbn.unique' => 'Isbn Already Registered',
            'isbn.max' => 'Max Character 13',
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
            'published_year.required' => 'Publised year is required',
            'published_year.integer' => 'Publised year must numeric value.',
            'published_year.between' => 'Publised year Beetween 1900 and 2000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
        'errors' => $validator->errors(),
        'status' => true
        ], 422));
    }
}
