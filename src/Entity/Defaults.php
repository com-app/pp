<?php

namespace App\Entity;

use App\Repository\DefaultsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefaultsRepository::class)]
class Defaults
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $_key = null;

    #[ORM\Column(nullable: true)]
    private ?float $_value = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $_values = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->_key;
    }

    public function setKey(string $_key): self
    {
        $this->_key = $_key;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->_value;
    }

    public function setValue(?float $_value): self
    {
        $this->_value = $_value;

        return $this;
    }

    public function getValues(): ?string
    {
        return $this->_values;
    }

    public function setValues(?string $_values): self
    {
        $this->_values = $_values;

        return $this;
    }
}
