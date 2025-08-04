<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAmigurumiRowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'instructions' => 'sometimes|required|string|max:255',
            'row_number' => 'sometimes|required|string|max:255',
            'stitch_number' => 'nullable|integer|min:1',
            'comment' => 'nullable|string|max:255',
             'color_change' => 'nullable|string|max:255',
            'order' => 'required|integer',
        ];
    }
}
