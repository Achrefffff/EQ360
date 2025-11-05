<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class SppaInput
{
    /** @Assert\NotBlank() @Groups({"sppa:write"}) */
    public ?string $nom = null;

    /** @Groups({"sppa:write"}) */
    public ?string $description = null;

    /** @Groups({"sppa:write"}) */
    public ?string $avatar = null;

    /** @Groups({"sppa:write"}) */
    public ?string $couleur = null;

    /** @Groups({"sppa:write"}) */
    public ?array $competences = null;

    /** @Groups({"sppa:write"}) */
    public ?array $valeurs = null;

    /** @Groups({"sppa:write"}) */
    public ?float $niveau = null;

    /** @Groups({"sppa:write"}) */
    public ?float $heuresAccumulees = null;

    /** @Groups({"sppa:write"}) */
    public ?float $experienceXp = null;
}
