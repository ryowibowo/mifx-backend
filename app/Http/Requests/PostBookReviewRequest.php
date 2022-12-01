<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostBookReviewRequest extends FormRequest
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
            'review' => 'required|integer| min:1|max:5',
            'comment' => 'required|string| min:1',
        ];
    }

    public function messages()
    {
        return [
           
            'review.required' => 'Review is required',
            'review.integer' => 'Review  must numeric value.',
            'review.min' => 'Max Value 1',
            'review.max' => 'Max Value 5',
            'comment.required' => 'Comment is required',
            'comment.integer' => 'Comment must numeric value.',
            'comment.min' => 'Max Value 1',
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
