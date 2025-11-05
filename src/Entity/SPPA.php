<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SPPARepository")
 * @ORM\Table(name="sppas")
 */
class SPPA
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couleur;

    /**
     * @ORM\Column(type="json")
     */
    private $competences = [];

    /**
     * @ORM\Column(type="json")
     */
    private $valeurs = [];

    /**
     * @ORM\Column(type="float")
     */
    private $niveau;

    /**
     * @ORM\Column(type="float")
     */
    private $heuresAccumulees;

    /**
     * @ORM\Column(type="float")
     */
    private $experienceXp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sppas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Projet", mappedBy="sppa")
     */
    private $projets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Objectif", mappedBy="sppa")
     */
    private $objectifs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tache", mappedBy="sppa")
     */
    private $taches;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->objectifs = new ArrayCollection();
        $this->taches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self
    {
        $this->couleur = $couleur;
        return $this;
    }

    public function getCompetences(): array
    {
        return $this->competences;
    }

    public function setCompetences(array $competences): self
    {
        $this->competences = $competences;
        return $this;
    }

    public function getValeurs(): array
    {
        return $this->valeurs;
    }

    public function setValeurs(array $valeurs): self
    {
        $this->valeurs = $valeurs;
        return $this;
    }

    public function getNiveau(): ?float
    {
        return $this->niveau;
    }

    public function setNiveau(float $niveau): self
    {
        $this->niveau = $niveau;
        return $this;
    }

    public function getHeuresAccumulees(): ?float
    {
        return $this->heuresAccumulees;
    }

    public function setHeuresAccumulees(float $heuresAccumulees): self
    {
        $this->heuresAccumulees = $heuresAccumulees;
        return $this;
    }

    public function getExperienceXp(): ?float
    {
        return $this->experienceXp;
    }

    public function setExperienceXp(float $experienceXp): self
    {
        $this->experienceXp = $experienceXp;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return Collection|Projet[]
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): self
    {
        if (!$this->projets->contains($projet)) {
            $this->projets[] = $projet;
            $projet->setSppa($this);
        }
        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->removeElement($projet)) {
            if ($projet->getSppa() === $this) {
                $projet->setSppa(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Objectif[]
     */
    public function getObjectifs(): Collection
    {
        return $this->objectifs;
    }

    public function addObjectif(Objectif $objectif): self
    {
        if (!$this->objectifs->contains($objectif)) {
            $this->objectifs[] = $objectif;
            $objectif->setSppa($this);
        }
        return $this;
    }

    public function removeObjectif(Objectif $objectif): self
    {
        if ($this->objectifs->removeElement($objectif)) {
            if ($objectif->getSppa() === $this) {
                $objectif->setSppa(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Tache[]
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches[] = $tache;
            $tache->setSppa($this);
        }
        return $this;
    }

    public function removeTache(Tache $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getSppa() === $this) {
                $tache->setSppa(null);
            }
        }
        return $this;
    }
}