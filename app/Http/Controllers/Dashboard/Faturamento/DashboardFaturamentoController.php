<?php

namespace App\Http\Controllers\Dashboard\Faturamento;

use App\Constants\GraphColors;
use App\DTOs\InvoiceDTO;
use App\Enums\Database\DatabaseConnections;
use App\Http\Controllers\Controller;
use App\Services\Validation\DashboardFaturamentoPostValidation;
use App\Services\Validation\RequestValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardFaturamentoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();

        $validated = RequestValidationService::validateOrFail($data,
            DashboardFaturamentoPostValidation::rules());

        $invoiceDTO = InvoiceDTO::fromArray($validated);
        $filials = $invoiceDTO->getFilialCodes();
        $paymentMethods = $invoiceDTO->getPaymentMethodCodes();
        $options = $invoiceDTO->getFormattedOptions();

        $startDate = $invoiceDTO->getFormattedStartDate();
        $endDate = $invoiceDTO->getFormattedEndDate();

        $totalDeVendasPorFilialEFormaDePagamento = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dc.fil_codigo,
                dc.fil_nome filial,
                dc.form_pagt_descricao forma_pagamento,
                to_date(to_char(dc.vend_data, 'MM/YYYY'), 'MM/YYYY') mes_ano,
                sum(dc.total) total
            from dm_caixa dc
            where
                dc.vend_data >= to_date('$startDate', 'dd-mm-yyyy')
                and dc.vend_Data <= to_date('$endDate', 'dd-mm-yyyy')
                and dc.form_pagt_codigo in ($paymentMethods)
                and dc.fil_codigo in ($filials)
                and dc.tipo_lanc_codigo in ($options)
            group by
                1,
                2,
                3,
                4
            order by dc.fil_codigo, mes_ano, forma_pagamento
        ");

        $colors = GraphColors::colorsToArray();
        $plotlyData = self::preparePlotlyData($totalDeVendasPorFilialEFormaDePagamento, $colors);

        $response = [
            'graphics' => array_values($plotlyData),
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    private function preparePlotlyData(array $dbData, array $colors): array
    {
        $processedData = [];

        $dbData = array_map(function ($item) {
            return (array) $item;
        }, $dbData);

        foreach ($dbData as $row) {
            $filial = $row['filial'];
            $fil_codigo = $row['fil_codigo'];
            $paymentMethod = $row['forma_pagamento'];
            $total = (float) $row['total'];

            if (! isset($processedData[$filial])) {
                $processedData[$filial] = [
                    'paymentMethods' => [],
                    'totals' => [],
                    'code' => $fil_codigo,
                ];
            }

            if (! isset($processedData[$filial]['paymentMethods'][$paymentMethod])) {
                $processedData[$filial]['paymentMethods'][$paymentMethod] = 0;
            }
            $processedData[$filial]['paymentMethods'][$paymentMethod] += $total;
        }

        $plotlyTraces = [];
        foreach ($processedData as $filial => $data) {

            $color = $colors[$data['code']];
            $plotlyTraces[] = [
                'x' => array_keys($data['paymentMethods']),
                'y' => array_values($data['paymentMethods']),
                'name' => $filial,
                'type' => 'bar',
                'marker' => [
                    'color' => $color,
                    'cornerradius' => 10,
                ],
                'active' => true,
                'fil_codigo' => $fil_codigo,
                'hovertemplate' => 'R$ %{y:,.2f}',
            ];
        }

        return $plotlyTraces;
    }
}
