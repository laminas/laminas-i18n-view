<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use Laminas\I18n\View\Helper\CurrencyFormat;
use Laminas\I18n\View\Helper\CurrencySymbolStyle;
use Money\Currencies\ISOCurrencies;
use Money\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

final class CurrencyFormatTest extends TestCase
{
    public function testInvokeReturnsSelf(): CurrencyFormat
    {
        $helper = new CurrencyFormat(
            'en_GB',
            'GBP',
            new ISOCurrencies(),
            CurrencySymbolStyle::Standard,
        );

        self::assertSame($helper, $helper->__invoke());

        return $helper;
    }

    #[Depends('testInvokeReturnsSelf')]
    public function testMinimumBasicUsageWithConfiguredDefaults(CurrencyFormat $helper): void
    {
        self::assertSame('£123.45', $helper->minorUnit(12345));
        self::assertSame('£123.45', $helper->amount(123.45));
        self::assertSame('£123.45', $helper->money(Money::GBP(12345)));
    }

    /**
     * @return list<array{
     *     0: int,
     *     1: non-empty-string|null,
     *     2: non-empty-string|null,
     *     3: bool,
     *     4: string,
     * }>
     */
    public static function minorUnitProvider(): array
    {
        return [
            [12345,     null,  null,    false, '£123.45'],
            [2,         null,  null,    false, '£0.02'],
            [0,         null,  null,    false, '£0.00'],
            [123,       null,  null,    false, '£1.23'],
            [123456789, null,  null,    false, '£1,234,567.89'],
            [12345,     'EUR', null,    false, '€123.45'],
            [12345,     'GBP', 'de_DE', false, '123,45 £'],
            // .
            [12345,     null,  null,    true,  '£123'],
            [2,         null,  null,    true,  '£0'],
            [0,         null,  null,    true,  '£0'],
            [123,       null,  null,    true,  '£1'],
            [123456789, null,  null,    true,  '£1,234,568'],
            [12345,     'EUR', null,    true,  '€123'],
            [12345,     'GBP', 'de_DE', true,  '123 £'],
            // .
            [12345,     'JPY',  null,    false, 'JP¥12,345'],
            [2,         'JPY',  null,    false, 'JP¥2'],
            [0,         'JPY',  null,    false, 'JP¥0'],
            [123,       'JPY',  null,    false, 'JP¥123'],
            [123456789, 'JPY',  null,    false, 'JP¥123,456,789'],
            [12345,     'JPY',  null,    false, 'JP¥12,345'],
            [12345,     'JPY', 'de_DE',  false, '12.345 ¥'],
            // .
            [12345,     'JPY',  null,    true, 'JP¥12,345'],
            [2,         'JPY',  null,    true, 'JP¥2'],
            [0,         'JPY',  null,    true, 'JP¥0'],
            [123,       'JPY',  null,    true, 'JP¥123'],
            [123456789, 'JPY',  null,    true, 'JP¥123,456,789'],
            [12345,     'JPY',  null,    true, 'JP¥12,345'],
            [12345,     'JPY', 'de_DE',  true, '12.345 ¥'],
        ];
    }

    /**
     * @param non-empty-string|null $currency
     * @param non-empty-string|null $locale
     */
    #[DataProvider('minorUnitProvider')]
    public function testFormatMinorUnit(
        int $amount,
        string|null $currency,
        string|null $locale,
        bool $truncateDecimals,
        string $expect,
    ): void {
        $helper = new CurrencyFormat('en_GB', 'GBP', new ISOCurrencies(), CurrencySymbolStyle::Standard);
        self::assertSame(
            $expect,
            $helper->minorUnit($amount, $currency, $locale, $truncateDecimals),
        );
    }

    /**
     * @return list<array{
     *     0: int|float,
     *     1: non-empty-string|null,
     *     2: non-empty-string|null,
     *     3: bool,
     *     4: string,
     * }>
     */
    public static function majorUnitProvider(): array
    {
        return [
            [123.45,     null,  null,    false, '£123.45'],
            [12345,      null,  null,    false, '£12,345.00'],
            [0.02,       null,  null,    false, '£0.02'],
            [2,          null,  null,    false, '£2.00'],
            [0.00,       null,  null,    false, '£0.00'],
            [0,          null,  null,    false, '£0.00'],
            [1.23,       null,  null,    false, '£1.23'],
            [1234567.89, null,  null,    false, '£1,234,567.89'],
            [123.45,     'EUR', null,    false, '€123.45'],
            [123.45,     'GBP', 'de_DE', false, '123,45 £'],
            // .
            [123.45,     null,  null,    true, '£123'],
            [12345,      null,  null,    true, '£12,345'],
            [0.02,       null,  null,    true, '£0'],
            [2,          null,  null,    true, '£2'],
            [0.00,       null,  null,    true, '£0'],
            [0,          null,  null,    true, '£0'],
            [1.23,       null,  null,    true, '£1'],
            [1234567.89, null,  null,    true, '£1,234,568'],
            [123.45,     'EUR', null,    true, '€123'],
            [123.45,     'GBP', 'de_DE', true, '123 £'],
            // .
            [12345,     'JPY',  null,    false, 'JP¥12,345'],
            [2,         'JPY',  null,    false, 'JP¥2'],
            [0,         'JPY',  null,    false, 'JP¥0'],
            [123,       'JPY',  null,    false, 'JP¥123'],
            [123456789, 'JPY',  null,    false, 'JP¥123,456,789'],
            [12345,     'JPY',  null,    false, 'JP¥12,345'],
            [12345,     'JPY', 'de_DE',  false, '12.345 ¥'],
            // .
            [12345,     'JPY',  null,    true, 'JP¥12,345'],
            [2,         'JPY',  null,    true, 'JP¥2'],
            [0,         'JPY',  null,    true, 'JP¥0'],
            [123,       'JPY',  null,    true, 'JP¥123'],
            [123456789, 'JPY',  null,    true, 'JP¥123,456,789'],
            [12345,     'JPY',  null,    true, 'JP¥12,345'],
            [12345,     'JPY', 'de_DE',  true, '12.345 ¥'],
        ];
    }

