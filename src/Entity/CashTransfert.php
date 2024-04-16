<?php

namespace App\Entity;

use App\Repository\CashTransfertRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CashTransfertRepository::class)]
class CashTransfert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $source = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $target = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $performedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?User
    {
        return $this->source;
    }

    public function setSource(?User $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getTarget(): ?User
    {
        return $this->target;
    }

    public function setTarget(?User $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPerformedAt(): ?\DateTimeImmutable
    {
        return $this->performedAt;
    }

    public function setPerformedAt(\DateTimeImmutable $performedAt): self
    {
        $this->performedAt = $performedAt;

        return $this;
    }
}
