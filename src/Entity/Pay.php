<?php

namespace App\Entity;

use App\Repository\PayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayRepository::class)]
class Pay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transaction $transaction = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 3)]
    private ?string $rate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $client_phone = null;

    #[ORM\Column]
    private ?\DateTime $payed_At = null;
    
    public function __construct()
    {
        $this->payed_At = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->client_phone;
    }

    public function setClientPhone(?string $client_phone): self
    {
        $this->client_phone = $client_phone;

        return $this;
    }

    public function getPayedAt(): ?\DateTime
    {
        return $this->payed_At;
    }

    public function setPayedAt(\DateTime $payed_At): self
    {
        $this->payed_At = $payed_At;

        return $this;
    }
}
