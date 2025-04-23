<?php

namespace App\DTOs;

use Carbon\Carbon;

class InvoiceDTO
{
    public function __construct(
        private readonly array $filials,
        private readonly array $paymentMethods,
        private readonly DateRangeDTO $date,
        private readonly InvoiceOptionsDTO $options
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filials: $data['filials'],
            paymentMethods: $data['paymentMethods'],
            date: DateRangeDTO::fromArray($data['date']),
            options: InvoiceOptionsDTO::fromArray($data['options'])
        );
    }

    public function getFilialCodes(): string
    {
        $codes = array_column($this->filials, 'code');

        return implode(',', $codes);
    }

    public function getPaymentMethodCodes(): string
    {
        $codes = array_column($this->paymentMethods, 'code');
        sort($codes, true);

        return implode(',', $codes);
    }

    public function getFormattedOptions(): string
    {
        $selling = $this->options->onlySelling ? '1' : '';
        $received = $this->options->onlyReceived ? '3' : '';

        return implode(',', array_filter([$selling, $received]));
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

class InvoiceOptionsDTO
{
    public function __construct(
        public readonly bool $onlySelling,
        public readonly bool $onlyReceived
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            onlySelling: $data['onlySelling'] ?? false,
            onlyReceived: $data['onlyReceived'] ?? false
        );
    }
}
