<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\NumberFormat as NumberFormatHelper;
use Locale;
use NumberFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function str_replace;

final class NumberFormatTest extends TestCase
{
    private NumberFormatHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new NumberFormatHelper(
            'en_GB',
            null,
            NumberFormatter::DECIMAL,
            NumberFormatter::TYPE_DOUBLE,
            [],
        );
    }

    /**
     * @return list<array{
     *     0: non-empty-string|null,
     *     1: int,
     *     2: NumberFormatter::TYPE_*,
     *     3: int|null,
     *     4: array<int, string>,
     *     5: float,
     *     6: string,
     * }>
     */
    public static function numberFormatProvider(): array
    {
        return [
            [
                'de_DE',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '1.234.567,891',
            ],
            [
                'de_DE',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                6,
                [],
                1234567.891234567890000,
                '1.234.567,891235',
            ],
            [
                'de_DE',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '123.456.789 %',
            ],
            [
                'de_DE',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                1,
                [],
                1234567.891234567890000,
                '123.456.789,1 %',
            ],
            [
                'de_DE',
                NumberFormatter::SCIENTIFIC,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234560000,
                '1,23456789123456E6',
            ],
            [
                'ru_RU',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '1 234 567,891',
            ],
            [
                'ru_RU',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '123 456 789 %',
            ],
            [
                'ru_RU',
                NumberFormatter::SCIENTIFIC,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234560000,
                '1,23456789123456E6',
            ],
            [
                'en_US',
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '1,234,567.891',
            ],
            [
                'en_US',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234567890000,
                '123,456,789%',
            ],
            [
                'en_US',
                NumberFormatter::SCIENTIFIC,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                1234567.891234560000,
                '1.23456789123456E6',
            ],
            [
                'en_US',
                NumberFormatter::PERCENT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [
                    NumberFormatter::NEGATIVE_PREFIX => 'MINUS',
                ],
                -1234567.891234567890000,
                'MINUS123,456,789%',
            ],
            [
                null,
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                123.45,
                '123.45',
            ],
            [
                null,
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                123,
                '123',
            ],
            [
                null,
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_INT64,
                null,
                [],
                123.45,
                '123',
            ],
            [
                null,
                NumberFormatter::DECIMAL,
                NumberFormatter::TYPE_INT64,
                null,
                [],
                123,
                '123',
            ],
            [
                null,
                NumberFormatter::ORDINAL,
                NumberFormatter::TYPE_INT64,
                null,
                [],
                123,
                '123rd',
            ],
            [
                null,
                NumberFormatter::SPELLOUT,
                NumberFormatter::TYPE_DOUBLE,
                null,
                [],
                123.45,
                'onehundredtwenty-threepointfourfive',
            ],
            [
                null,
                NumberFormatter::DURATION,
                NumberFormatter::TYPE_INT32,
                null,
                [],
                123,
                '2:03',
            ],
            [
                null,
                NumberFormatter::DURATION,
                NumberFormatter::TYPE_INT32,
                null,
                [],
                3601,
                '1:00:01',
            ],
            [
                null,
                NumberFormatter::DURATION,
                NumberFormatter::TYPE_INT32,
                null,
                [],
                86401,
                '24:00:01',
            ],
        ];
    }

    /**
     * @param non-empty-string|null $locale
     * @param NumberFormatter::TYPE_* $formatType
     * @param array<int, string> $textAttributes
     */
    #[DataProvider('numberFormatProvider')]
    public function testBasic(
        string|null $locale,
        int $formatStyle,
        int $formatType,
        int|null $decimals,
        array $textAttributes,
        float $number,
        string $expected
    ): void {
        self::assertMbStringSame($expected, $this->helper->__invoke(
            $number,
            $locale,
            $formatStyle,
            $formatType,
            $decimals,
            $textAttributes
        ));
    }

    /**
     * @param non-empty-string|null $locale
     * @param NumberFormatter::TYPE_* $formatType
     * @param array<int, string> $textAttributes
     */
    #[DataProvider('numberFormatProvider')]
    public function testSettersProvideDefaults(
        string|null $locale,
        int $formatStyle,
        int $formatType,
        ?int $decimals,
        array $textAttributes,
        float $number,
        string $expected
    ): void {
        $helper = new NumberFormatHelper(
            $locale ?? 'en_GB',
            $decimals,
            $formatStyle,
            $formatType,
            $textAttributes,
        );

        self::assertMbStringSame($expected, $helper->__invoke($number));
    }

    public static function assertMbStringSame(string $expected, string $test, string $message = ''): void
    {
        $expected = str_replace(["\xC2\xA0", ' '], '', $expected);
        $test     = str_replace(["\xC2\xA0", ' '], '', $test);
        self::assertSame($expected, $test, $message);
    }
}
