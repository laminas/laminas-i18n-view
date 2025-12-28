<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper\Container;

use Laminas\I18n\I18nDefaults;
use Laminas\I18n\View\Helper\TranslatePlural;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Translator\TranslatorInterface;
use Psr\Container\ContainerInterface;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal Laminas\Test\I18n
 */
final readonly class TranslatePluralFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): TranslatePlural {
        $defaults = $container->get(I18nDefaults::class);

        return new TranslatePlural(
            $container->get(TranslatorInterface::class),
            $defaults->defaultTextDomain,
            $defaults->defaultLocale,
        );
    }
}
