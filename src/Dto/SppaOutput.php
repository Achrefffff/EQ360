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
}
