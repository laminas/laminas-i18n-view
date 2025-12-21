<?php

declare(strict_types=1);

namespace Laminas\I18n\View;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Money;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final readonly class ConfigProvider
{
    /**
     * @return array{
     *     dependencies: ServiceManagerConfiguration,
     *     view_helpers: ServiceManagerConfiguration,
     *     laminas-i18n?: array{
     *         pluralRulesByLanguage?: array<string, string>,
     *     },
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    Money\Currencies\ISOCurrencies::class => InvokableFactory::class,
                ],
                'aliases'   => [
                    Money\Currencies::class => Money\Currencies\ISOCurrencies::class,
                ],
            ],
            'view_helpers' => $this->viewHelpers(),
            'laminas-i18n' => [
                /**
                 * Used by the Plural view helper
                 */
                'pluralRulesByLanguage' => [
                    // 'en' => 'nplurals=2; plural=(n==1 ? 0 : 1)',
                    // 'fr' => 'nplurals=2; plural=(n==0 || n==1 ? 0 : 1)'
                ],
            ],
        ];
    }

    /** @return ServiceManagerConfiguration */
    private function viewHelpers(): array
    {
        return [
            'factories' => [
                Helper\CountryCodeDataList::class => Helper\Container\CountryCodeDataListFactory::class,
                Helper\CurrencyFormat::class      => Helper\Container\CurrencyFormatFactory::class,
                Helper\DateFormat::class          => Helper\Container\DateFormatFactory::class,
                Helper\NumberFormat::class        => Helper\Container\NumberFormatFactory::class,
                Helper\Plural::class              => Helper\Container\PluralFactory::class,
                Helper\Translate::class           => Helper\Container\TranslateFactory::class,
                Helper\TranslatePlural::class     => Helper\Container\TranslatePluralFactory::class,
            ],
            'aliases'   => [
                'countryCodeDataList' => Helper\CountryCodeDataList::class,
                'currencyFormat'      => Helper\CurrencyFormat::class,
                'dateFormat'          => Helper\DateFormat::class,
                'numberFormat'        => Helper\NumberFormat::class,
                'plural'              => Helper\Plural::class,
                'translate'           => Helper\Translate::class,
                'translatePlural'     => Helper\TranslatePlural::class,
            ],
        ];
    }
}
