<?php

namespace App\Service;

use App\Dto\ProjetInput;
use App\Dto\ProjetOutput;
use App\Entity\Projet;
use App\Entity\SPPA;
use App\Repository\ProjetRepository;
use App\Repository\SPPARepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class ProjetService
{
    private EntityManagerInterface $em;
    private ProjetRepository $repo;
    private SPPARepository $sppaRepo;
    private ObjectifService $objectifService;

    public function __construct(EntityManagerInterface $em, ProjetRepository $repo, SPPARepository $sppaRepo, ObjectifService $objectifService)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->sppaRepo = $sppaRepo;
        $this->objectifService = $objectifService;
    }

    public function list(int $page = 1, int $limit = 20): array
    {
        $qb = $this->repo->createQueryBuilder('p')
            ->orderBy('p.dateDebut', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
        $items = [];
        foreach ($paginator as $proj) {
            $items[] = $this->toOutput($proj);
        }

        return [
            'items' => $items,
            'total' => count($paginator),
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public function get(int $id): ?ProjetOutput
    {
        $p = $this->repo->find($id);
        if (!$p) {
            return null;
        }
        return $this->toOutput($p);
    }

    public function create(ProjetInput $input, ?\App\Entity\User $user = null): ProjetOutput
    {
        $projet = new Projet();
        $this->hydrateEntity($projet, $input, $user);
        $this->em->persist($projet);
        $this->em->flush();
        return $this->toOutput($projet);
    }

    public function update(Projet $projet, ProjetInput $input): ProjetOutput
    {
        $ancienStatut = $projet->getStatut();
        $this->hydrateEntity($projet, $input, $projet->getUser());
        $this->em->flush();
        
        // Si le statut a changé, mettre à jour la progression de l'objectif
        if ($ancienStatut !== $projet->getStatut() && $projet->getObjectif()) {
            $this->objectifService->updateProgression($projet->getObjectif());
        }
        
        return $this->toOutput($projet);
    }

    public function delete(Projet $projet): void
    {
        $this->em->remove($projet);
        $this->em->flush();
    }

    private function hydrateEntity(Projet $p, ProjetInput $input, ?\App\Entity\User $user = null): void
    {
        if ($input->nom !== null) $p->setNom($input->nom);
        if ($input->typeProjet !== null) $p->setTypeProjet($input->typeProjet);
        if ($input->categorie !== null) $p->setCategorie($input->categorie);
        if ($input->description !== null) $p->setDescription($input->description);
        if ($input->dateDebut !== null) {
            $p->setDateDebut(new \DateTime($input->dateDebut));
        }
        if ($input->dateFin !== null) {
            $p->setDateFin(new \DateTime($input->dateFin));
        }
        if ($input->budget !== null) $p->setBudget((float)$input->budget);
        if ($input->statut !== null) $p->setStatut($input->statut);
        if ($input->pieceJointes !== null) $p->setPieceJointes($input->pieceJointes);
        if (property_exists($input, 'sppaId')) {
            if ($input->sppaId) {
                $sppa = $this->sppaRepo->find($input->sppaId);
                if ($sppa instanceof SPPA) {
                    $p->setSppa($sppa);
                }
            } else {
                $p->setSppa(null);
            }
        }
        if ($user !== null) {
            $p->setUser($user);
        }
    }

    private function toOutput(Projet $p): ProjetOutput
    {
        $out = new ProjetOutput();
        $out->id = $p->getId();
        $out->nom = $p->getNom();
        $out->typeProjet = $p->getTypeProjet();
        $out->categorie = $p->getCategorie();
        $out->description = $p->getDescription();
        $out->dateDebut = $p->getDateDebut() ? $p->getDateDebut()->format('Y-m-d') : null;
        $out->dateFin = $p->getDateFin() ? $p->getDateFin()->format('Y-m-d') : null;
        $out->budget = $p->getBudget();
        $out->statut = $p->getStatut();
        $out->pieceJointes = $p->getPieceJointes();
        $sppa = $p->getSppa();
        $out->sppaId = $sppa ? $sppa->getId() : null;
        if ($sppa) {
            $out->sppa = ['id' => $sppa->getId(), 'nom' => $sppa->getNom()];
        }
        $user = $p->getUser();
        if ($user) {
            $out->user = ['id' => $user->getId(), 'email' => $user->getEmail(), 'nom' => $user->getNom()];
        }
        
        // Calculer les statistiques du projet
        $out->stats = $this->calculateProjetStats($p);
        
        return $out;
    }
    
    private function calculateProjetStats(Projet $p): array
    {
        $taches = $p->getTaches();
        $totalTaches = $taches->count();
        $tachesTerminees = 0;
        $xpTotal = 0.0;
        $heuresTotal = 0.0;
        
        if ($totalTaches === 0) {
            return [
                'totalTaches' => 0,
                'tachesTerminees' => 0,
                'tauxCompletion' => 0,
                'experienceXp' => 0,
                'niveau' => 1,
                'heuresAccumulees' => 0,
            ];
        }
        
        foreach ($taches as $tache) {
            if ($tache->getStatut() === 'done') {
                $tachesTerminees++;
                $heuresTotal += $tache->getDureeEstimee();
                
                // Calculer l'XP de la tâche
                $xpBase = ($tache->getDifficulte() * 10) 
                        + ($tache->getDureeEstimee() * 2) 
                        + ($tache->getEnthousiasme() * 1);
                
                // Vérifier si en retard
                $dateEcheance = $tache->getDateEcheance();
                if ($dateEcheance && (new \DateTime()) > $dateEcheance) {
                    $xpBase = $xpBase / 2;
                }
                
                $xpTotal += $xpBase;
            }
        }
        
        $tauxCompletion = round(($tachesTerminees / $totalTaches) * 100, 2);
        $niveau = (int) floor($xpTotal / 100) + 1;
        
        return [
            'totalTaches' => $totalTaches,
            'tachesTerminees' => $tachesTerminees,
            'tauxCompletion' => $tauxCompletion,
            'experienceXp' => $xpTotal,
            'niveau' => $niveau,
            'heuresAccumulees' => $heuresTotal,
        ];
    }
}
