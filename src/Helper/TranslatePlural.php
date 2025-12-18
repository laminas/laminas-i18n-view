<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use Laminas\Translator\TranslatorInterface;

/**
 * View helper for translating plural messages.
 */
final readonly class TranslatePlural
{
    /**
     * @param non-empty-string $defaultTextDomain
     * @param non-empty-string $defaultLocale
     */
    public function __construct(
        private TranslatorInterface $translator,
        private string $defaultTextDomain,
        private string $defaultLocale,
    ) {
    }

    /**
     * Translate a plural message
     *
     * @param non-empty-string|null $textDomain
     * @param non-empty-string|null $locale
     */
    public function __invoke(
        string $singular,
        string $plural,
        int $number,
        string|null $textDomain = null,
        string|null $locale = null
    ): string {
        return $this->translator->translatePlural(
            $singular,
            $plural,
            $number,
            $textDomain ?? $this->defaultTextDomain,
            $locale ?? $this->defaultLocale,
        );
    }
}
