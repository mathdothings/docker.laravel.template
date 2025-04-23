<?php

namespace App\Http\Controllers\Dashboard\Vendas;

use App\DTOs\Sales\SalesRecordDTO;
use App\Enums\Database\DatabaseConnections;
use App\Http\Controllers\Controller;
use App\Models\Sales\SalesRecordModel;
use App\Services\Validation\DashboardVendasPostValidation;
use App\Services\Validation\RequestValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardVendasPorFormaDePagamentoController extends Controller
{
    public function index(): JsonResponse
    {
        $paymentMethods = DB::connection(DatabaseConnections::AQUARIUS->value)->select('
            select
                distinct form_pagt_codigo as code,
                form_pagt_descricao as label
            from
                dm_vendas_filial_forma_pagto dvffp
            order by
                1
        ');

        $response = [
            'payment_methods' => array_values($paymentMethods),
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function post(Request $request): JsonResponse
    {
        $data = $request->all();
        $validated = RequestValidationService::validateOrFail($data,
            DashboardVendasPostValidation::rules());

        $salesDTO = SalesRecordDTO::fromArray($validated);
        $filials = $salesDTO->getFilialCodes();
        $start = $salesDTO->getFormattedStartDate();
        $end = $salesDTO->getFormattedEndDate();
        $paymentMethods = $salesDTO->getPaymentMethdos();
        $options = $salesDTO->getOptions();

        // quantidade_de_vendas_mes_ano_por_filial.sql
        // quantidade de vendas/total de vendas por filial por mês/ano
        $sales = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dvf.fil_codigo as code,
                dvf.fil_nome as label,
                to_date(to_char(dvf.data_faturamento, 'mm/yyyy'), 'mm/yyyy') as month_year,
                count(distinct dvf.num_pedido) as amount,
                sum(dvf.total) as total
            from
                dm_vendas_filial_forma_pagto dvf
            where
                dvf.data_faturamento between to_date('$start', 'dd/mm/yyyy')
                and to_date('$end', 'dd/mm/yyyy')
                and dvf.fil_codigo in ($filials)
                and dvf.form_pagt_codigo in ($paymentMethods)
            group by
                dvf.fil_codigo,
                dvf.fil_nome,
                to_date(to_char(dvf.data_faturamento, 'mm/yyyy'), 'mm/yyyy')
            order by
                dvf.fil_codigo,
                to_date(to_char(dvf.data_faturamento, 'mm/yyyy'), 'mm/yyyy')
        ");

        $salesRecord = SalesRecordModel::fromDatabaseRows($sales);
        $graphics = SalesRecordModel::toPlotlyTraces($salesRecord, $options);
        $response = [];
        $response['options'] = $options;
        $response['graphics'] = $graphics;

        return response()->json($response, Response::HTTP_OK);
    }
}
