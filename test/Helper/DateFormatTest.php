<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View\Helper;

use DateTimeImmutable;
use DateTimeZone;
use IntlDateFormatter;
use Laminas\I18n\View\Helper\DateFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function assert;

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
     *     4: string,
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
            [null,    IntlDateFormatter::TRADITIONAL, null, null, 'Wednesday, 4 March 2020 at 22:33'],
            ['en_GB', IntlDateFormatter::TRADITIONAL, null, null, 'Wednesday, 4 March 2020 at 22:33'],
            ['de_DE', IntlDateFormatter::TRADITIONAL, null, null, 'Mittwoch, 4. März 2020 um 22:33'],
            ['en_US', IntlDateFormatter::TRADITIONAL, null, null, 'Wednesday, March 4, 2020 at 10:33 PM'],
            // .
            [null,    IntlDateFormatter::FULL, null, null, 'Wednesday, 4 March 2020 at 22:33'],
            ['en_GB', IntlDateFormatter::FULL, null, null, 'Wednesday, 4 March 2020 at 22:33'],
            ['de_DE', IntlDateFormatter::FULL, null, null, 'Mittwoch, 4. März 2020 um 22:33'],
            ['en_US', IntlDateFormatter::FULL, null, null, 'Wednesday, March 4, 2020 at 10:33 PM'],
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
            // .
            [null,    null, IntlDateFormatter::TRADITIONAL, null, '04/03/2020, 22:33:44 Coordinated Universal Time'],
            ['en_GB', null, IntlDateFormatter::TRADITIONAL, null, '04/03/2020, 22:33:44 Coordinated Universal Time'],
            ['de_DE', null, IntlDateFormatter::TRADITIONAL, null, '04.03.20, 22:33:44 Koordinierte Weltzeit'],
            ['en_US', null, IntlDateFormatter::TRADITIONAL, null, '3/4/20, 10:33:44 PM Coordinated Universal Time'],
        ];
    }

    /**
     * @param non-empty-string|null $locale
     */
    #[DataProvider('fixedDateProvider')]
    public function testBasicBehaviourOfDefaults(
        string|null $locale,
        int|null $dateType,
        int|null $timeType,
        string|null $pattern,
        string $expect,
    ): void {
        $helper = new DateFormat(
            'en_GB',
            new DateTimeZone('Europe/London'),
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT,
        );

        self::assertSame(
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

        self::assertSame('Mar 20', $helper->__invoke(date: $this->date, pattern: 'MMM YY'));
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

        self::assertSame(
            $expectFormat,
            $helper->__invoke($now->getTimestamp()),
        );
    }
}
