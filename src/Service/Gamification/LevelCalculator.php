<?php

namespace App\Service\Gamification;

class LevelCalculator
{
    public function calculerNiveau(float $xp): int
    {
        return (int) floor($xp / 100) + 1;
    }

    public function xpPourProchainNiveau(float $xpActuel): float
    {
        $niveauActuel = $this->calculerNiveau($xpActuel);
        $xpProchainNiveau = $niveauActuel * 100;
        return $xpProchainNiveau - $xpActuel;
    }
}
