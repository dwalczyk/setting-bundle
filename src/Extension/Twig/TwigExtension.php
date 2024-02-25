<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Twig;

use DWalczyk\SettingBundle\SettingsInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly SettingsInterface $settings
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setting', [$this->settings, 'get']),
        ];
    }
}
