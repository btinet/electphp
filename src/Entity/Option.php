<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`option`')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\ManyToMany(targetEntity: Election::class, mappedBy: 'options')]
    private Collection $elections;

    #[ORM\ManyToMany(targetEntity: ElectionResult::class, inversedBy: 'options')]
    private Collection $electionResults;

    public function __construct()
    {
        $this->elections = new ArrayCollection();
        $this->electionResults = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->label;
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

        return $this;
    }

    /**
     * @return Collection<int, Election>
     */
    public function getElections(): Collection
    {
        return $this->elections;
    }

    public function addElection(Election $election): static
    {
        if (!$this->elections->contains($election)) {
            $this->elections->add($election);
            $election->addOption($this);
        }

        return $this;
    }

    public function removeElection(Election $election): static
    {
        if ($this->elections->removeElement($election)) {
            $election->removeOption($this);
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
        }

        return $this;
    }

    public function removeElectionResult(ElectionResult $electionResult): static
    {
        $this->electionResults->removeElement($electionResult);

        return $this;
    }
}
