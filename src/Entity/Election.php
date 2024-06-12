<?php

namespace App\Entity;

use App\Repository\ElectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ElectionRepository::class)]
#[UniqueEntity(
    fields: ['label'],
    message: 'Die Bezeichnung der Wahlrunde muss eindeutig sein.'
)]
class Election
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: false)]
    private ?string $label = null;

    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $uuid;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'election', targetEntity: ElectionCode::class, cascade: ['persist','remove'])]
    private Collection $codes;

    #[ORM\OneToMany(mappedBy: 'election', targetEntity: Person::class, cascade: ['persist','remove'])]
    private Collection $people;

    #[ORM\ManyToMany(targetEntity: ElectionResult::class, mappedBy: 'election')]
    private Collection $electionResults;

    #[ORM\Column(nullable: true)]
    private ?int $voices = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'elections')]
    private ?User $user = null;

    #[ORM\Column(nullable: false)]
    private bool $vote = false;

    #[ORM\ManyToMany(targetEntity: Option::class, inversedBy: 'elections')]
    private Collection $options;

    public function __construct()
    {
        $this->codes = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->electionResults = new ArrayCollection();
        $this->options = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->label;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        $namespace = Uuid::fromString(Uuid::NAMESPACE_URL);
        $this->uuid = Uuid::v3($namespace, $label . date("ymdHis"));
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ElectionCode>
     */
    public function getCodes(): Collection
    {
        return $this->codes;
    }

    public function addCode(ElectionCode $code): static
    {
        if (!$this->codes->contains($code)) {
            $this->codes->add($code);
            $code->setElection($this);
        }

        return $this;
    }

    public function removeCode(ElectionCode $code): static
    {
        if ($this->codes->removeElement($code)) {
            // set the owning side to null (unless already changed)
            if ($code->getElection() === $this) {
                $code->setElection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function getVotesPerPeople(): array
    {
        $count = [];
        foreach ($this->getPeople() as $person) {
            if($person instanceof Person) {
                $person->getElectionResults()->count();
            }

        }
        return $count;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
            $person->setElection($this);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getElection() === $this) {
                $person->setElection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ElectionResult>
     */
    public function getElectionResults(): Collection
    {
        return $this->electionResults;
    }

    public function addElectionResult(ElectionResult $electionResult): static
    {
        if (!$this->electionResults->contains($electionResult)) {
            $this->electionResults->add($electionResult);
            $electionResult->addElection($this);
        }

        return $this;
    }

    public function removeElectionResult(ElectionResult $electionResult): static
    {
        if ($this->electionResults->removeElement($electionResult)) {
            $electionResult->removeElection($this);
        }

        return $this;
    }

    public function getVoices(): ?int
    {
        return $this->voices;
    }

    public function setVoices(?int $voices): static
    {
        $this->voices = $voices;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isVote(): bool
    {
        return $this->vote;
    }

    public function setVote(bool $vote): static
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
        }

        return $this;
    }

    public function removeOption(Option $option): static
    {
        $this->options->removeElement($option);

        return $this;
    }

}
