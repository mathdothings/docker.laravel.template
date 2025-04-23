<?php

namespace App\DTOs\Sales;

class SalesDTO
{
    public function __construct(
        public readonly array $filials,
        public readonly int $amount,
        public readonly float $total
    ) {}

    /**
     * Creates DTO from database results (array of stdClass rows)
     */
    public static function fromDatabaseResults(array $rows): self
    {
        $branches = array_map(
            fn ($row) => [
                'code' => $row->filial_code,
                'label' => $row->filial_label,
                'amount' => (int) $row->amount,
                'total' => (float) $row->total,
            ],
            $rows
        );

        return new self(
            filials: $branches,
            amount: array_sum(array_column($branches, 'amount')),
            total: array_sum(array_column($branches, 'total'))
        );
    }
}
