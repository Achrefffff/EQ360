<?php

namespace App\Service\Gamification;

use App\Entity\Tache;
use App\Entity\Projet;
use Doctrine\ORM\EntityManagerInterface;

class GamificationService
{
    private XpCalculator $xpCalculator;
    private LevelCalculator $levelCalculator;
    private ProgressionCalculator $progressionCalculator;
    private EntityManagerInterface $em;

    public function __construct(
        XpCalculator $xpCalculator,
        LevelCalculator $levelCalculator,
        ProgressionCalculator $progressionCalculator,
        EntityManagerInterface $em
    ) {
        $this->xpCalculator = $xpCalculator;
        $this->levelCalculator = $levelCalculator;
        $this->progressionCalculator = $progressionCalculator;
        $this->em = $em;
    }

    public function onTacheCompleted(Tache $tache): void
    {
        $sppa = $tache->getSppa();
        if (!$sppa) {
            return;
        }

        // Toujours donner l'XP au SPPA, même si la tâche a un projet
        $xpGagne = $this->xpCalculator->calculerXpTache($tache);
        
        $nouvelleXp = $sppa->getExperienceXp() + $xpGagne;
        $sppa->setExperienceXp($nouvelleXp);
        
        $nouveauNiveau = $this->levelCalculator->calculerNiveau($nouvelleXp);
        $sppa->setNiveau($nouveauNiveau);
        
        $heures = $this->progressionCalculator->calculerHeures($sppa);
        $sppa->setHeuresAccumulees($heures);
        
        $this->em->flush();
    }

    public function checkProjetCompletion(Projet $projet): void
    {
        $taches = $projet->getTaches();
        
        // Vérifier si le projet a des tâches
        if ($taches->isEmpty()) {
            return;
        }

        // Vérifier si toutes les tâches sont terminées
        $totalTaches = $taches->count();
        $tachesTerminees = 0;
        
        foreach ($taches as $tache) {
            if ($tache->getStatut() === 'done') {
                $tachesTerminees++;
            }
        }

        // Si toutes les tâches ne sont pas terminées, on ne fait rien
        if ($tachesTerminees !== $totalTaches) {
            return;
        }

        // Toutes les tâches sont terminées : donner un BONUS au SPPA
        $sppa = $projet->getSppa();
        if (!$sppa) {
            return;
        }

        // Calculer le bonus : 100 XP de base + 10 XP par tâche
        $bonusXp = 100 + ($totalTaches * 10);

        // Ajouter le bonus au SPPA
        $nouvelleXp = $sppa->getExperienceXp() + $bonusXp;
        $sppa->setExperienceXp($nouvelleXp);
        
        $nouveauNiveau = $this->levelCalculator->calculerNiveau($nouvelleXp);
        $sppa->setNiveau($nouveauNiveau);
        
        $heures = $this->progressionCalculator->calculerHeures($sppa);
        $sppa->setHeuresAccumulees($heures);
        
        $this->em->flush();
    }
}
