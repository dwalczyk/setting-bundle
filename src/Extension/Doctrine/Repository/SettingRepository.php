<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use DWalczyk\SettingBundle\Extension\Doctrine\Entity\Setting;

final readonly class SettingRepository implements SettingRepositoryInterface
{
    private ObjectRepository $repository;

    public function __construct(private EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Setting::class);
    }

    public function find(string $name, int|string|null $ownerIdentifier): ?Setting
    {
        return $this->repository->findOneBy([
            'name' => $name,
            'ownerIdentifier' => $ownerIdentifier,
        ]);
    }

    public function persist(Setting $setting): void
    {
        $this->em->persist($setting);

        $this->em->flush();
    }

    public function update(Setting $setting): void
    {
        $this->em->flush();
    }
}
