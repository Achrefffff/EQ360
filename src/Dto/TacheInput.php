<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class TacheInput
{
    /** @Assert\NotBlank() @Groups({"tache:write"}) */
    public ?string $titre = null;

    /** @Groups({"tache:write"}) */
    public ?string $description = null;

    /** @Assert\Type(type="integer") @Groups({"tache:write"}) */
    public ?int $projetId = null;

    /** @Assert\Date() @Groups({"tache:write"}) */
    public ?string $dateDebut = null;

    /** @Assert\Date() @Groups({"tache:write"}) */
    public ?string $dateFin = null;
}
