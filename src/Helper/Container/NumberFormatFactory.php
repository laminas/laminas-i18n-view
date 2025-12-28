<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper\Container;

use Laminas\I18n\DefaultLocale;
use Laminas\I18n\View\Helper\NumberFormat;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal Laminas\Test\I18n
 */
final readonly class NumberFormatFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): NumberFormat {
        return new NumberFormat(
            $container->get(DefaultLocale::class)->locale,
        );
    }
}
