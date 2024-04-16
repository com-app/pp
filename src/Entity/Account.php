<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column]
    private ?float $balance = null;

    #[ORM\Column]
    private ?bool $open = null;

    #[ORM\OneToMany(mappedBy: 'acct', targetEntity: AccountMovement::class, orphanRemoval: true)]
    private Collection $accountMovements;

    public function __construct()
    {
        $this->accountMovements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function credit(float $credit): self
    {
        $this->setBalance($this->balance +$credit);

        return $this;
    }
    public function debit(float $debit): self
    {
        $this->setBalance($this->balance-$debit);

        return $this;
    }

    public function isOpen(): ?bool
    {
        return $this->open;
    }

    public function setState(bool $open): self
    {
        $this->open = $open;

        return $this;
    }

    /**
     * @return Collection<int, AccountMovement>
     */
    public function getAccountMovements(): Collection
    {
        return $this->accountMovements;
    }

    public function addAccountMovement(AccountMovement $accountMovement): self
    {
        if (!$this->accountMovements->contains($accountMovement)) {
            $this->accountMovements->add($accountMovement);
            $accountMovement->setAcct($this);
        }

        return $this;
    }

    public function removeAccountMovement(AccountMovement $accountMovement): self
    {
        if ($this->accountMovements->removeElement($accountMovement)) {
            // set the owning side to null (unless already changed)
            if ($accountMovement->getAcct() === $this) {
                $accountMovement->setAcct(null);
            }
        }

        return $this;
    }
}
