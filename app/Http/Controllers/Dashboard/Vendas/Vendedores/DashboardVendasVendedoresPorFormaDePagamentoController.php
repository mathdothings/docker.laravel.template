<?php

namespace App\Http\Controllers\Dashboard\Vendas\Vendedores;

use App\DTOs\Sales\SalesByDealerDTO;
use App\Enums\Database\DatabaseConnections;
use App\Http\Controllers\Controller;
use App\Models\Sales\SalesByDealerModel;
use App\Services\Validation\DashboardVendasPorFormaDePagamentoPostValidation;
use App\Services\Validation\RequestValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardVendasVendedoresPorFormaDePagamentoController extends Controller
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
            DashboardVendasPorFormaDePagamentoPostValidation::rules());

        $salesDTO = SalesByDealerDTO::fromArray($validated);
        $filials = $salesDTO->getFilialCodes();
        $start = $salesDTO->getFormattedStartDate();
        $end = $salesDTO->getFormattedEndDate();
        $options = $salesDTO->getOptions();
        $paymentMethdos = $salesDTO->getPaymentMethodsCodes();

        $order = '';

        if ($options['byAmount']) {
            $order = 'order by 7, 5 desc';
        } else {
            $order = 'order by 8, 5 desc';
        }

        // quantidade_de_vendas_mes_ano_por_filial.sql
        // quantidade de vendas/total de vendas por filial por mês/ano
        $sales = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dvf.fil_codigo as filial_code,
                dvf.fil_nome as filial_label,
                dvf.vendedor_codigo as dealer_code,
                dvf.vendedor_nome as dealer_label,
                dvffp.form_pagt_codigo as payment_code,
                dvffp.form_pagt_descricao as payment_label,
                count(dvf.num_pedido) as amount,
                sum(dvffp.total) total
            from
                dm_vendas_filial dvf,
                dm_vendas_filial_forma_pagto dvffp
            where
                dvffp.data_faturamento >= to_date('$start', 'dd/mm/yyyy')
                and dvffp.data_faturamento <= to_date('$end', 'dd/mm/yyyy')
                and dvffp.form_pagt_codigo in ($paymentMethdos)
                and dvffp.fil_codigo in ($filials)
                and dvf.fil_codigo = dvffp.fil_codigo
                and dvf.num_pedido = dvffp.num_pedido
            group by
                1,
                2,
                3,
                4,
                5,
                6".$order);

        $salesRecord = SalesByDealerModel::fromDatabaseRows($sales);
        $graphics = SalesByDealerModel::toPlotlyBarTraces($salesRecord, $options, $start, $end);
        $response = [];
        $response['options'] = $options;
        $response['graphics'] = array_values($graphics);

        return response()->json($response, Response::HTTP_OK);
    }

    public function drill(Request $request): JsonResponse
    {
        $data = $request->all();
        $validated = RequestValidationService::validateOrFail($data,
            DashboardVendasPorFormaDePagamentoPostValidation::drillrules());

        $salesDTO = SalesByDealerDTO::fromArray($validated);
        $filials = $salesDTO->getFilialCodes();
        $start = $salesDTO->getFormattedStartDate();
        $end = $salesDTO->getFormattedEndDate();
        $options = $salesDTO->getOptions();
        $dealers = $salesDTO->getDealersCodes();

        $order = '';

        if ($options['byAmount']) {
            $order = 'order by 7, 5 desc';
        } else {
            $order = 'order by 8, 5 desc';
        }

        // quantidade_de_vendas_mes_ano_por_filial.sql
        // quantidade de vendas/total de vendas por filial por mês/ano
        $sales = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dvf.fil_codigo as filial_code,
                dvf.fil_nome as filial_label,
                dvf.vendedor_codigo as dealer_code,
                dvf.vendedor_nome as dealer_label,
                dvffp.form_pagt_codigo as payment_code,
                dvffp.form_pagt_descricao as payment_label,
                count(dvf.num_pedido) as amount,
                sum(dvffp.total) total
            from
                dm_vendas_filial dvf,
                dm_vendas_filial_forma_pagto dvffp
            where
                dvffp.data_faturamento >= to_date('$start', 'dd/mm/yyyy')
                and dvffp.data_faturamento <= to_date('$end', 'dd/mm/yyyy')
                and dvffp.fil_codigo in ($filials)
                and dvf.vendedor_codigo in ($dealers)
                and dvf.fil_codigo = dvffp.fil_codigo
                and dvf.num_pedido = dvffp.num_pedido
            group by
                1,
                2,
                3,
                4,
                5,
                6".$order);

        $paymentMethods = [];
        foreach ($sales as $value) {
            $paymentMethods[] = ['code' => $value->payment_code, 'label' => $value->payment_label];
        }

        $response['payment_methods'] = array_values($paymentMethods);
        $response['options'] = $options;

        $salesRecord = SalesByDealerModel::fromDatabaseRows($sales);
        $graphics = SalesByDealerModel::toPlotlyBarTraces($salesRecord, $options, $start, $end);
        $response['graphics'] = array_values($graphics);

        return response()->json($response, Response::HTTP_OK);
    }
}
