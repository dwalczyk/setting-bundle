<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Doctrine\Entity;

class Setting
{
    private ?int $id = null;

    private string $name;

    private ?string $ownerIdentifier;

    private ?string $value;

    protected \DateTimeImmutable $createdAt;

    private ?\DateTimeImmutable $modifiedAt = null;

    public function __construct(
        string $name,
        ?string $ownerIdentifier
    ) {
        $this->name = $name;
        $this->ownerIdentifier = $ownerIdentifier;

        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwnerIdentifier(): ?string
    {
        return $this->ownerIdentifier;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;

        $this->modifiedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }
}
