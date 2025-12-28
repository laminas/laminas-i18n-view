<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Internal;

use function assert;
use function function_exists;
use function is_string;
use function mb_trim;
use function preg_quote;
use function preg_replace;
use function sprintf;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n\View
 * @psalm-internal LaminasTest\I18n\View
 */
final readonly class Polyfill
{
    public static function mbTrim(string $input): string
    {
        if (function_exists('mb_trim')) {
            return mb_trim($input);
        }

        $characters = "\f\n\r\t\v\u{00A0}\u{1680}\u{2000}\u{2001}\u{2002}\u{2003}\u{2004}\u{2005}\u{2006}\u{2007}\u{2008}\u{2009}\u{200A}\u{2028}\u{2029}\u{202F}\u{205F}\u{3000}\u{0085}\u{180E}"; // phpcs:ignore
        $regex      = sprintf('{^[%1$s]+|[%1$s]+$}Du', preg_quote($characters));
        $result     = preg_replace($regex, '', $input);
        assert(is_string($result));

        return $result;
    }
}
