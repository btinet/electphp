<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'people')]
    private ?Election $election = null;

    #[ORM\ManyToMany(targetEntity: ElectionResult::class, mappedBy: 'person', cascade: ['persist','remove'],orphanRemoval: true)]
    private Collection $electionResults;

    #[ORM\ManyToOne(inversedBy: 'people')]
    private ?User $user = null;

    public function __construct()
    {
        $this->electionResults = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function randColor(): string
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getElection(): ?Election
    {
        return $this->election;
    }

    public function setElection(?Election $election): static
    {
        $this->election = $election;

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
            $electionResult->addPerson($this);
        }

        return $this;
    }

    public function removeElectionResult(ElectionResult $electionResult): static
    {
        if ($this->electionResults->removeElement($electionResult)) {
            $electionResult->removePerson($this);
        }

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
}
