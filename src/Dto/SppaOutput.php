<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class SppaOutput
{
    /** @Groups({"sppa:read"}) */
    public ?int $id = null;

    /** @Groups({"sppa:read"}) */
    public ?string $nom = null;

    /** @Groups({"sppa:read"}) */
    public ?string $description = null;

    /** @Groups({"sppa:read"}) */
    public ?string $avatar = null;

    /** @Groups({"sppa:read"}) */
    public ?string $couleur = null;

    /** @Groups({"sppa:read"}) */
    public ?array $competences = null;

    /** @Groups({"sppa:read"}) */
    public ?array $valeurs = null;

    /** @Groups({"sppa:read"}) */
    public ?float $niveau = null;

    /** @Groups({"sppa:read"}) */
    public ?float $heuresAccumulees = null;

    /** @Groups({"sppa:read"}) */
    public ?float $experienceXp = null;

    /** @Groups({"sppa:read"}) */
    public ?array $taches = null;

    /** @Groups({"sppa:read"}) */
    public ?array $projets = null;
}
