<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ProjetInput
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Groups({"projet:write"})
     */
    public ?string $nom = null;

    /**
     * @Assert\Length(max=255)
     * @Groups({"projet:write"})
     */
    public ?string $typeProjet = null;

    /**
     * @Assert\Length(max=255)
     * @Groups({"projet:write"})
     */
    public ?string $categorie = null;

    /**
     * @Assert\Length(max=1000)
     * @Groups({"projet:write"})
     */
    public ?string $description = null;

    /**
     * @Assert\Date()
     * @Groups({"projet:write"})
     */
    public ?string $dateDebut = null; // ISO date string

    /**
     * @Assert\Date()
     * @Groups({"projet:write"})
     */
    public ?string $dateFin = null; // ISO date string

    /**
     * @Assert\Type(type="numeric")
     * @Groups({"projet:write"})
     */
    public ?float $budget = null;

    /**
     * @Assert\Length(max=255)
     * @Groups({"projet:write"})
     */
    public ?string $statut = null;

    /**
     * @Groups({"projet:write"})
     */
    public ?string $pieceJointes = null;

    /**
     * @Assert\Type(type="integer")
     * @Groups({"projet:write"})
     */
    public ?int $sppaId = null;
}
