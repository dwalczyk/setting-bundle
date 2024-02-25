<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Debug;

use DWalczyk\SettingBundle\SettingOwnerInterface;
use DWalczyk\SettingBundle\SettingsInterface;
use Symfony\Component\Stopwatch\Stopwatch;

final class TraceableSettings implements SettingsInterface
{
    private Stopwatch $stopwatch;

    public function __construct(
        private readonly SettingsInterface $settings,
        private readonly SettingDataCollector $dataCollector,
    ) {
        $this->stopwatch = new Stopwatch(true);
    }

    public function get(string $name, SettingOwnerInterface|int|string|null $owner = null): mixed
    {
        $this->stopwatch->start('get');

        $value = $this->settings->get($name, $owner);

        $event = $this->stopwatch->stop('get');
        $this->stopwatch->reset();

        $this->dataCollector->addGet($name, $owner, $value, $event->getDuration());

        return $value;
    }

    public function set(string $name, mixed $value, SettingOwnerInterface|int|string|null $owner = null): void
    {
        $this->stopwatch->start('set');

        $this->settings->set($name, $value, $owner);

        $event = $this->stopwatch->stop('set');
        $this->stopwatch->reset();

        $this->dataCollector->addSet($name, $owner, $value, $event->getDuration());
    }
}
