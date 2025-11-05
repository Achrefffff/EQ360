<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class TacheOutput
{
    /** @Groups({"tache:read"}) */
    public ?int $id = null;

    /** @Groups({"tache:read"}) */
    public ?string $titre = null;

    /** @Groups({"tache:read"}) */
    public ?string $description = null;

    /** @Groups({"tache:read"}) */
    public ?string $dateDebut = null;

    /** @Groups({"tache:read"}) */
    public ?string $dateFin = null;

    /** @Groups({"tache:read"}) */
    public ?array $projet = null;
}
