<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\DependencyInjection;

use DWalczyk\SettingBundle\DataTransformerInterface;
use DWalczyk\SettingBundle\Extension\Core\FromConfigurationSettingExtension;
use DWalczyk\SettingBundle\SettingExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @internal
 */
final class BundleExtension extends Extension
{
    public function getAlias(): string
    {
        return 'dwalczyk_setting';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('dwalczyk_setting.data_storage', $config['data_storage']);
        if (isset($config['cache'])) {
            $container->setParameter('dwalczyk_setting.cache', $config['cache']);
        }
        $container->setParameter('dwalczyk_setting.cache_lifetime', $config['cache_lifetime']);

        $def = new Definition(FromConfigurationSettingExtension::class, [$config['definitions']]);
        $def->addTag('dwalczyk_setting.extension');
        $container->setDefinition(FromConfigurationSettingExtension::class, $def);

        $container->registerForAutoconfiguration(SettingExtensionInterface::class)
            ->addTag('dwalczyk_setting.extension')
        ;
        $container->registerForAutoconfiguration(DataTransformerInterface::class)
            ->addTag('dwalczyk_setting.data_transformer')
        ;

        $loader = new YamlFileLoader($container, new FileLocator(\dirname(__DIR__).'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
