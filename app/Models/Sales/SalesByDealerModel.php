<?php

namespace App\Models\Sales;

use App\Constants\GraphColors;
use App\DTOs\Graphics\BarGraphicChartDTO;
use App\DTOs\Graphics\PieGraphicChartDTO;
use stdClass;

/**
 * Represents a sales record from the database
 *
 * This class encapsulates all data related to a single sales transaction record,
 * including location information, period, transaction count, and total amount.
 */
class SalesByDealerModel
{
    /**
     * @param  int  $code  Unique identifier for the sales filial
     * @param  string  $label  Descriptive name of the sales filial
     * @param  string  $monthYear  Period in MM/YYYY format when sales occurred
     * @param  int  $amount  Number of transactions/items sold
     * @param  float  $total  Total monetary value of sales
     */
    public function __construct(
        public readonly int $filialCode,
        public readonly string $filialLabel,
        public readonly string $monthYear,
        public readonly float $amount,
        public readonly float $total,
        public readonly float $ticket,
        public readonly int $dealerCode,
        public readonly string $dealerLabel,
        public readonly int $paymentMethodCode,
        public readonly string $paymentMethodLabel,
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
                    filialCode: (int) ($row->filial_code ?? 0),
                    filialLabel: trim($row->filial_label ?? ''),
                    dealerCode: (int) ($row->dealer_code ?? ''),
                    dealerLabel: trim($row->dealer_label ?? ''),
                    paymentMethodCode: (int) ($row->payment_code ?? 0),
                    paymentMethodLabel: trim($row->payment_label ?? ''),
                    monthYear: trim($row->month_year ?? ''),
                    amount: (float) ($row->amount ?? 0),
                    total: (float) ($row->total ?? '0'),
                    ticket: (float) ($row->ticket ?? 0)
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
     *               - filial_code (int)
     *               - filial_label (string)
     *               - month_year (string)
     *               - amount (int)
     *               - total (string) Formatted with 2 decimal places
     */
    public function toArray(): array
    {
        return [
            'filial_code' => $this->filialCode,
            'filial_label' => $this->filialLabel,
            'month_year' => $this->monthYear,
            'amount' => $this->amount,
            'total' => number_format($this->total, 2),
        ];
    }

    /**
     * Converts SalesRecord objects into Plotly pie traces format grouped by filial
     *
     * @param  SalesRecord[]  $salesRecords
     * @param  string  $endtDate
     * @return array Array of Plotly trace configurations
     */
    public static function toPlotlyPieTraces(array $salesRecords, string|array $options, string $startDate, string $endDate): array
    {
        $grouped = [];

        $trace = new PieGraphicChartDTO;
        $trace->setHole(0.5);

        foreach ($salesRecords as $record) {
            $key = $record->dealerCode;
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'dealer_label' => $record->dealerLabel,
                    'dealer_code' => $record->dealerCode,
                    'value' => 0,
                    $trace->setFilialName($record->filialLabel),
                    $trace->setFilialCode($record->filialCode),
                    $trace->setStartDate($startDate),
                    $trace->setEndDate($endDate),
                ];
            }

            $grouped[$key]['value'] += match ($options) {
                'byPrice' => $record->total,
                'byAmount' => $record->amount,
                'byTicket' => $record->ticket
            };
        }

        $traces = [];
        $colors = GraphColors::colorsToArray();

        $labels = [];
        $values = [];
        $colorList = [];
        $dealerCodes = [];
        $count = 1;

        foreach ($grouped as $dealer) {
            $labels[] = $dealer['dealer_label'];
            $values[] = $dealer['value'];
            $colorList[] = $colors[$count];
            $dealerCodes[] = $dealer['dealer_code'];
            $count++;
        }

        $trace->setLabels($labels);
        $trace->setValues($values);
        $trace->setMarkerColors($colorList);
        $trace->setDealersCodes($dealerCodes);

        if ($options === 'byPrice' || $options === 'byTicket') {
            $trace->setHoverTemplate('%{label}: R$ %{value:,.2f}<extra></extra>');
        } else {
            $trace->setHoverTemplate('%{label}: %{value}<extra></extra>');
        }

        $traces[] = $trace->toArray();

        return $traces;
    }

    /**
     * Converts SalesRecord objects into Plotly traces format grouped by filial
     *
     * @param  SalesRecord[]  $salesRecords
     * @return array Array of Plotly trace configurations
     */
    public static function toPlotlyBarTraces(array $salesRecords, array $options): array
    {
        $grouped = [];
        foreach ($salesRecords as $record) {
            $filialCode = $record->filialCode;
            $filialLabel = $record->filialLabel;
            $paymentMethodCode = $record->paymentMethodCode;
            $paymentMethodLabel = $record->paymentMethodLabel;
            $dealerCode = $record->dealerCode;
            $dealerLabel = $record->dealerLabel;
            $total = $record->total;
            $amount = $record->amount;

            if (! isset($grouped[$dealerLabel])) {
                $grouped[$dealerLabel] = [
                    'x' => [],
                    'y' => [],
                    'filial_code' => $filialCode,
                    'filial_label' => $filialLabel,
                    'payment_methods' => [],
                    'dealer_code' => $dealerCode,
                    'dealer_label' => $dealerLabel,
                ];
            }

            if (! isset($grouped[$dealerLabel]['x'][$paymentMethodLabel])) {
                $grouped[$dealerLabel]['x'][$paymentMethodLabel] = 0;
                $grouped[$dealerLabel]['y'][$paymentMethodLabel] = 0;
                $grouped[$dealerLabel]['payment_methods']['payment_method_code'] = $paymentMethodCode;
                $grouped[$dealerLabel]['payment_methods']['payment_method_label'] = $paymentMethodLabel;
            }

            if ($options['byAmount']) {
                $grouped[$dealerLabel]['x'][$paymentMethodLabel] += $amount;
                $grouped[$dealerLabel]['y'][$paymentMethodLabel] += $amount;
            }

            if ($options['byPrice']) {
                $grouped[$dealerLabel]['x'][$paymentMethodLabel] += $total;
                $grouped[$dealerLabel]['y'][$paymentMethodLabel] += $total;
            }
        }

        $traces = [];
        $colors = GraphColors::colorsToArray();

        $count = 1;
        foreach ($grouped as $dealer) {
            $trace = new BarGraphicChartDTO($dealer['dealer_label'], 'bar', $dealer['dealer_code']);
            $trace->setX(array_keys($dealer['x']));
            $trace->setY(array_values($dealer['y']));
            $trace->setMarkerColor($colors[$count]);
            $trace->setName($dealer['dealer_label']);

            if ($options['byPrice']) {
                $trace->setHoverTemplate('R$ %{y:,.2f}');
            }

            $traces[] = $trace->toArray();
            $count++;

        }

        return $traces;
    }
}
