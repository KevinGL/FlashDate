<?php

namespace App\Entity;

use App\Repository\DatyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DatyRepository::class)]
class Daty
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'daties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $part1 = null;

    #[ORM\ManyToOne(inversedBy: 'daties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $part2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPart1(): ?Participant
    {
        return $this->part1;
    }

    public function setPart1(?Participant $part1): static
    {
        $this->part1 = $part1;

        return $this;
    }

    public function getPart2(): ?Participant
    {
        return $this->part2;
    }

    public function setPart2(?Participant $part2): static
    {
        $this->part2 = $part2;

        return $this;
    }
}
