<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use function preg_replace;

/**
 * An enum to simplify currency symbol usage in intl message formats for currency
 *
 * The intl extension (ICU) uses the '¤' symbol to represent any currency symbol in a formatting pattern such as this
 * example in `en_GB`: "¤#,##0.00" would yield "£1,234.56", whereas "¤¤#,##0.00" would yield "GBP 1,234.56"
 *
 * @link https://unicode.org/reports/tr35/tr35-numbers.html#Number_Pattern_Character_Definitions
 */
enum CurrencySymbolStyle: string
{
    case None     = 'none';
    case Standard = 'standard';
    case ISOCode  = 'iso';
    case Name     = 'name';

    public function applyTo(string $pattern): string
    {
        $value = match ($this) {
            self::None => '',
            self::Standard => '¤',
            self::ISOCode => '¤¤',
            self::Name => '¤¤¤',
        };

        return (string) preg_replace('/¤+/u', $value, $pattern);
    }
}
