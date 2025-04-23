<?php

namespace App\DTOs\Sales;

use Carbon\Carbon;

class SalesRecordDTO
{
    public function __construct(
        private readonly array $filials,
        private readonly array $priceTables,
        private readonly DateRangeDTO $date,
        private readonly SalesRecordOptionsDTO $options,
        private readonly array $paymentMethdos
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filials: $data['filials'],
            date: DateRangeDTO::fromArray($data['date']),
            options: SalesRecordOptionsDTO::fromArray($data['options']),
            priceTables: $data['priceTables'] ?? [],
            paymentMethdos: $data['paymentMethods'] ?? []
        );
    }

    public function getFilialCodes(): string
    {
        $codes = array_column($this->filials, 'code');

        return implode(',', $codes);
    }

    public function getPriceTablesCodes(): string
    {
        $codes = array_column($this->priceTables, 'code');

        return implode(',', $codes);
    }

    public function getPaymentMethdos(): string
    {
        $codes = array_column($this->paymentMethdos, 'code');

        return implode(',', $codes);
    }

    public function getOptions(): SalesRecordOptionsDTO
    {
        return SalesRecordOptionsDTO::createWithTruthyValues(
            byAmount: $this->options->byAmount,
            byPrice: $this->options->byPrice
        );
    }

    public function getFormattedStartDate(): string
    {
        return $this->date->getFormattedStartDate();
    }

    public function getFormattedEndDate(): string
    {
        return $this->date->getFormattedEndDate();
    }
}

class DateRangeDTO
{
    public function __construct(
        private readonly DateDTO $start,
        private readonly DateDTO $end
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            start: DateDTO::fromArray($data['start']),
            end: DateDTO::fromArray($data['end'])
        );
    }

    public function getFormattedStartDate(): string
    {
        return Carbon::create(
            $this->start->year,
            $this->start->month,
            $this->start->day
        )->format('d-m-Y');
    }

    public function getFormattedEndDate(): string
    {
        return Carbon::create(
            $this->end->year,
            $this->end->month,
            $this->end->day
        )->format('d-m-Y');
    }
}

class DateDTO
{
    public function __construct(
        public readonly int $year,
        public readonly int $month,
        public readonly int $day
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            year: $data['year'],
            month: $data['month'],
            day: $data['day']
        );
    }
}

class SalesRecordOptionsDTO
{
    public function __construct(
        public readonly ?bool $byAmount = null,
        public readonly ?bool $byPrice = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            byAmount: $data['byAmount'] ?? false,
            byPrice: $data['byPrice'] ?? false
        );
    }

    /**
     * Creates a DTO with only truthy properties.
     */
    public static function createWithTruthyValues(bool $byAmount, bool $byPrice): self
    {
        return new self(
            byAmount: $byAmount ?: false,
            byPrice: $byPrice ?: false
        );
    }
}
