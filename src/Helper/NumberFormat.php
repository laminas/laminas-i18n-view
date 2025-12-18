<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use NumberFormatter;

/**
 * View helper for formatting numbers.
 */
final readonly class NumberFormat
{
    /**
     * @param non-empty-string $defaultLocale
     * @param NumberFormatter::TYPE_* $defaultType
     * @param array<int, string> $defaultTextAttributes
     */
    public function __construct(
        private string $defaultLocale,
        private int|null $defaultDecimalPrecision = null,
        private int $defaultStyle = NumberFormatter::DEFAULT_STYLE,
        private int $defaultType = NumberFormatter::TYPE_DEFAULT,
        private array $defaultTextAttributes = [],
    ) {
    }

    /**
     * Format a number
     *
     * @param non-empty-string|null $locale
     * @param NumberFormatter::TYPE_* $formatType
     * @param array<int, string>|null $textAttributes
     */
    public function __invoke(
        int|float $number,
        string|null $locale = null,
        int|null $formatStyle = null,
        int|null $formatType = null,
        int|null $decimals = null,
        array|null $textAttributes = null,
    ): string {
        $formatter = new NumberFormatter(
            $locale ?? $this->defaultLocale,
            $formatStyle ?? $this->defaultStyle,
        );

        $precision = $decimals ?? $this->defaultDecimalPrecision;
        if ($precision !== null) {
            $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $precision);
            $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $precision);
        }

        $attributes = $textAttributes ?? $this->defaultTextAttributes;

        foreach ($attributes as $textAttribute => $value) {
            $formatter->setTextAttribute($textAttribute, $value);
        }

        return $formatter->format($number, $formatType ?? $this->defaultType);
    }
}
