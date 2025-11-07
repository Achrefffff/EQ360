<?php

namespace App\Service;

use App\Dto\TacheInput;
use App\Dto\TacheOutput;
use App\Entity\Tache;
use App\Entity\Projet;
use App\Entity\User;
use App\Repository\TacheRepository;
use App\Repository\ProjetRepository;
use App\Repository\SPPARepository;
use App\Service\Gamification\GamificationService;
use Doctrine\ORM\EntityManagerInterface;

class TacheService
{
    private EntityManagerInterface $em;
    private TacheRepository $repo;
    private ProjetRepository $projetRepo;
    private SPPARepository $sppaRepo;
    private GamificationService $gamificationService;

    public function __construct(
        EntityManagerInterface $em, 
        TacheRepository $repo, 
        ProjetRepository $projetRepo,
        SPPARepository $sppaRepo,
        GamificationService $gamificationService
    ) {
        $this->em = $em;
        $this->repo = $repo;
        $this->projetRepo = $projetRepo;
        $this->sppaRepo = $sppaRepo;
        $this->gamificationService = $gamificationService;
    }

    public function create(TacheInput $input, ?User $user = null): Tache
    {
        $t = new Tache();
        $t->setNom($input->nom ?? '');
        $t->setDescription($input->description ?? '');
        $t->setType('general');
        $t->setDomaine('Général');
        $t->setValeurAjoutee('');
        $t->setPriorite($input->priorite ?? 'Moyenne');
        $t->setDureeEstimee($input->dureeEstimee ?? 0.0);
        $t->setDifficulte($input->difficulte ?? 1);
        $t->setEnthousiasme($input->enthousiasme ?? 1);
        $t->setStatut($input->statut ?? 'todo');
        $t->setPieceJoint(null);
        
        if ($input->dateFin) {
            $t->setDateEcheance(new \DateTime($input->dateFin));
        } elseif ($input->dateDebut) {
            $t->setDateEcheance(new \DateTime($input->dateDebut));
        }
        
        if ($input->projetId) {
            $p = $this->projetRepo->find($input->projetId);
            if ($p) $t->setProjet($p);
        }
        
        if ($input->sppaId) {
            $s = $this->sppaRepo->find($input->sppaId);
            if ($s) $t->setSppa($s);
        }
        
        if ($user !== null) {
            $t->setUser($user);
        }
        
        $this->em->persist($t);
        $this->em->flush();
        return $t;
    }

    public function list(int $page = 1, int $limit = 20): array
    {
        $qb = $this->repo->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
        $items = [];
        foreach ($paginator as $item) $items[] = $this->toOutput($item);
        return ['items' => $items, 'total' => count($paginator), 'page' => $page, 'limit' => $limit];
    }

    public function get(int $id): ?TacheOutput
    {
        $t = $this->repo->find($id);
        if (!$t) return null;
        return $this->toOutput($t);
    }

    public function update(Tache $tache, TacheInput $input): TacheOutput
    {
        $ancienStatut = $tache->getStatut();
        
        if ($input->nom !== null) $tache->setNom($input->nom);
        if ($input->description !== null) $tache->setDescription($input->description);
        if ($input->statut !== null) $tache->setStatut($input->statut);
        if ($input->priorite !== null) $tache->setPriorite($input->priorite);
        if ($input->difficulte !== null) $tache->setDifficulte($input->difficulte);
        if ($input->enthousiasme !== null) $tache->setEnthousiasme($input->enthousiasme);
        if ($input->dureeEstimee !== null) $tache->setDureeEstimee($input->dureeEstimee);
        if ($input->dateFin) {
            $tache->setDateEcheance(new \DateTime($input->dateFin));
        } elseif ($input->dateDebut) {
            $tache->setDateEcheance(new \DateTime($input->dateDebut));
        }
        if ($input->projetId) {
            $p = $this->projetRepo->find($input->projetId);
            if ($p instanceof Projet) $tache->setProjet($p);
        }
        
        if (property_exists($input, 'sppaId')) {
            if ($input->sppaId) {
                $s = $this->sppaRepo->find($input->sppaId);
                if ($s) $tache->setSppa($s);
            } else {
                $tache->setSppa(null);
            }
        }
        
        $this->em->flush();
        
        if ($ancienStatut !== 'done' && $tache->getStatut() === 'done') {
            $this->gamificationService->onTacheCompleted($tache);
            
            // Vérifier si le projet est complété
            $projet = $tache->getProjet();
            if ($projet) {
                $this->gamificationService->checkProjetCompletion($projet);
            }
        }
        
        return $this->toOutput($tache);
    }

    public function delete(Tache $tache): void
    {
        $this->em->remove($tache);
        $this->em->flush();
    }

    private function toOutput(Tache $t): TacheOutput
    {
        $out = new TacheOutput();
        $out->id = $t->getId();
        $out->nom = $t->getNom();
        $out->description = $t->getDescription();
        $out->priorite = $t->getPriorite();
        $out->statut = $t->getStatut();
        $out->dureeEstimee = $t->getDureeEstimee();
        $out->difficulte = $t->getDifficulte();
        $out->enthousiasme = $t->getEnthousiasme();
        $sppa = $t->getSppa();
        $out->sppaId = $sppa ? $sppa->getId() : null;
        if ($sppa) $out->sppa = ['id' => $sppa->getId(), 'nom' => $sppa->getNom()];
        $dateEcheance = $t->getDateEcheance();
        $out->dateDebut = $dateEcheance ? $dateEcheance->format('Y-m-d') : null;
        $out->dateFin = $dateEcheance ? $dateEcheance->format('Y-m-d') : null;
        $p = $t->getProjet();
        if ($p) $out->projet = ['id' => $p->getId(), 'nom' => $p->getNom()];
        return $out;
    }
}
