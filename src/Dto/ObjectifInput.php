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

    /** @Groups({"objectif:write"}) */
    public ?string $domaineVie = null;

    /** @Groups({"objectif:write"}) */
    public ?string $horizon = null;

    /** @Groups({"objectif:write"}) */
    public ?string $priorite = null;

    /** @Groups({"objectif:write"}) */
    public ?string $statut = null;

    /** @Assert\Date() @Groups({"objectif:write"}) */
    public ?string $dateDebut = null;

    /** @Assert\Date() @Groups({"objectif:write"}) */
    public ?string $dateFin = null;

    /** @Groups({"objectif:write"}) */
    public ?array $projetIds = null;

    /** @Assert\Type(type="integer") @Groups({"objectif:write"}) */
    public ?int $sppaId = null;
}
