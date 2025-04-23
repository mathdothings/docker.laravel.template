<?php

namespace App\DTOs\Sales;

use Carbon\Carbon;

class SalesByDealerDTO
{
    public function __construct(
        private readonly array $filials,
        private readonly ?array $dealers,
        private readonly DateRangeDTO $date,
        private readonly string|array $options,
        private readonly ?array $paymentMethods
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filials: $data['filials'],
            date: DateRangeDTO::fromArray($data['date']),
            options: $data['options'],
            paymentMethods: $data['paymentMethods'] ?? null,
            dealers: $data['dealers'] ?? null
        );
    }

    public function getFilialCodes(): string
    {
        $codes = array_column($this->filials, 'code');
        sort($codes, true);

        return implode(',', $codes);
    }

    public function getDealersCodes(): string
    {
        $codes = array_column($this->dealers, 'code');
        sort($codes, true);

        return implode(',', $codes);
    }

    public function getPaymentMethodsCodes(): string
    {

        if (! isset($this->paymentMethods)) {
            return '';
        }

        $codes = array_column($this->paymentMethods, 'code');
        sort($codes, true);

        return implode(',', $codes);
    }

    public function getOptions(): string|array
    {
        return $this->options;
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
