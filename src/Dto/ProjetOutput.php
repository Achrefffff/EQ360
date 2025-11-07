<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class ProjetOutput
{
    /**
     * @Groups({"projet:read"})
     */
    public ?int $id = null;

    /** @Groups({"projet:read"}) */
    public ?string $nom = null;

    /** @Groups({"projet:read"}) */
    public ?string $typeProjet = null;

    /** @Groups({"projet:read"}) */
    public ?string $categorie = null;

    /** @Groups({"projet:read"}) */
    public ?string $description = null;

    /** @Groups({"projet:read"}) */
    public ?string $dateDebut = null;

    /** @Groups({"projet:read"}) */
    public ?string $dateFin = null;

    /** @Groups({"projet:read"}) */
    public ?float $budget = null;

    /** @Groups({"projet:read"}) */
    public ?string $statut = null;

    /** @Groups({"projet:read"}) */
    public ?string $pieceJointes = null;

    /** @Groups({"projet:read"}) */
    public ?int $sppaId = null;

    /** @Groups({"projet:read"}) */
    public ?array $sppa = null; // minimal sppa info

    /** @Groups({"projet:read"}) */
    public ?array $user = null; // minimal user info

    /** @Groups({"projet:read"}) */
    public ?array $stats = null; // statistiques du projet
}
