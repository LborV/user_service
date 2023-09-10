<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $last_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $c_date;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $m_date;

    #[ORM\Column(length: 150)]
    private ?string $email = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $is_valid = true;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void {
        $this->setCDate(new \DateTimeImmutable());
        $this->setMDate(new \DateTimeImmutable());
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(\Doctrine\Persistence\Event\LifecycleEventArgs $event) {
        $this->setMDate(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setIsValid(bool $is_valid): static
    {
        $this->is_valid = $is_valid;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->is_valid;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getCDate(): ?\DateTimeInterface
    {
        return $this->c_date;
    }

    public function setCDate(\DateTimeInterface $c_date): static
    {
        $this->c_date = $c_date;

        return $this;
    }

    public function getMDate(): ?\DateTimeInterface
    {
        return $this->m_date;
    }

    public function setMDate(\DateTimeInterface $m_date): static
    {
        $this->m_date = $m_date;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
