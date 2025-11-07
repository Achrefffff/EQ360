<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class TacheInput
{
    /** @Assert\NotBlank() @Groups({"tache:write"}) */
    public ?string $nom = null;

    /** @Groups({"tache:write"}) */
    public ?string $description = null;

    /** @Assert\Type(type="integer") @Groups({"tache:write"}) */
    public ?int $projetId = null;

    /** @Assert\Type(type="integer") @Groups({"tache:write"}) */
    public ?int $sppaId = null;

    /** @Assert\Date() @Groups({"tache:write"}) */
    public ?string $dateDebut = null;

    /** @Assert\Date() @Groups({"tache:write"}) */
    public ?string $dateFin = null;

    /** 
     * @Assert\Choice(choices={"Haute", "Moyenne", "Basse"}, message="La priorité doit être: Haute, Moyenne ou Basse")
     * @Groups({"tache:write"}) 
     */
    public ?string $priorite = null;

    /** 
     * @Assert\Choice(choices={"todo", "in_progress", "done"}, message="Le statut doit être: todo, in_progress ou done")
     * @Groups({"tache:write"}) 
     */
    public ?string $statut = null;

    /** 
     * @Assert\Range(min=1, max=10, notInRangeMessage="La difficulté doit être entre 1 et 10")
     * @Groups({"tache:write"}) 
     */
    public ?int $difficulte = null;

    /** 
     * @Assert\Range(min=1, max=10, notInRangeMessage="L'enthousiasme doit être entre 1 et 10")
     * @Groups({"tache:write"}) 
     */
    public ?int $enthousiasme = null;

    /** 
     * @Assert\Positive(message="La durée estimée doit être positive")
     * @Groups({"tache:write"}) 
     */
    public ?float $dureeEstimee = null;
}
