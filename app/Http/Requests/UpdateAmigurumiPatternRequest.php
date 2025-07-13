<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAmigurumiPatternRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'yarn_description' => 'nullable|string',
            'tools_description' => 'nullable|string',
            'sections' => 'nullable|array',
            'sections.*.title' => 'required|string|max:255',
            'sections.*.order' => 'nullable|integer|min:0',
            'sections.*.rows' => 'nullable|array',
            'sections.*.rows.*.row_number' => 'required|string|max:255',
            'sections.*.rows.*.instructions' => 'required|string|max:1000',
            'sections.*.rows.*.stitch_number' => 'integer|min:1',
            'sections.*.rows.*.comment' => 'string|max:255',
        ];
    }
}
