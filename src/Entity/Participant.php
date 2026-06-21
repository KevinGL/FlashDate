<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Session $session = null;

    /**
     * @var Collection<int, Daty>
     */
    #[ORM\OneToMany(targetEntity: Daty::class, mappedBy: 'part1')]
    private Collection $daties;

    #[ORM\OneToOne(inversedBy: 'participant', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->daties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): static
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Collection<int, Daty>
     */
    public function getDaties(): Collection
    {
        return $this->daties;
    }

    public function addDaty(Daty $daty): static
    {
        if (!$this->daties->contains($daty)) {
            $this->daties->add($daty);
            $daty->setPart1($this);
        }

        return $this;
    }

    public function removeDaty(Daty $daty): static
    {
        if ($this->daties->removeElement($daty)) {
            // set the owning side to null (unless already changed)
            if ($daty->getPart1() === $this) {
                $daty->setPart1(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
