<?php

namespace App\Service\Gamification;

use App\Entity\SPPA;

class ProgressionCalculator
{
    public function calculerHeures(SPPA $sppa): float
    {
        $total = 0.0;
        foreach ($sppa->getTaches() as $tache) {
            if ($tache->getStatut() === 'done') {
                $total += $tache->getDureeEstimee();
            }
        }
        return $total;
    }
}
