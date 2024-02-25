<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

use DWalczyk\SettingBundle\DependencyInjection\BundleExtension;
use DWalczyk\SettingBundle\DependencyInjection\CustomPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class SettingBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CustomPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new BundleExtension();
    }
}
