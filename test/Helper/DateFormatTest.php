<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;
use Laminas\I18n\View\Helper\DateFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_map;
use function assert;
use function is_array;
use function str_replace;

final class DateFormatTest extends TestCase
{
    private DateTimeImmutable $date;

    protected function setUp(): void
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s T', '2020-03-04 22:33:44 UTC');
        assert($date !== false);

        $this->date = $date;
    }

    /**
     * @return list<array{
     *     0: non-empty-string|null,
     *     1: int|null,
     *     2: int|null,
     *     3: string|null,
     *     4: string|list<string>,
     * }>
     */
    public static function fixedDateProvider(): array
    {
        return [
            [null,    null, null, null, '04/03/2020, 22:33'],
            ['en_GB', null, null, null, '04/03/2020, 22:33'],
            ['de_DE', null, null, null, '04.03.20, 22:33'],
            ['en_US', null, null, null, '3/4/20, 10:33 PM'],
            // .
            [null,    IntlDateFormatter::SHORT, null, null, '04/03/2020, 22:33'],
            ['en_GB', IntlDateFormatter::SHORT, null, null, '04/03/2020, 22:33'],
            ['de_DE', IntlDateFormatter::SHORT, null, null, '04.03.20, 22:33'],
            ['en_US', IntlDateFormatter::SHORT, null, null, '3/4/20, 10:33 PM'],
            // .
            [null,    IntlDateFormatter::NONE, null, null, '22:33'],
            ['en_GB', IntlDateFormatter::NONE, null, null, '22:33'],
            ['de_DE', IntlDateFormatter::NONE, null, null, '22:33'],
            ['en_US', IntlDateFormatter::NONE, null, null, '10:33 PM'],
            // .
            [null,    IntlDateFormatter::MEDIUM, null, null, '4 Mar 2020, 22:33'],
            ['en_GB', IntlDateFormatter::MEDIUM, null, null, '4 Mar 2020, 22:33'],
            ['de_DE', IntlDateFormatter::MEDIUM, null, null, '04.03.2020, 22:33'],
            ['en_US', IntlDateFormatter::MEDIUM, null, null, 'Mar 4, 2020, 10:33 PM'],
            // .
            [null,    IntlDateFormatter::LONG, null, null, '4 March 2020 at 22:33'],
            ['en_GB', IntlDateFormatter::LONG, null, null, '4 March 2020 at 22:33'],
            ['de_DE', IntlDateFormatter::LONG, null, null, '4. März 2020 um 22:33'],
            ['en_US', IntlDateFormatter::LONG, null, null, 'March 4, 2020 at 10:33 PM'],
            // .
            [
                null,
                IntlDateFormatter::FULL,
                null,
                null,
                [
                    'Wednesday, 4 March 2020 at 22:33',
                    'Wednesday 4 March 2020 at 22:33',
                ],
            ],
            [
                'en_GB',
                IntlDateFormatter::FULL,
                null,
                null,
                [
                    'Wednesday, 4 March 2020 at 22:33',
                    'Wednesday 4 March 2020 at 22:33',
                ],
            ],
            ['de_DE', IntlDateFormatter::FULL, null, null, 'Mittwoch, 4. März 2020 um 22:33'],
            ['en_US', IntlDateFormatter::FULL, null, null, 'Wednesday, March 4, 2020 at 10:33 PM'],
            // .
            [null,    null, IntlDateFormatter::NONE, null, '04/03/2020'],
            ['en_GB', null, IntlDateFormatter::NONE, null, '04/03/2020'],
            ['de_DE', null, IntlDateFormatter::NONE, null, '04.03.20'],
            ['en_US', null, IntlDateFormatter::NONE, null, '3/4/20'],
            // .
            [null,    null, IntlDateFormatter::SHORT, null, '04/03/2020, 22:33'],
            ['en_GB', null, IntlDateFormatter::SHORT, null, '04/03/2020, 22:33'],
            ['de_DE', null, IntlDateFormatter::SHORT, null, '04.03.20, 22:33'],
            ['en_US', null, IntlDateFormatter::SHORT, null, '3/4/20, 10:33 PM'],
            // .
            [null,    null, IntlDateFormatter::MEDIUM, null, '04/03/2020, 22:33:44'],
            ['en_GB', null, IntlDateFormatter::MEDIUM, null, '04/03/2020, 22:33:44'],
            ['de_DE', null, IntlDateFormatter::MEDIUM, null, '04.03.20, 22:33:44'],
            ['en_US', null, IntlDateFormatter::MEDIUM, null, '3/4/20, 10:33:44 PM'],
            // .
            [null,    null, IntlDateFormatter::LONG, null, '04/03/2020, 22:33:44 UTC'],
            ['en_GB', null, IntlDateFormatter::LONG, null, '04/03/2020, 22:33:44 UTC'],
            ['de_DE', null, IntlDateFormatter::LONG, null, '04.03.20, 22:33:44 UTC'],
            ['en_US', null, IntlDateFormatter::LONG, null, '3/4/20, 10:33:44 PM UTC'],
            // .
            [null,    null, IntlDateFormatter::FULL, null, '04/03/2020, 22:33:44 Coordinated Universal Time'],
            ['en_GB', null, IntlDateFormatter::FULL, null, '04/03/2020, 22:33:44 Coordinated Universal Time'],
            ['de_DE', null, IntlDateFormatter::FULL, null, '04.03.20, 22:33:44 Koordinierte Weltzeit'],
            ['en_US', null, IntlDateFormatter::FULL, null, '3/4/20, 10:33:44 PM Coordinated Universal Time'],
        ];
    }

    /**
     * @param non-empty-string|null $locale
     * @param string|list<string> $expect
     */
    #[DataProvider('fixedDateProvider')]
    public function testBasicBehaviourOfDefaults(
        string|null $locale,
        int|null $dateType,
        int|null $timeType,
        string|null $pattern,
        string|array $expect,
    ): void {
        $helper = new DateFormat(
            'en_GB',
            new DateTimeZone('Europe/London'),
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT,
        );

        self::assertMbSame(
            $expect,
            $helper->__invoke(
                $this->date,
                $locale,
                $dateType,
                $timeType,
                $pattern,
            ),
        );
    }

    public function testPatternOverride(): void
    {
        $helper = new DateFormat(
            'en_GB',
            new DateTimeZone('Europe/London'),
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT,
        );

        self::assertMbSame('Mar 20', $helper->__invoke(date: $this->date, pattern: 'MMM YY'));
    }

    public function testIntegerDateIsTreatedAsTimestampInTheDefaultTimeZone(): void
    {
        $helper = new DateFormat(
            'en_GB',
            new DateTimeZone('America/New_York'),
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT,
        );

        $now = DateTimeImmutable::createFromFormat('Y-m-d H:i:s T', '2020-01-01 10:33:44 UTC');
        self::assertNotFalse($now);
        $expect       = $now->setTimezone(new DateTimeZone('America/New_York'));
        $expectFormat = $helper->__invoke($expect);

        self::assertMbSame(
            $expectFormat,
            $helper->__invoke($now->getTimestamp()),
        );
    }

    public function testCalendarType(): void
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', '0-12-25');
        self::assertNotFalse($date);
        $pattern = 'YYYY G';

        $helper = new DateFormat(
            'en_GB',
            new DateTimeZone('Europe/London'),
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            IntlDateFormatter::GREGORIAN,
        );

        $value = $helper->__invoke(date: $date, pattern: $pattern);
        self::assertMbSame('0000 BC', $value);

        $helper = new DateFormat(
            'en_GB@calendar=buddhist',
            new DateTimeZone('Europe/London'),
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            IntlDateFormatter::TRADITIONAL,
        );

        $value = $helper->__invoke(date: $date, pattern: $pattern);
        self::assertMbSame('0000 BE', $value);
    }

    /**
     * Assert 2 strings are identical, ignoring multibyte whitespace (NBSP et al)
     *
     * Different versions of intl/icu may use slightly different patterns, or use non-breaking-spaces rather than ascii
     * spaces for example… This method is intended to smooth out those minor differences during comparison
     *
     * @param string|list<string> $expect
     */
    public static function assertMbSame(string|array $expect, string $actual, string $message = ''): void
    {
        $replace = static fn(string $value): string => str_replace(["\u{00A0}", "\u{202F}"], ' ', $value);

        $expect = is_array($expect) ? $expect : [$expect];
        $expect = array_map($replace, $expect);

        self::assertContains(
            $replace($actual),
            $expect,
            $message,
        );
    }
}
