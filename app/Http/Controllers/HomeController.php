<?php

namespace App\Http\Controllers;

use App\Constants\GraphColors;
use App\Enums\Database\DatabaseConnections;
use App\Traits\FormatDate;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $quantidadeDeVendasPorFilial = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dvf.fil_codigo,
                dvf.fil_nome,
                count(distinct dvf.num_pedido) qtd_vendas
            from
                aquarius_dm.dm_vendas_filial dvf
            where
                dvf.data_faturamento >= to_date('01/12/2024', 'dd/mm/yyyy')
                and dvf.data_faturamento < to_date('01/02/2025', 'dd/mm/yyyy')
            group by
                dvf.fil_codigo,
                dvf.fil_nome
            order by
                dvf.fil_codigo
        ");

        // total_de_vendas_por_ano-mes_por_filial.sql
        $faturamento = DB::connection(DatabaseConnections::AQUARIUS->value)->select("
            select
                dm.fil_codigo,
                dm.fil_nome filial,
                to_date(to_char(dm.vend_data, 'MM/YYYY'), 'MM/YYYY') mes_ano,
                sum(dm.total) total
            from
                aquarius_dm.dm_caixa dm
            where
                dm.vend_data >= current_date - 30
                and dm.vend_data < current_date
                and dm.form_pagt_codigo != 5
            group by
                dm.fil_codigo,
                dm.fil_nome,
                to_date(to_char(dm.vend_data, 'MM/YYYY'), 'MM/YYYY')
            order by
                1,
                3,
                4
        ");

        $paymentTypes = DB::connection(DatabaseConnections::AQUARIUS->value)->select('
            select distinct
                dm.form_pagt_codigo as code,
                dm.form_pagt_descricao as label
            from
                aquarius_dm.dm_caixa dm
            where
                dm.vend_data >= current_date - 30
                and dm.vend_data < current_date
                and dm.form_pagt_codigo != 5
            order by
                1
        ');

        $groupedResults = [];

        $colors = GraphColors::colorsToArray();

        $lookup = array_column($quantidadeDeVendasPorFilial, 'qtd_vendas', 'fil_codigo');

        foreach ($faturamento as $result) {
            $filial = $result->filial;
            $fil_codigo = $result->fil_codigo;
            $qtd_vendas = $lookup[$fil_codigo];

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
                    'code' => $fil_codigo,
                    'qtd_vendas' => $qtd_vendas,
                    // 'text' => $filial,
                    'hovertemplate' => 'R$ %{y:,.2f}',
                ];
            }

            $groupedResults[$filial]['x'][] = FormatDate::formatDateString($result->mes_ano);
            $groupedResults[$filial]['y'][] = (float) $result->total;
            $groupedResults[$filial]['qtd_vendas'] = $qtd_vendas;
        }

        usort($paymentTypes, fn ($a, $b) => $a->label <=> $b->label);
        $response = [
            'graphics' => array_values($groupedResults),
            'payment_methods' => array_values($paymentTypes),
        ];

        return response()->json($response);
    }
}
