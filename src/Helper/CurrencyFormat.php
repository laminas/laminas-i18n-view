<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\View\Internal\Polyfill;
use Money\Currencies;
use Money\Currency;
use Money\Money;
use NumberFormatter;

use function sprintf;
use function str_pad;
use function strlen;
use function substr;

use const STR_PAD_LEFT;

/**
 * View helper for formatting currency.
 */
final readonly class CurrencyFormat
{
    /**
     * @param non-empty-string $defaultLocale
     * @param non-empty-string $defaultCurrency
     */
    public function __construct(
        private string $defaultLocale,
        private string $defaultCurrency,
        private Currencies $currencies,
        private CurrencySymbolStyle $symbolStyle,
    ) {
    }

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * Format a Money instance
     *
     * @param non-empty-string|null $locale
     */
    public function money(
        Money $amount,
        string|null $locale = null,
        bool $truncateDecimals = false,
        CurrencySymbolStyle|null $symbolStyle = null,
    ): string {
        return $this->minorUnit(
            (int) $amount->getAmount(),
            $amount->getCurrency()->getCode(),
            $locale,
            $truncateDecimals,
            $symbolStyle,
        );
    }

    /**
     * @param int $amount The amount in minor currency units, i.e. cents or pence
     * @param non-empty-string|null $currencyCode
     * @param non-empty-string|null $locale
     */
    public function minorUnit(
        int $amount,
        string|null $currencyCode = null,
        string|null $locale = null,
        bool $truncateDecimals = false,
        CurrencySymbolStyle|null $symbolStyle = null,
    ): string {
        $currency  = new Currency($currencyCode ?? $this->defaultCurrency);
        $subunit   = $this->currencies->subunitFor($currency);
        $formatter = new NumberFormatter(
            $locale ?? $this->defaultLocale,
            NumberFormatter::CURRENCY,
        );

        $symbolStyle ??= $this->symbolStyle;
        $formatter->setPattern($symbolStyle->applyTo($formatter->getPattern()));

        if (strlen((string) $amount) > $subunit) {
            $minor = substr((string) $amount, 0 - $subunit);
            $major = substr((string) $amount, 0, strlen((string) $amount) - $subunit);
            $float = (float) sprintf('%s.%s', $major, $minor);
        } else {
            $float = (float) sprintf('0.%s', str_pad((string) $amount, $subunit, '0', STR_PAD_LEFT));
        }

        if ($truncateDecimals) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        } else {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $subunit);
        }

        return Polyfill::mbTrim($formatter->formatCurrency($float, $currency->getCode()));
    }

    /**
     * @param int|float $amount The amount in major currency units, i.e. pounds or dollars
     * @param non-empty-string|null $currencyCode
     * @param non-empty-string|null $locale
     */
    public function amount(
        int|float $amount,
        string|null $currencyCode = null,
        string|null $locale = null,
        bool $truncateDecimals = false,
        CurrencySymbolStyle|null $symbolStyle = null,
    ): string {
        $currency  = new Currency($currencyCode ?? $this->defaultCurrency);
        $subunit   = $this->currencies->subunitFor($currency);
        $formatter = new NumberFormatter(
            $locale ?? $this->defaultLocale,
            NumberFormatter::CURRENCY,
        );

        $symbolStyle ??= $this->symbolStyle;
        $formatter->setPattern($symbolStyle->applyTo($formatter->getPattern()));

        if ($truncateDecimals) {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        } else {
            $formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $subunit);
        }

        return $formatter->formatCurrency($amount, $currency->getCode());
    }
}
