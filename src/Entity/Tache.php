<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TacheRepository")
 * @ORM\Table(name="taches")
 */
class Tache
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $livrable;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domaine;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $valeurAjoutee;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateEcheance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $priorite;

    /**
     * @ORM\Column(type="float")
     */
    private $dureeEstimee;

    /**
     * @ORM\Column(type="integer")
     */
    private $difficulte;

    /**
     * @ORM\Column(type="integer")
     */
    private $enthousiasme;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pieceJoint;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="taches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Objectif", inversedBy="taches")
     * @ORM\JoinColumn(nullable=true)
     */
    private $objectif;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Projet", inversedBy="taches")
     * @ORM\JoinColumn(nullable=true)
     */
    private $projet;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SPPA", inversedBy="taches")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sppa;

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

    public function getLivrable(): ?string
    {
        return $this->livrable;
    }

    public function setLivrable(?string $livrable): self
    {
        $this->livrable = $livrable;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(string $domaine): self
    {
        $this->domaine = $domaine;
        return $this;
    }

    public function getValeurAjoutee(): ?string
    {
        return $this->valeurAjoutee;
    }

    public function setValeurAjoutee(string $valeurAjoutee): self
    {
        $this->valeurAjoutee = $valeurAjoutee;
        return $this;
    }

    public function getDateEcheance(): ?\DateTimeInterface
    {
        return $this->dateEcheance;
    }

    public function setDateEcheance(?\DateTimeInterface $dateEcheance): self
    {
        $this->dateEcheance = $dateEcheance;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): self
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getDureeEstimee(): ?float
    {
        return $this->dureeEstimee;
    }

    public function setDureeEstimee(float $dureeEstimee): self
    {
        $this->dureeEstimee = $dureeEstimee;
        return $this;
    }

    public function getDifficulte(): ?int
    {
        return $this->difficulte;
    }

    public function setDifficulte(int $difficulte): self
    {
        $this->difficulte = $difficulte;
        return $this;
    }

    public function getEnthousiasme(): ?int
    {
        return $this->enthousiasme;
    }

    public function setEnthousiasme(int $enthousiasme): self
    {
        $this->enthousiasme = $enthousiasme;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getPieceJoint(): ?string
    {
        return $this->pieceJoint;
    }

    public function setPieceJoint(?string $pieceJoint): self
    {
        $this->pieceJoint = $pieceJoint;
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

    public function getObjectif(): ?Objectif
    {
        return $this->objectif;
    }

    public function setObjectif(?Objectif $objectif): self
    {
        $this->objectif = $objectif;
        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): self
    {
        $this->projet = $projet;
        return $this;
    }

    public function getSppa(): ?SPPA
    {
        return $this->sppa;
    }

    public function setSppa(?SPPA $sppa): self
    {
        $this->sppa = $sppa;
        return $this;
    }
}