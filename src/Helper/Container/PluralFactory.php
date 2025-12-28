<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper\Container;

use Laminas\I18n\I18nDefaults;
use Laminas\I18n\Translator\Plural\Rule;
use Laminas\I18n\View\Helper\Plural;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Locale;
use Psr\Container\ContainerInterface;

use function is_iterable;
use function is_string;
use function iterator_to_array;

/**
 * @internal
 *
 * @psalm-internal Laminas\I18n
 * @psalm-internal Laminas\Test\I18n
 */
final readonly class PluralFactory implements FactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null,
    ): Plural {
        $defaults = $container->get(I18nDefaults::class);
        /** @psalm-var mixed $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $config = is_iterable($config) ? iterator_to_array($config) : [];

        /** @psalm-var mixed $configuredRules */
        $configuredRules = $config['laminas-i18n']['pluralRulesByLanguage'] ?? [];
        $configuredRules = is_iterable($configuredRules) ? iterator_to_array($configuredRules) : [];

        $rules = [];

        foreach ($configuredRules as $languageOrLocale => $ruleString) {
            if (! is_string($languageOrLocale) || ! is_string($ruleString)) {
                continue;
            }

            $language = Locale::getPrimaryLanguage($languageOrLocale);
            $rule     = Rule::fromString($ruleString);
            if ($language === null || $language === '') {
                continue;
            }

            $rules[$language] = $rule;
        }

        return new Plural(
            $defaults->defaultLocale,
            $rules,
        );
    }
}
