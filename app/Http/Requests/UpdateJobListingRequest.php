<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $jobListing = $this->route('myJob') ?? $this->route('my_job');

        return $jobListing
            ? $this->user()?->can('update', $jobListing) === true
            : $this->user()?->isEmployer() === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:100'],
            'location'    => ['required', 'string', 'max:255'],
            'job_type'    => ['required', 'in:full-time,part-time,remote,contract,internship'],
            'salary_min'  => ['nullable', 'integer', 'min:0'],
            'salary_max'  => ['nullable', 'integer', 'gte:salary_min'],
            'tags'        => ['nullable', 'array'],
            'tags.*'      => ['exists:tags,id'],
            'expires_at'  => ['nullable', 'date', 'after:today'],
            'status'      => ['nullable', 'in:active,draft,closed'],
        ];
    }
}
