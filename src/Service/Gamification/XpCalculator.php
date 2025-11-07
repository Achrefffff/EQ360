<?php

namespace App\Service\Gamification;

use App\Entity\Tache;

class XpCalculator
{
    public function calculerXpTache(Tache $tache): float
    {
        $xpBase = ($tache->getDifficulte() * 10) 
                + ($tache->getDureeEstimee() * 2) 
                + ($tache->getEnthousiasme() * 1);
        
        if ($this->estEnRetard($tache)) {
            return $xpBase / 2;
        }
        
        return $xpBase;
    }

    private function estEnRetard(Tache $tache): bool
    {
        $dateEcheance = $tache->getDateEcheance();
        
        if ($dateEcheance === null) {
            return false;
        }
        
        $aujourdhui = new \DateTime();
        return $aujourdhui > $dateEcheance;
    }
}
