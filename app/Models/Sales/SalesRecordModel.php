<?php

namespace App\Models\Sales;

use App\Constants\GraphColors;
use App\DTOs\Graphics\BarGraphicChartDTO;
use App\DTOs\Sales\SalesRecordOptionsDTO;
use App\Traits\FormatDate;
use stdClass;

/**
 * Represents a sales record from the database
 *
 * This class encapsulates all data related to a single sales transaction record,
 * including location information, period, transaction count, and total amount.
 */
class SalesRecordModel
{
    /**
     * @param  int  $code  Unique identifier for the sales filial
     * @param  string  $label  Descriptive name of the sales filial
     * @param  string  $monthYear  Period in MM/YYYY format when sales occurred
     * @param  int  $amount  Number of transactions/items sold
     * @param  float  $total  Total monetary value of sales
     */
    public function __construct(
        public readonly int $code,
        public readonly string $label,
        public readonly string $monthYear,
        public readonly float $amount,
        public readonly float $total
    ) {}

    /**
     * Creates multiple SalesRecordModel instances from database result set
     *
     * @param  array  $rows  Array of associative arrays containing database rows:
     *                       Each row should contain:
     *                       - code (string|int)
     *                       - label (string)
     *                       - month_year (string)
     *                       - amount (string|int)
     *                       - total (string) Formatted with thousands separator
     * @return SalesRecordModel[] Array of SalesRecord instances
     *
     * @throws InvalidArgumentException If any row is missing required fields
     */
    public static function fromDatabaseRows(array $rows): array
    {
        return array_map(
            function (stdClass $row) {
                return new self(
                    code: (int) ($row->code ?? 0),
                    label: trim($row->label ?? ''),
                    monthYear: trim($row->month_year ?? ''),
                    amount: (float) ($row->amount ?? 0),
                    total: (float) ($row->total ?? '0')
                );
            },
            $rows
        );
    }

    /**
     * Parses database total string into float
     */
    private static function parseTotal(string $total): float
    {
        // Remove thousands separators if present
        return str_replace(['.', ','], ['', '.'], $total);
    }

    /**
     * Converts the SalesRecordModel to an array representation
     *
     * @return array Associative array with these keys:
     *               - code (int)
     *               - label (string)
     *               - month_year (string)
     *               - amount (int)
     *               - total (string) Formatted with 2 decimal places
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'label' => $this->label,
            'month_year' => $this->monthYear,
            'amount' => $this->amount,
            'total' => number_format($this->total, 2),
        ];
    }

    /**
     * Converts SalesRecord objects into Plotly traces format grouped by filial
     *
     * @param  SalesRecord[]  $salesRecords
     * @return array Array of Plotly trace configurations
     */
    public static function toPlotlyTraces(array $salesRecords, SalesRecordOptionsDTO $options): array
    {
        $grouped = [];
        foreach ($salesRecords as $record) {
            $key = $record->code;
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'code' => $record->code,
                    'label' => $record->label,
                    'data' => [],
                ];
            }
            if ($options->byPrice) {
                $grouped[$key]['data'][$record->monthYear] = $record->total;
            } else {
                $grouped[$key]['data'][$record->monthYear] = $record->amount;
            }
        }

        $traces = [];
        $colors = GraphColors::colorsToArray();

        foreach ($grouped as $filial) {
            uksort($filial['data'], function ($a, $b) {
                return strtotime(str_replace('/', '-01-', $a)) - strtotime(str_replace('/', '-01-', $b));
            });

            $formattedDates = array_map(
                fn ($monthYear) => FormatDate::formatDateString($monthYear),
                array_keys($filial['data'])
            );

            $trace = new BarGraphicChartDTO($filial['label'], 'bar', $filial['code']);
            $trace->setX($formattedDates);
            $trace->setY(array_values($filial['data']));
            $trace->setMarkerColor($colors[$filial['code']]);
            if ($options->byPrice) {
                $trace->setHoverTemplate('R$ %{y:,.2f}');
            }

            $traces[] = $trace->toArray();

        }

        return $traces;
    }
}
