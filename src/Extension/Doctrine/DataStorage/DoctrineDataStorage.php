<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Doctrine\DataStorage;

use Doctrine\ORM\EntityManagerInterface;
use DWalczyk\SettingBundle\DataStorageInterface;
use DWalczyk\SettingBundle\Exception\DataStorage\SettingNotFoundException;
use DWalczyk\SettingBundle\Extension\Doctrine\Entity\Setting;
use DWalczyk\SettingBundle\Extension\Doctrine\Repository\SettingRepositoryInterface;

final readonly class DoctrineDataStorage implements DataStorageInterface
{
    public function __construct(
        private SettingRepositoryInterface $repository,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @throws SettingNotFoundException
     */
    public function read(string $name, ?string $ownerIdentifier): ?string
    {
        $result = $this->repository->find($name, $ownerIdentifier);
        if (null === $result) {
            throw new SettingNotFoundException($name, $ownerIdentifier);
        }

        return $result->getValue();
    }

    public function write(string $name, ?string $value, ?string $ownerIdentifier): void
    {
        $setting = $this->repository->find($name, $ownerIdentifier) ?? new Setting($name, $ownerIdentifier);
        $setting->setValue($value);

        if (null === $setting->getId()) {
            $this->em->persist($setting);
        }

        $this->em->flush();
    }
}
