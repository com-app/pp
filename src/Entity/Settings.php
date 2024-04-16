<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $defaults = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefaults(): ?string
    {
        return $this->defaults;
    }

    public function setDefaults(?string $defaults): self
    {
        $this->defaults = $defaults;

        return $this;
    }
}
