<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper\Container;

use IntlDateFormatter;
use Laminas\I18n\I18nDefaults;
use Laminas\I18n\View\Helper\DateFormat;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal Laminas\Test\I18n
 */
final readonly class DateFormatFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): DateFormat {
        $defaults = $container->get(I18nDefaults::class);

        return new DateFormat(
            $defaults->defaultLocale,
            $defaults->defaultTimeZone,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            IntlDateFormatter::GREGORIAN,
        );
    }
}
