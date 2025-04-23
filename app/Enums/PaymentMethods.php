<?php

namespace App\Enums;

use App\Constants\GraphColors;

/**
 * Enum representing various payment methods.
 *
 * Each case corresponds to a specific payment method, with a string value that
 * represents the human-readable name of the payment method.
 * These string values are also used to map the payment method names
 * stored in the database to their corresponding enum cases.
 */
enum PaymentMethods: string
{
    /** Represents the American Express payment method. */
    case AMERIC_EXP = 'American Express';

    /** Represents a generic card payment method. */
    case CARTAO = 'Cartão';

    /** Represents payment by cheque. */
    case CHEQUE = 'Cheque';

    /** Represents payment by cash. */
    case DINHEIRO = 'Dinheiro';

    /** Represents payment by promissory note. */
    case DUPLICATA = 'Duplicata';

    /** Represents the Hipercard payment method. */
    case HIPERCARD = 'Hipercard';

    /** Represents the Maestro payment method. */
    case MAESTRO = 'Maestro';

    /** Represents the Mastercard payment method. */
    case MASTERCARD = 'Mastercard';

    /** Represents the Pix payment method. */
    case PIX = 'Pix';

    /** Represents the Pix payment method. */
    case TROCA = 'Troca';

    /** Represents the Visa payment method. */
    case VISA = 'Visa';

    /** Represents the Visa Electron payment method. */
    case VISA_ELECT = 'Visa Electron';

    /** Represents the Deposito payment method. */
    case DEPOSITO = 'Deposito';

    /**
     * Converts a database value into the corresponding PaymentMethods enum case.
     *
     * This method is useful when retrieving payment method values from a database
     * and mapping them to the appropriate enum case.
     *
     * @param  string  $DatabaseValue  The value retrieved from the database.
     * @return self|null The corresponding PaymentMethods enum case, or null if no match is found.
     */
    public static function mapFromDatabaseValue(string $DatabaseValue): ?self
    {
        $mapping = [
            'AMERIC EXP' => self::AMERIC_EXP,
            'CARTAO' => self::CARTAO,
            'CHEQUE' => self::CHEQUE,
            'DEPOSITO' => self::DEPOSITO,
            'DINHEIRO' => self::DINHEIRO,
            'DUPLICATA' => self::DUPLICATA,
            'HIPERCARD' => self::HIPERCARD,
            'MAESTRO' => self::MAESTRO,
            'MASTERCARD' => self::MASTERCARD,
            'PIX' => self::PIX,
            'TROCA' => self::TROCA,
            'VISA ELECT' => self::VISA_ELECT,
            'VISA' => self::VISA,
        ];

        return $mapping[$DatabaseValue] ?? null;
    }

    /**
     * Maps a payment method to its corresponding graph color.
     *
     * This method returns the hex color code for the payment method.
     *
     * @return string The hex color code.
     */
    public function mapColor(): string
    {
        $colorMapping = [
            self::AMERIC_EXP->value => GraphColors::YELLOW,
            self::CARTAO->value => GraphColors::INDIGO,
            self::CHEQUE->value => GraphColors::RED,
            self::DINHEIRO->value => GraphColors::BLUE,
            self::DUPLICATA->value => GraphColors::GREEN,
            self::HIPERCARD->value => GraphColors::SLATE,
            self::MAESTRO->value => GraphColors::AMBER,
            self::MASTERCARD->value => GraphColors::VIOLET,
            self::PIX->value => GraphColors::ROSE,
            self::TROCA->value => GraphColors::CYAN,
            self::VISA->value => GraphColors::LIME,
            self::VISA_ELECT->value => GraphColors::GRAY,
            self::DEPOSITO->value => GraphColors::ORANGE,
        ];

        return $colorMapping[$this->value] ?? GraphColors::BLACK;
    }

    /**
     * Converts a database value into the corresponding PaymentMethods graph color.
     *
     * This method is useful when retrieving payment method values from a database
     * and mapping them to the appropriate color.
     *
     * @param  string  $DatabaseValue  The value retrieved from the database.
     * @return string The corresponding color hex code.
     */
    public static function mapToColor(string $DatabaseValue): string
    {
        $paymentMethod = self::mapFromDatabaseValue($DatabaseValue);

        return $paymentMethod ? $paymentMethod->mapColor() : GraphColors::BLACK;
    }
}
