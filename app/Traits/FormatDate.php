<?php

namespace App\Traits;

use Carbon\Carbon;
use IntlDateFormatter;

trait FormatDate
{
    /**
     * Formats a date string as "MMM yyyy" (e.g., "2024-11-01" becomes "Nov 2024")
     *
     * @param  string  $dateString  The input date string (e.g., "2024-11-01")
     * @param  string  $locale  The locale to use for formatting (default: 'pt_BR')
     * @return string The formatted date string (e.g., "Nov 2024")
     *
     * @throws \Exception When date parsing fails
     */
    public static function formatDateString(string $dateString, string $locale = 'pt_BR'): string
    {
        $date = Carbon::parse($dateString);

        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            $date->getTimezone(),
            IntlDateFormatter::GREGORIAN,
            'MMM yyyy'
        );

        $formatted = $formatter->format($date);

        return ucfirst(str_replace('.', '', $formatted));
    }
}
