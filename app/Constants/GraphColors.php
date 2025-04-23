<?php

namespace App\Constants;

/**
 * Class ColorConstants
 *
 * This class provides a centralized and maintainable way to store and reuse a predefined set of colors.
 * Each color is defined as a constant, ensuring immutability and easy access.
 * Colors are based on the Tailwind CSS 400 range.
 */
class GraphColors
{
    /** Black */
    public const BLACK = '#000000';

    /** Red 400 */
    public const RED = '#ff6467';

    /** Orange 400 */
    public const ORANGE = '#ff8904';

    /** Amber 400 */
    public const AMBER = '#ffb900';

    /** Yellow 400 */
    public const YELLOW = '#fdc700';

    /** Lime 400 */
    public const LIME = '#9ae600';

    /** Green 400 */
    public const GREEN = '#05df72';

    /** Emerald 400 */
    public const EMERALD = '#00d492';

    /** Teal 400 */
    public const TEAL = '#00d5be';

    /** Cyan 400 */
    public const CYAN = '#00d3f2';

    /** Sky 400 */
    public const SKY = '#00bcff';

    /** Blue 400 */
    public const BLUE = '#51a2ff';

    /** Indigo 400 */
    public const INDIGO = '#7c86ff';

    /** Violet 400 */
    public const VIOLET = '#a684ff';

    /** Purple 400 */
    public const PURPLE = '#c27aff';

    /** Fuchsia 400 */
    public const FUCHSIA = '#ed6aff';

    /** Pink 400 */
    public const PINK = '#fb64b6';

    /** Rose 700 */
    public const ROSE = '#c60635';

    /** Brown */
    public const BROWN = '#fe9a00';

    /** Slate 400 */
    public const SLATE = '#90a1b9';

    /** Gray 400 */
    public const GRAY = '#99a1af';

    /** Zinc 400 */
    public const ZINC = '#9f9fa9';

    /** Neutral 400 */
    public const NEUTRAL = '#a1a1a1';

    /** Stone 400 */
    public const STONE = '#a6a09b';

    /**
     * Get all Tailwind CSS 400 range colors as an associative array.
     *
     * This method returns an array where the keys are the color names and the values are the corresponding hex codes.
     * Useful for scenarios where you need to iterate over all colors or pass them as a collection.
     *
     * @return array<string, string>
     */
    public static function colors(): array
    {
        return [
            'black' => self::BLACK,

            'yellow' => self::YELLOW,
            'violet' => self::VIOLET,
            'red' => self::RED,
            'blue' => self::BLUE,
            'green' => self::GREEN,
            'slate' => self::SLATE,

            'orange' => self::ORANGE,
            'fuchsia' => self::FUCHSIA,
            'rose' => self::ROSE,
            'cyan' => self::CYAN,
            'lime' => self::LIME,
            'gray' => self::GRAY,

            'amber' => self::AMBER,
            'indigo' => self::INDIGO,
            'pink' => self::PINK,
            'sky' => self::SKY,
            'emerald' => self::EMERALD,
            'neutral' => self::NEUTRAL,

            'stone' => self::STONE,
            'brown' => self::BROWN,
            'purple' => self::PURPLE,
            'teal' => self::TEAL,
            'zinc' => self::ZINC,
        ];
    }

    public static function colorsToArray(): array
    {
        return array_values(self::colors());
    }
}
