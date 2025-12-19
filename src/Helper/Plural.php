<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use Laminas\I18n\Translator\Plural\Rule as PluralRule;

use function is_string;

/**
 * Helper for rendering text based on a count number (like the I18n plural translation helper, but when translation
 * is not needed).
 *
 * Please note that we did not write any hard-coded rules for languages, as languages can evolve, we preferred to
 * let the developer define the rules himself, instead of potentially break applications if we change rules in the
 * future.
 *
 * However, you can find most of the up-to-date plural rules for most languages in those links:
 *      - http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html
 *      - https://developer.mozilla.org/en-US/docs/Localization_and_Plurals
 */
final readonly class Plural
{
    private PluralRule $rule;

    public function __construct(PluralRule|string $rule)
    {
        $this->rule = is_string($rule)
            ? PluralRule::fromString($rule)
            : $rule;
    }

    /**
     * Given an array of strings, a number and, if wanted, an optional locale (the default one is used
     * otherwise), this picks the right string according to plural rules of the locale
     *
     * @param array<int, string> $strings
     */
    public function __invoke(array $strings, int $number): string
    {
        $pluralIndex = $this->rule->evaluate($number);

        return $strings[$pluralIndex];
    }
}
