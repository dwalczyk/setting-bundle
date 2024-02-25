<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Debug;

use DWalczyk\SettingBundle\SettingOwnerInterface;
use DWalczyk\SettingBundle\SettingRegistryInterface;
use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Symfony\Component\VarDumper\Cloner\Data;

/**
 * @internal
 */
class SettingDataCollector extends AbstractDataCollector implements LateDataCollectorInterface
{
    private array $calls = [];

    public function __construct(private readonly SettingRegistryInterface $registry)
    {
    }

    public function getName(): string
    {
        return 'dwalczyk_setting';
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
    }

    public static function getTemplate(): ?string
    {
        return '@Setting/Collector/template.html.twig';
    }

    public function addGet(string $name, SettingOwnerInterface|int|string|null $owner, mixed $value, float $duration): void
    {
        $this->calls[] = [
            'type' => 'get',
            'params' => [
                'name' => $name,
                'owner' => $owner,
            ],
            'return' => $value,
            'duration' => $duration,
        ];
    }

    public function addSet(string $name, SettingOwnerInterface|int|string|null $owner, mixed $value, float $duration): void
    {
        $this->calls[] = [
            'type' => 'set',
            'params' => [
                'name' => $name,
                'owner' => $owner,
                'value' => $value,
            ],
            'duration' => $duration,
        ];
    }

    public function getData(): Data|array
    {
        return $this->data;
    }

    public function lateCollect(): void
    {
        $this->data = [
            'calls' => $this->calls,
            'definitions' => $this->registry->getDefinitions(),
        ];
    }

    public function getCalls(?string $callType = null): array
    {
        if (null !== $callType) {
            $calls = [];
            foreach ($this->getCalls() as $call) {
                if ($call['type'] === $callType) {
                    $calls[] = $call;
                }
            }

            return $calls;
        }

        return $this->getData()['calls'];
    }

    public function getCallsTypes(): array
    {
        return [
            'get',
            'set',
        ];
    }

    public function getCallsTotalDuration(?string $callType = null): float
    {
        $totalDuration = 0;
        foreach ($this->getCalls() as $call) {
            if (null === $callType || $call['type'] === $callType) {
                $totalDuration += $call['duration'];
            }
        }

        return $totalDuration;
    }

    public function getCallsCount(?string $callType = null): int
    {
        if (null !== $callType) {
            $count = 0;
            foreach ($this->getCalls() as $call) {
                if ($call['type'] === $callType) {
                    ++$count;
                }
            }

            return $count;
        }

        return \count($this->getCalls());
    }

    public function showDataCollector(): bool
    {
        return true;
    }
}
