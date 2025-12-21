<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use IntlDateFormatter;

use function assert;
use function is_int;

/**
 * View helper for formatting dates.
 */
final readonly class DateFormat
{
    /**
     * @param non-empty-string $defaultLocale
     * @param IntlDateFormatter::GREGORIAN|IntlDateFormatter::TRADITIONAL $calendarType
     */
    public function __construct(
        private string $defaultLocale,
        private DateTimeZone $defaultTimeZone,
        private int $defaultDateType,
        private int $defaultTimeType,
        private int $calendarType = IntlDateFormatter::GREGORIAN,
    ) {
    }

    /**
     * Format a date
     *
     * @param non-empty-string|null $locale
     */
    public function __invoke(
        DateTimeInterface|int $date,
        string|null $locale = null,
        int|null $dateType = null,
        int|null $timeType = null,
        string|null $pattern = null
    ): string {
        if (is_int($date)) {
            $date = DateTimeImmutable::createFromFormat('U', (string) $date);
            assert($date instanceof DateTimeInterface);
            $date = $date->setTimezone($this->defaultTimeZone);
        }

        $formatter = new IntlDateFormatter(
            $locale ?? $this->defaultLocale,
            $dateType ?? $this->defaultDateType,
            $timeType ?? $this->defaultTimeType,
            $date->getTimezone(),
            $this->calendarType,
            $pattern ?? ''
        );

        return $formatter->format($date);
    }
}
