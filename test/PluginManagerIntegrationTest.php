<?php

declare(strict_types=1);

namespace LaminasTest\I18n\View;

use Laminas\I18n\ConfigProvider as I18nConfigProvider;
use Laminas\I18n\View\ConfigProvider;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\ConfigProvider as ViewConfigProvider;
use Laminas\View\HelperPluginManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function array_replace_recursive;
use function assert;
use function class_exists;
use function count;

/** @psalm-import-type ServiceManagerConfiguration from ServiceManager */
final class PluginManagerIntegrationTest extends TestCase
{
    private function getPluginManager(): HelperPluginManagerInterface
    {
        $config = array_replace_recursive(
            (new ViewConfigProvider())->__invoke(),
            (new I18nConfigProvider())->__invoke(),
            (new ConfigProvider())->__invoke(),
        );

        /** @psalm-var ServiceManagerConfiguration $dependencies */
        $dependencies                       = $config['dependencies'];
        $dependencies['services']         ??= [];
        $dependencies['services']['config'] = $config;

        /** @psalm-var ServiceManagerConfiguration $dependencies */
        $container = new ServiceManager($dependencies);

        return $container->get(HelperPluginManagerInterface::class);
    }

    /** @return iterable<string, array{0: string, 1: class-string}> */
    public static function aliasProvider(): iterable
    {
        $aliases = (new ConfigProvider())->__invoke()['view_helpers']['aliases'] ?? [];
        self::assertGreaterThan(0, count($aliases));

        foreach ($aliases as $alias => $class) {
            assert(class_exists($class));

            yield $alias => [$alias, $class];
        }
    }

    /** @param class-string $class */
    #[DataProvider('aliasProvider')]
    public function testAliasResolvesToInstance(string $alias, string $class): void
    {
        $plugins = $this->getPluginManager();
        self::assertInstanceOf($class, $plugins->get($alias));
    }
}
