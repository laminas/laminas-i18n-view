<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper\Container;

use Laminas\I18n\I18nDefaults;
use Laminas\I18n\View\Helper\CurrencyFormat;
use Laminas\I18n\View\Helper\CurrencySymbolStyle;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Money\Currencies;
use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal Laminas\Test\I18n
 */
final readonly class CurrencyFormatFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): CurrencyFormat {
        $defaults = $container->get(I18nDefaults::class);

        return new CurrencyFormat(
            $defaults->defaultLocale,
            $defaults->defaultCurrencyCode,
            $container->get(Currencies::class),
            CurrencySymbolStyle::Standard,
        );
    }
}
