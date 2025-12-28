<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Internal;

use Laminas\I18n\View\Internal\Polyfill;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class PolyfillTest extends TestCase
{
    /** @return list<array{0: string}> */
    public static function characterListProvider(): array
    {
        return [
            [' '],
            ["\f"],
            ["\n"],
            ["\r"],
            ["\t"],
            ["\v"],
            ["\u{00A0}"],
            ["\u{1680}"],
            ["\u{2000}"],
            ["\u{2001}"],
            ["\u{2002}"],
            ["\u{2003}"],
            ["\u{2004}"],
            ["\u{2005}"],
            ["\u{2006}"],
            ["\u{2007}"],
            ["\u{2008}"],
            ["\u{2009}"],
            ["\u{200A}"],
            ["\u{2028}"],
            ["\u{2029}"],
            ["\u{202F}"],
            ["\u{205F}"],
            ["\u{3000}"],
            ["\u{0085}"],
            ["\u{180E}"],
        ];
    }

    #[DataProvider('characterListProvider')]
    public function testDefaultCharactersAreTrimmed(string $character): void
    {
        $expect = sprintf('foo%sfoo', $character);
        $input  = sprintf('%s%s%s', $character, $expect, $character);

        $result = Polyfill::mbTrim($input);
        self::assertSame($expect, $result);
    }
}
