<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\DependencyInjection;

use DWalczyk\SettingBundle\Extension\Debug\TraceableSettings;
use DWalczyk\SettingBundle\Settings;
use DWalczyk\SettingBundle\SettingsInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 */
final class CustomPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $cacheParam = $container->getParameter('dwalczyk_setting.cache');
        $dataStorageParam = $container->getParameter('dwalczyk_setting.data_storage');

        if (!\is_string($cacheParam)) {
            throw new \Exception(\sprintf('Invalid container parameter "%s" - only string accepted.', 'dwalczyk_setting.cache'));
        }

        if (!\is_string($dataStorageParam)) {
            throw new \Exception(\sprintf('Invalid container parameter "%s" - only string accepted.', 'dwalczyk_setting.data_storage'));
        }

        if ($container->hasParameter('dwalczyk_setting.cache')) {
            $container->setDefinition('dwalczyk_setting.cache', $container->getDefinition($cacheParam));
        } else {
            $container->setDefinition('dwalczyk_setting.cache', new Definition(NullAdapter::class));
        }
        $container->setDefinition('dwalczyk_setting.data_storage', $container->getDefinition($dataStorageParam));

        $isDebug = (bool) $container->getParameter('kernel.debug');
        if ($isDebug) {
            $container->setDefinition(SettingsInterface::class, $container->getDefinition(TraceableSettings::class));
        } else {
            $container->setDefinition(SettingsInterface::class, $container->getDefinition(Settings::class));
        }
    }
}
