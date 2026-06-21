<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $birthDate = null;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'userId1', orphanRemoval: true)]
    private Collection $conversations1;

    /**
     * @var Collection<int, Conversation>
     */
    #[ORM\OneToMany(targetEntity: Conversation::class, mappedBy: 'userId2', orphanRemoval: true)]
    private Collection $conversations2;

    #[ORM\Column(length: 255)]
    private ?string $gender = null;

    #[ORM\Column(length: 255)]
    private ?string $search = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $messages;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column]
    private ?float $distanceFilter = null;

    #[ORM\Column(length: 255)]
    private ?string $ageRange = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Participant $participant = null;

    /**
     * @var Collection<int, Participation>
     */
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $participations;

    public function __construct()
    {
        $this->conversations1 = new ArrayCollection();
        $this->conversations2 = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $birthDate): static
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations1(): Collection
    {
        return $this->conversations1;
    }

    public function addConversations1(Conversation $conversations1): static
    {
        if (!$this->conversations1->contains($conversations1)) {
            $this->conversations1->add($conversations1);
            $conversations1->setUser1($this);
        }

        return $this;
    }

    public function removeConversations1(Conversation $conversations1): static
    {
        if ($this->conversations1->removeElement($conversations1)) {
            // set the owning side to null (unless already changed)
            if ($conversations1->getUser1() === $this) {
                $conversations1->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversations2(): Collection
    {
        return $this->conversations2;
    }

    public function addConversations2(Conversation $conversations2): static
    {
        if (!$this->conversations2->contains($conversations2)) {
            $this->conversations2->add($conversations2);
            $conversations2->setUser2($this);
        }

        return $this;
    }

    public function removeConversations2(Conversation $conversations2): static
    {
        if ($this->conversations2->removeElement($conversations2)) {
            // set the owning side to null (unless already changed)
            if ($conversations2->getUser2() === $this) {
                $conversations2->setUser2(null);
            }
        }

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(string $search): static
    {
        $this->search = $search;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getDistanceFilter(): ?float
    {
        return $this->distanceFilter;
    }

    public function setDistanceFilter(float $distanceFilter): static
    {
        $this->distanceFilter = $distanceFilter;

        return $this;
    }

    public function getAgeRange(): ?string
    {
        return $this->ageRange;
    }

    public function setAgeRange(string $ageRange): static
    {
        $this->ageRange = $ageRange;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(Participant $participant): static
    {
        // set the owning side of the relation if necessary
        if ($participant->getUser() !== $this) {
            $participant->setUser($this);
        }

        $this->participant = $participant;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setUser($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            // set the owning side to null (unless already changed)
            if ($participation->getUser() === $this) {
                $participation->setUser(null);
            }
        }

        return $this;
    }
}
