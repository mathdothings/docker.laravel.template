<?php

namespace App\Models\Sales;

use App\Constants\GraphColors;
use App\DTOs\Graphics\BarGraphicChartDTO;
use App\Traits\FormatDate;
use stdClass;

/**
 * Represents a sales record from the database
 *
 * This class encapsulates all data related to a single sales transaction record,
 * including location information, period, transaction count, and total amount.
 */
class SalesModel
{
    /**
     * @param  int  $code  Unique identifier for the sales filial
     * @param  string  $label  Descriptive name of the sales filial
     * @param  string  $monthYear  Period in MM/YYYY format when sales occurred
     * @param  int  $amount  Number of transactions/items sold
     * @param  float  $total  Total monetary value of sales
     */
    public function __construct(
        public readonly int $filial_code,
        public readonly string $filial_label,
        public readonly string $month_year,
        public readonly float $amount,
        public readonly float $total
    ) {}

    /**
     * Creates multiple SalesRecordModel instances from database result set
     *
     * @param  array  $rows  Array of associative arrays containing database rows:
     *                       Each row should contain:
     *                       - filial_code (string|int)
     *                       - filial_label (string)
     *                       - month_year (string)
     *                       - amount (string|int)
     *                       - total (string) Formatted with thousands separator
     * @return SalesModel[] Array of SalesRecord instances
     *
     * @throws InvalidArgumentException If any row is missing required fields
     */
    public static function fromDatabaseRows(array $rows): array
    {
        return array_map(
            function (stdClass $row) {
                return new self(
                    filial_code: (int) ($row->filial_code ?? 0),
                    filial_label: trim($row->filial_label ?? ''),
                    month_year: trim($row->month_year ?? ''),
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
            'code' => $this->filial_code,
            'label' => $this->filial_label,
            'month_year' => $this->month_year,
            'amount' => $this->amount,
            'total' => number_format($this->total, 2),
        ];
    }

    /**
     * Converts SalesRecord objects into Plotly traces format grouped by filial
     *
     * @param  SalesModel[]  $salesRecords
     * @return array Array of Plotly trace configurations
     */
    public static function toPlotlyTraces(array $salesRecords): array
    {
        $grouped = [];
        foreach ($salesRecords as $record) {
            $key = $record->filial_code;
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'filial_code' => $record->filial_code,
                    'filial_label' => $record->filial_label,
                    'data' => [],
                ];
            }

            $grouped[$key]['data'][$record->month_year] = $record->amount;
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

            $trace = new BarGraphicChartDTO($filial['filial_label'], 'bar', $filial['filial_code']);
            $trace->setX($formattedDates);
            $trace->setY(array_values($filial['data']));
            $trace->setMarkerColor($colors[$filial['code']]);
            $traces[] = $trace->toArray();

        }

        return $traces;
    }
}