    /**
     * @param non-empty-string|null $currency
     * @param non-empty-string|null $locale
     */
    #[DataProvider('majorUnitProvider')]
    public function testFormatMajorUnit(
        int|float $amount,
        string|null $currency,
        string|null $locale,
        bool $truncateDecimals,
        string $expect,
    ): void {
        $helper = new CurrencyFormat('en_GB', 'GBP', new ISOCurrencies(), CurrencySymbolStyle::Standard);
        self::assertSame(
            $expect,
            $helper->amount($amount, $currency, $locale, $truncateDecimals),
        );
    }

    /**
     * @return list<array{
     *     0: Money,
     *     1: non-empty-string|null,
     *     2: bool,
     *     3: string,
     * }>
     */
    public static function moneyProvider(): array
    {
        return [
            [Money::GBP(1),   null,    false, '£0.01'],
            [Money::GBP(100), null,    false, '£1.00'],
            [Money::GBP(1),   null,    true,  '£0'],
            [Money::GBP(100), null,    true,  '£1'],
            [Money::GBP(1),   'de_DE', false, '0,01 £'],
            [Money::GBP(100), 'de_DE', false, '1,00 £'],
            [Money::GBP(1),   'de_DE', true,  '0 £'],
            [Money::GBP(100), 'de_DE', true,  '1 £'],
        ];
    }

    /**
     * @param non-empty-string|null $locale
     */
    #[DataProvider('moneyProvider')]
    public function testFormatMoney(
        Money $amount,
        string|null $locale,
        bool $truncateDecimals,
        string $expect,
    ): void {
        $helper = new CurrencyFormat('en_GB', 'GBP', new ISOCurrencies(), CurrencySymbolStyle::Standard);
        self::assertSame(
            $expect,
            $helper->money($amount, $locale, $truncateDecimals),
        );
    }

    /**
     * @return list<array{
     *     0: int,
     *     1: non-empty-string|null,
     *     2: non-empty-string|null,
     *     3: CurrencySymbolStyle,
     *     4: string,
     * }>
     */
    public static function symbolStyleProvider(): array
    {
        return [
            [12345, null, null, CurrencySymbolStyle::None,     '123.45'],
            [12345, null, null, CurrencySymbolStyle::Standard, '£123.45'],
            [12345, null, null, CurrencySymbolStyle::ISOCode,  'GBP 123.45'],
            [12345, null, null, CurrencySymbolStyle::Name,     'British pounds 123.45'],
            // .
            [12345, null, 'de_DE', CurrencySymbolStyle::None,     '123,45'],
            [12345, null, 'de_DE', CurrencySymbolStyle::Standard, '123,45 £'],
            [12345, null, 'de_DE', CurrencySymbolStyle::ISOCode,  '123,45 GBP'],
            [12345, null, 'de_DE', CurrencySymbolStyle::Name,     '123,45 Britische Pfund'],
            // .
            [12345, 'JPY', 'de_DE', CurrencySymbolStyle::None,     '12.345'],
            [12345, 'JPY', 'de_DE', CurrencySymbolStyle::Standard, '12.345 ¥'],
            [12345, 'JPY', 'de_DE', CurrencySymbolStyle::ISOCode,  '12.345 JPY'],
            [12345, 'JPY', 'de_DE', CurrencySymbolStyle::Name,     '12.345 Japanische Yen'],
        ];
    }

    /**
     * @param non-empty-string|null $currency
     * @param non-empty-string|null $locale
     */
    #[DataProvider('symbolStyleProvider')]
    public function testSymbolStyleChanges(
        int $amount,
        string|null $locale,
        string|null $currency,
        CurrencySymbolStyle $style,
        string $expect,
    ): void {
        $helper = new CurrencyFormat('en_GB', 'GBP', new ISOCurrencies(), CurrencySymbolStyle::Standard);
        self::assertSame(
            $expect,
            $helper->minorUnit($amount, $locale, $currency, false, $style),
        );
    }
}
