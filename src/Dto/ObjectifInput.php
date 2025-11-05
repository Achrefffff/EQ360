<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ObjectifInput
{
    /** @Assert\NotBlank() @Groups({"objectif:write"}) */
    public ?string $titre = null;

    /** @Groups({"objectif:write"}) */
    public ?string $description = null;

    /** @Assert\Type(type="integer") @Groups({"objectif:write"}) */
    public ?int $projetId = null;

    /** @Groups({"objectif:write"}) */
    public ?int $sppaId = null;
}
