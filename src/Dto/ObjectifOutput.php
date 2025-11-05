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
    public ?array $projet = null; // minimal project info
}
