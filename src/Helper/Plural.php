<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\Translator\Plural\Rule as PluralRule;
use Locale;

use function sprintf;

/**
 * Helper for rendering text based on a count number (like the I18n plural translation helper, but when translation
 * is not needed).
 *
 * Please note that we did not write any hard-coded rules for languages, as languages can evolve, we preferred to
 * let the developer define the rules himself, instead of potentially break applications if we change rules in the
 * future.
 *
 * However, you can find most of the up-to-date plural rules for most languages in those links:
 *      - https://www.unicode.org/cldr/charts/48/supplemental/language_plural_rules.html
 *      - https://developer.mozilla.org/docs/Web/JavaScript/Reference/Global_Objects/Intl/PluralRules
 */
final readonly class Plural
{
    /**
     * @param non-empty-string $defaultLocale
     * @param array<string, PluralRule> $rules
     */
    public function __construct(
        private string $defaultLocale,
        private array $rules,
    ) {
    }

    /**
     * Given an array of strings, a number and, if wanted, an optional locale (the default one is used
     * otherwise), this picks the right string according to plural rules of the locale
     *
     * @param array<int, string> $strings
     * @param non-empty-string|null $locale
     */
    public function __invoke(
        array $strings,
        int $number,
        string|null $locale = null,
    ): string {
        $key = (string) Locale::getPrimaryLanguage($locale ?? $this->defaultLocale);
        if ($key === '' || ! isset($this->rules[$key])) {
            throw new RuntimeException(sprintf(
                'A plural rule has not been defined for the language "%s"',
                $key,
            ));
        }

        $pluralIndex = $this->rules[$key]->evaluate($number);

        return $strings[$pluralIndex] ?? '';
    }
}
