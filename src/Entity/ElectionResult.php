<?php

namespace App\Entity;

use App\Repository\ElectionResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElectionResultRepository::class)]
class ElectionResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Election::class, inversedBy: 'electionResults')]
    private Collection $election;

    #[ORM\ManyToMany(targetEntity: Person::class, inversedBy: 'electionResults')]
    private Collection $person;

    #[ORM\ManyToMany(targetEntity: Option::class, mappedBy: 'electionResults')]
    private Collection $options;

    public function __construct()
    {
        $this->election = new ArrayCollection();
        $this->person = new ArrayCollection();
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Election>
     */
    public function getElection(): Collection
    {
        return $this->election;
    }

    public function addElection(Election $election): static
    {
        if (!$this->election->contains($election)) {
            $this->election->add($election);
        }

        return $this;
    }

    public function removeElection(Election $election): static
    {
        $this->election->removeElement($election);

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPerson(): Collection
    {
        return $this->person;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->person->contains($person)) {
            $this->person->add($person);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        $this->person->removeElement($person);

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
            $option->addElectionResult($this);
        }

        return $this;
    }

    public function removeOption(Option $option): static
    {
        if ($this->options->removeElement($option)) {
            $option->removeElectionResult($this);
        }

        return $this;
    }
}
