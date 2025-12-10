<?php

namespace App\Entity;

use App\Repository\AdviceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdviceRepository::class)]
class Advice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "La description doit contenir au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'jsonb')]
    #[Assert\NotBlank(message: "Les mois sont obligatoires")]
    #[Assert\Count(min: 1, minMessage: "Vous devez sélectionner au moins {{limit}} mois")]
    #[Assert\All([
        new Assert\Type(type: 'integer', message: 'Chaque mois doit être un entier'),
        new Assert\Range(
            notInRangeMessage: 'Le mois doit être compris entre {{ min }} et {{ max }}',
            min: 1,
            max: 12,
        )
    ])]
    private array $months = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMonths(): array
    {
        return $this->months;
    }

    public function setMonths(array $months): static
    {
        $this->months = $months;

        return $this;
    }
}
