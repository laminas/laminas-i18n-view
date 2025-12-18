<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use function preg_replace;

enum CurrencySymbolStyle
{
    case None;
    case Standard;
    case ISOCode;
    case Name;

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
