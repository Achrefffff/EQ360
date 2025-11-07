<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class TacheOutput
{
    /** @Groups({"tache:read"}) */
    public ?int $id = null;

    /** @Groups({"tache:read"}) */
    public ?string $nom = null;

    /** @Groups({"tache:read"}) */
    public ?string $description = null;

    /** @Groups({"tache:read"}) */
    public ?string $dateDebut = null;

    /** @Groups({"tache:read"}) */
    public ?string $dateFin = null;

    /** @Groups({"tache:read"}) */
    public ?array $projet = null;

    /** @Groups({"tache:read"}) */
    public ?string $priorite = null;

    /** @Groups({"tache:read"}) */
    public ?string $statut = null;

    /** @Groups({"tache:read"}) */
    public ?float $dureeEstimee = null;

    /** @Groups({"tache:read"}) */
    public ?int $difficulte = null;

    /** @Groups({"tache:read"}) */
    public ?int $enthousiasme = null;

    /** @Groups({"tache:read"}) */
    public ?int $sppaId = null;

    /** @Groups({"tache:read"}) */
    public ?array $sppa = null;
}
