<?php

declare(strict_types=1);

namespace Atoolo\Translator;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
class AtooloTranslatorBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $configDir = __DIR__ . '/../config';

        $loader = new GlobFileLoader(new FileLocator($configDir));
        $loader->setResolver(
            new LoaderResolver(
                [
                    new YamlFileLoader($container, new FileLocator($configDir)),
                ],
            ),
        );

        $loader->load('services.yaml');
    }
}
