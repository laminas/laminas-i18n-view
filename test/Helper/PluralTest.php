<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\Translator\Plural\Rule;
use Laminas\I18n\View\Helper\Plural as PluralHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PluralTest extends TestCase
{
    /**
     * @return array<array-key, array{0: string, 1: list<string>, 2:int, 3:string}>
     */
    public static function pluralsTestProvider(): array
    {
        return [
            ['nplurals=1; plural=0', ['かさ'], 0, 'かさ'],
            ['nplurals=1; plural=0', ['かさ'], 10, 'かさ'],
            ['nplurals=2; plural=(n==1 ? 0 : 1)', ['umbrella', 'umbrellas'], 0, 'umbrellas'],
            ['nplurals=2; plural=(n==1 ? 0 : 1)', ['umbrella', 'umbrellas'], 1, 'umbrella'],
            ['nplurals=2; plural=(n==1 ? 0 : 1)', ['umbrella', 'umbrellas'], 2, 'umbrellas'],
            ['nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', ['parapluie', 'parapluies'], 0, 'parapluie'],
            ['nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', ['parapluie', 'parapluies'], 1, 'parapluie'],
            ['nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', ['parapluie', 'parapluies'], 2, 'parapluies'],
        ];
    }

    /**
     * @param list<string> $strings
     */
    #[DataProvider('pluralsTestProvider')]
    public function testGetCorrectPlurals(string $pluralRule, array $strings, int $number, string $expected): void
    {
        $helper = new PluralHelper('en-GB', ['en' => Rule::fromString($pluralRule)]);
        $result = $helper->__invoke($strings, $number);
        self::assertSame($expected, $result);
    }

    public function testMultiplePluralRulesCanBeDefinedAndUsed(): void
    {
        $helper = new PluralHelper(
            'en-GB',
            [
                'en' => Rule::fromString('nplurals=2; plural=(n==1 ? 0 : 1)'),
                'fr' => Rule::fromString('nplurals=2; plural=(n>=2 ? 1 : 0)'),
            ],
        );

        self::assertSame('single', $helper->__invoke(['single', 'plural'], 0, 'fr-FR'));
        self::assertSame('single', $helper->__invoke(['single', 'plural'], 1, 'fr-FR'));
        self::assertSame('plural', $helper->__invoke(['single', 'plural'], 2, 'fr-FR'));

        self::assertSame('plural', $helper->__invoke(['single', 'plural'], 0));
        self::assertSame('single', $helper->__invoke(['single', 'plural'], 1));
        self::assertSame('plural', $helper->__invoke(['single', 'plural'], 2));

        self::assertSame('plural', $helper->__invoke(['single', 'plural'], 0, 'en_US'));
        self::assertSame('single', $helper->__invoke(['single', 'plural'], 1, 'en_US'));
        self::assertSame('plural', $helper->__invoke(['single', 'plural'], 2, 'en_US'));

        self::assertSame('plural', $helper->__invoke(['single', 'plural'], 0, 'en-GB'));
        self::assertSame('single', $helper->__invoke(['single', 'plural'], 1, 'en-GB'));
        self::assertSame('plural', $helper->__invoke(['single', 'plural'], 2, 'en-GB'));
    }
}
