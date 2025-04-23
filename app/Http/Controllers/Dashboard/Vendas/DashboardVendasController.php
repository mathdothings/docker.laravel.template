<?php

namespace App\Http\Controllers\Dashboard\Vendas;

use App\Constants\GraphColors;
use App\Enums\Database\DatabaseConnections;
use App\Http\Controllers\Controller;
use App\Models\Sales\SalesModel;
use App\Traits\FormatDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardVendasController extends Controller
{
    public function index(): JsonResponse
    {
        $sales = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dvf.fil_codigo filial_code,
                dvf.fil_nome filial_label,
                to_date(to_char(dvf.data_faturamento, 'MM/YYYY'), 'MM/YYYY') month_year,
                count(distinct dvf.num_pedido) amount,
                sum(dvf.total) as total
            from
                aquarius_dm.dm_vendas_filial dvf
            where
                dvf.data_faturamento >= current_date - 30
                and dvf.data_faturamento < current_date
            group by
                1, 2, 3
            order by
                filial_code
        ");

        $salesRecord = SalesModel::fromDatabaseRows($sales);

        $colors = GraphColors::colorsToArray();
        foreach ($salesRecord as $result) {
            $filial = $result->filial_label;
            $fil_codigo = $result->filial_code;
            $amount = $result->amount;
            $total = $result->total;

            if (! isset($groupedResults[$filial])) {
                $color = $colors[$fil_codigo];

                $groupedResults[$filial] = [
                    'x' => [],
                    'y' => [],
                    'name' => $filial,
                    'type' => 'bar',
                    'marker' => [
                        'color' => $color,
                        'cornerradius' => 10,
                    ],
                    'active' => true,
                    'fil_codigo' => $fil_codigo,
                    'amount' => $amount,
                    'total' => $total,
                    // 'text' => $filial,
                    'hovertemplate' => 'R$ %{y:,.2f}',
                ];
            }

            $groupedResults[$filial]['x'][] = FormatDate::formatDateString($result->month_year);
            $groupedResults[$filial]['y'][] = (float) $result->total;
            $groupedResults[$filial]['amount'] = $amount;
            $groupedResults[$filial]['total'] = $total;
        }

        // $graphics = SalesModel::toPlotlyTraces($salesRecord);
        $response = [];
        $response['graphics'] = array_values($groupedResults);

        return response()->json($response, Response::HTTP_OK);
    }
}
