<?php

namespace App\Services\Validation;

class DashboardVendasPorVendedorPostValidation
{
    /**
     * Validation rules for the dashboard faturamento post request.
     */
    public static function rules(): array
    {
        return [
            'filials' => ['required', 'array', 'min:1'],
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

            'options' => ['required', 'string', 'min:1', 'max:8', 'in:byPrice,byAmount,byTicket'],
        ];
    }
}
