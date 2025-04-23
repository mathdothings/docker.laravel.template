<?php

namespace App\Http\Controllers\Dashboard\Faturamento;

use App\DTOs\InvoiceDTO;
use App\Enums\Database\DatabaseConnections;
use App\Enums\PaymentMethods;
use App\Http\Controllers\Controller;
use App\Services\Validation\DashboardFaturamentoPostValidation;
use App\Services\Validation\RequestValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardFaturamentoDrillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();

        $validated = RequestValidationService::validateOrFail($data,
            DashboardFaturamentoPostValidation::drillrules());

        $invoiceDTO = InvoiceDTO::fromArray($validated);
        $filials = $invoiceDTO->getFilialCodes();
        $paymentMethods = $invoiceDTO->getPaymentMethodCodes();
        $options = $invoiceDTO->getFormattedOptions();

        $startDate = $invoiceDTO->getFormattedStartDate();
        $endDate = $invoiceDTO->getFormattedEndDate();

        // total_de_vendas_por_filial_e_forma_de_pagamento.sql
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

        $groupedResults = [];
        $paymentMethods = [];

        foreach ($totalDeVendasPorFilialEFormaDePagamento as $result) {
            $filial = $result->filial;
            $fil_codigo = $result->fil_codigo;
            $forma_pagamento = $result->forma_pagamento;
            $paymentMethods[] = PaymentMethods::mapFromDatabaseValue($forma_pagamento);

            if (! isset($groupedResults[$filial])) {
                $color = PaymentMethods::mapToColor($forma_pagamento);

                $groupedResults[$forma_pagamento] = [
                    'x' => [],
                    'y' => [],
                    'name' => $forma_pagamento,
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

            $groupedResults[$forma_pagamento]['x'][] = PaymentMethods::mapFromDatabaseValue($forma_pagamento);
            $groupedResults[$forma_pagamento]['y'][] = (float) $result->total;
        }

        $response = [
            'graphics' => array_values($groupedResults),
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
