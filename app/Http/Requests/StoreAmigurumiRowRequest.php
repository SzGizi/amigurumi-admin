<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAmigurumiRowRequest extends FormRequest
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
        return [
            'instructions' => 'required|string|max:255',
            'row_number' => 'required|string|max:255',
            'amigurumi_section_id' => 'required|exists:amigurumi_sections,id',
            'stitch_number' => 'integer|min:1',
            'comment' => 'string|max:255',
            'color_change' => 'string|max:255',
            'order' => 'required|integer',
        ];
    }
}
