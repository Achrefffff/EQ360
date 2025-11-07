<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class ObjectifOutput
{
    /** @Groups({"objectif:read"}) */
    public ?int $id = null;

    /** @Groups({"objectif:read"}) */
    public ?string $titre = null;

    /** @Groups({"objectif:read"}) */
    public ?string $description = null;

    /** @Groups({"objectif:read"}) */
    public ?string $domaineVie = null;

    /** @Groups({"objectif:read"}) */
    public ?string $horizon = null;

    /** @Groups({"objectif:read"}) */
    public ?string $priorite = null;

    /** @Groups({"objectif:read"}) */
    public ?string $statut = null;

    /** @Groups({"objectif:read"}) */
    public ?string $dateDebut = null;

    /** @Groups({"objectif:read"}) */
    public ?string $dateFin = null;

    /** @Groups({"objectif:read"}) */
    public ?float $progression = null;

    /** @Groups({"objectif:read"}) */
    public ?array $projets = null;

    /** @Groups({"objectif:read"}) */
    public ?int $sppaId = null;

    /** @Groups({"objectif:read"}) */
    public ?array $sppa = null;
}
