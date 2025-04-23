<?php

namespace App\Services\Validation;

class DashboardVendasPorFormaDePagamentoPostValidation
{
    /**
     * Validation rules for the dashboard faturamento post request.
     */
    public static function rules(): array
    {
        return [
            'filials' => ['required', 'array', 'min:1', 'max:1000'],
            'filials.*.code' => ['required', 'int', 'min:1', 'max:1000'],

            'date' => ['required', 'array', 'min:1'],

            'date.start' => ['required', 'array'],
            'date.start.day' => ['required', 'integer', 'between:1,31'],
            'date.start.month' => ['required', 'integer', 'between:1,12'],
            'date.start.year' => ['required', 'integer', 'min:2000'],

            'date.end' => ['required', 'array'],
            'date.end.day' => ['required', 'integer', 'between:1,31'],
            'date.end.month' => ['required', 'integer', 'between:1,12'],
            'date.end.year' => ['required', 'integer', 'min:2000'],

            'paymentMethods' => ['required', 'array', 'min:1'],
            'paymentMethods.*.code' => ['required', 'integer', 'min:1'],

            'options' => ['required', 'array', 'min:1'],
            'options.*' => ['boolean'],
        ];
    }

    /**
     * Validation rules for the dashboard faturamento post drill request.
     */
    public static function drillrules(): array
    {
        return [
            'filials' => ['required', 'array', 'min:1', 'max:1000'],
            'filials.*.code' => ['required', 'int', 'min:1', 'max:1000'],

            'date' => ['required', 'array', 'min:1'],

            'date.start' => ['required', 'array'],
            'date.start.day' => ['required', 'integer', 'between:1,31'],
            'date.start.month' => ['required', 'integer', 'between:1,12'],
            'date.start.year' => ['required', 'integer', 'min:2000'],

            'date.end' => ['required', 'array'],
            'date.end.day' => ['required', 'integer', 'between:1,31'],
            'date.end.month' => ['required', 'integer', 'between:1,12'],
            'date.end.year' => ['required', 'integer', 'min:2000'],

            'options' => ['required', 'array', 'min:1'],
            'options.*' => ['boolean'],

            'dealers' => ['required', 'array', 'min:1', 'max:1000'],
            'dealers.*.code' => ['required', 'int', 'min:1', 'max:1000'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public static function messages(): array
    {
        return [
            'filials.required' => 'The filials field is required.',
            'filials.array' => 'The filials field must be an array.',
            'filials.*.integer' => 'Each filial must be an integer.',
            'filials.*.min' => 'Each filial must be at least 1.',

            'years.required' => 'The years field is required.',
            'years.array' => 'The years field must be an array.',
            'years.*.integer' => 'Each year must be an integer.',
            'years.*.min' => 'Each year must be at least 2000.',
            'years.*.max' => 'Each year must be no more than 2100.',

            'months.required' => 'The months field is required.',
            'months.array' => 'The months field must be an array.',
            'months.*.string' => 'Each month must be a string.',
            'months.*.regex' => 'Each month must be in the format "YYYY-MM".',
        ];
    }
}
