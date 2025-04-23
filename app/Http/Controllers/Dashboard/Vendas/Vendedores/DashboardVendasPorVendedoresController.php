<?php

namespace App\Http\Controllers\Dashboard\Vendas\Vendedores;

use App\DTOs\Sales\SalesByDealerDTO;
use App\Enums\Database\DatabaseConnections;
use App\Http\Controllers\Controller;
use App\Models\Sales\SalesByDealerModel;
use App\Services\Validation\DashboardVendasPorVendedorPostValidation;
use App\Services\Validation\RequestValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardVendasPorVendedoresController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->all();
        $validated = RequestValidationService::validateOrFail($data,
            DashboardVendasPorVendedorPostValidation::rules());

        $salesDTO = SalesByDealerDTO::fromArray($validated);
        $filials = $salesDTO->getFilialCodes();
        $start = $salesDTO->getFormattedStartDate();
        $end = $salesDTO->getFormattedEndDate();
        $options = $salesDTO->getOptions();

        $order = '';

        if ($options === 'byAmount') {
            $order = 'order by 1, 5 desc';
        }

        if ($options === 'byPrice') {
            $order = 'order by 1, 6 desc';
        }

        if ($options === 'byTicket') {
            $order = 'order by 1, 7 desc';
        }
        // quantidade_de_vendas_mes_ano_por_filial.sql
        // quantidade de vendas/total de vendas por filial por mês/ano
        $sales = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dvf.fil_codigo as filial_code,
                dvf.fil_nome as filial_label,
                dvf.vendedor_codigo dealer_code,
                dvf.vendedor_nome as dealer_label,
                dvf.vendedor_cpf dealer_cpf,
                count(distinct dvf.num_pedido) amount,
                sum(dvf.total) as total,
                round(sum(dvf.total) / count(distinct dvf.num_pedido), 2) ticket
            from
                dm_vendas_filial dvf
            where
                dvf.data_faturamento >= to_date('$start', 'dd/mm/yyyy')
                and dvf.data_faturamento < to_date('$end', 'dd/mm/yyyy')
                and dvf.fil_codigo in ($filials)
            group by
                1,
                2,
                3,
                4,
                5
            ".$order);

        $salesRecord = SalesByDealerModel::fromDatabaseRows($sales);
        $graphics = SalesByDealerModel::toPlotlyPieTraces($salesRecord, $options, $start, $end);
        $response = [];
        $response['options'] = $options;
        $response['graphics'] = array_values($graphics);

        return response()->json($response, Response::HTTP_OK);
    }
}
