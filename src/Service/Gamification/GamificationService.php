<?php

namespace App\Service\Gamification;

use App\Entity\Tache;
use App\Entity\Projet;
use App\Entity\Objectif;
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
        $totalTaches = $taches->count();
        
        // Si le projet a des tâches, vérifier qu'elles sont toutes terminées
        if ($totalTaches > 0) {
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
        }

        // Projet terminé : changer le statut et donner un BONUS au SPPA
        $projet->setStatut('termine');
        
        $sppa = $projet->getSppa();
        if ($sppa) {
            // Calculer le bonus : 100 XP de base + 10 XP par tâche
            $bonusXp = 100 + ($totalTaches * 10);

            // Ajouter le bonus au SPPA
            $nouvelleXp = $sppa->getExperienceXp() + $bonusXp;
            $sppa->setExperienceXp($nouvelleXp);
            
            $nouveauNiveau = $this->levelCalculator->calculerNiveau($nouvelleXp);
            $sppa->setNiveau($nouveauNiveau);
            
            $heures = $this->progressionCalculator->calculerHeures($sppa);
            $sppa->setHeuresAccumulees($heures);
        }
        
        $this->em->flush();
    }

    public function onObjectifAtteint(Objectif $objectif): void
    {
        $sppa = $objectif->getSppa();
        if (!$sppa) {
            return;
        }

        // Bonus minimum de 500 XP, même sans projets
        $bonusXp = 500;
        
        // Bonus supplémentaire si l'objectif a des projets terminés
        $projets = $objectif->getProjets();
        if ($projets && $projets->count() > 0) {
            $projetsTermines = 0;
            foreach ($projets as $projet) {
                if ($projet->getStatut() === 'termine') {
                    $projetsTermines++;
                }
            }
            // 50 XP bonus par projet terminé
            $bonusXp += ($projetsTermines * 50);
        }

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
