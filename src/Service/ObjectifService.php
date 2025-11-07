<?php

namespace App\Service;

use App\Dto\ObjectifInput;
use App\Dto\ObjectifOutput;
use App\Entity\Objectif;
use App\Entity\Projet;
use App\Repository\ObjectifRepository;
use App\Repository\ProjetRepository;
use App\Repository\SPPARepository;
use App\Service\Gamification\GamificationService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class ObjectifService
{
    private EntityManagerInterface $em;
    private ObjectifRepository $repo;
    private ProjetRepository $projetRepo;
    private SPPARepository $sppaRepo;
    private GamificationService $gamificationService;

    public function __construct(EntityManagerInterface $em, ObjectifRepository $repo, ProjetRepository $projetRepo, SPPARepository $sppaRepo, GamificationService $gamificationService)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->projetRepo = $projetRepo;
        $this->sppaRepo = $sppaRepo;
        $this->gamificationService = $gamificationService;
    }

    public function create(ObjectifInput $input, ?User $user = null): Objectif
    {
        $obj = new Objectif();
        $obj->setNom($input->titre ?? '');
        $obj->setDescription($input->description ?? '');
        $obj->setDomaineVie($input->domaineVie ?? 'Général');
        $obj->setHorizon($input->horizon ?? 'Moyen');
        $obj->setPriorite($input->priorite ?? 'Moyenne');
        $obj->setStatut($input->statut ?? 'en_cours');
        $obj->setProgression(0.0);
        
        if ($input->dateDebut) {
            $obj->setDateDebut(new \DateTime($input->dateDebut));
        } else {
            $obj->setDateDebut(new \DateTime());
        }
        
        if ($input->dateFin) {
            $obj->setDateFin(new \DateTime($input->dateFin));
        }

        if ($user !== null) {
            $obj->setUser($user);
        }
        
        if ($input->sppaId) {
            $s = $this->sppaRepo->find($input->sppaId);
            if ($s) $obj->setSppa($s);
        }
        
        $this->em->persist($obj);
        $this->em->flush();
        
        // Associer les projets
        if ($input->projetIds && is_array($input->projetIds)) {
            foreach ($input->projetIds as $projetId) {
                $p = $this->projetRepo->find($projetId);
                if ($p) {
                    $p->setObjectif($obj);
                }
            }
            $this->em->flush();
            $this->updateProgression($obj);
        }
        
        return $obj;
    }

    public function list(int $page = 1, int $limit = 20): array
    {
        $qb = $this->repo->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
        $items = [];
        foreach ($paginator as $item) {
            $items[] = $this->toOutput($item);
        }
        return ['items' => $items, 'total' => count($paginator), 'page' => $page, 'limit' => $limit];
    }

    public function get(int $id): ?ObjectifOutput
    {
        $o = $this->repo->find($id);
        if (!$o) return null;
        return $this->toOutput($o);
    }

    public function update(Objectif $objectif, ObjectifInput $input): ObjectifOutput
    {
        if ($input->titre !== null) $objectif->setNom($input->titre);
        if ($input->description !== null) $objectif->setDescription($input->description);
        if ($input->domaineVie !== null) $objectif->setDomaineVie($input->domaineVie);
        if ($input->horizon !== null) $objectif->setHorizon($input->horizon);
        if ($input->priorite !== null) $objectif->setPriorite($input->priorite);
        if ($input->statut !== null) $objectif->setStatut($input->statut);
        
        if ($input->dateDebut) {
            $objectif->setDateDebut(new \DateTime($input->dateDebut));
        }
        if ($input->dateFin) {
            $objectif->setDateFin(new \DateTime($input->dateFin));
        }
        
        if (property_exists($input, 'sppaId')) {
            if ($input->sppaId) {
                $s = $this->sppaRepo->find($input->sppaId);
                if ($s) $objectif->setSppa($s);
            } else {
                $objectif->setSppa(null);
            }
        }
        
        // Gérer les projets
        if (property_exists($input, 'projetIds')) {
            // Retirer l'objectif de tous les anciens projets
            foreach ($objectif->getProjets() as $oldProjet) {
                $oldProjet->setObjectif(null);
            }
            
            // Associer les nouveaux projets
            if ($input->projetIds && is_array($input->projetIds)) {
                foreach ($input->projetIds as $projetId) {
                    $p = $this->projetRepo->find($projetId);
                    if ($p) {
                        $p->setObjectif($objectif);
                    }
                }
            }
            
            $this->em->flush();
            $this->updateProgression($objectif);
        }
        
        $this->em->flush();
        return $this->toOutput($objectif);
    }

    public function delete(Objectif $objectif): void
    {
        $this->em->remove($objectif);
        $this->em->flush();
    }

    private function toOutput(Objectif $o): ObjectifOutput
    {
        $out = new ObjectifOutput();
        $out->id = $o->getId();
        $out->titre = $o->getNom();
        $out->description = $o->getDescription();
        $out->domaineVie = $o->getDomaineVie();
        $out->horizon = $o->getHorizon();
        $out->priorite = $o->getPriorite();
        $out->statut = $o->getStatut();
        $out->progression = $o->getProgression();
        $out->dateDebut = $o->getDateDebut() ? $o->getDateDebut()->format('Y-m-d') : null;
        $out->dateFin = $o->getDateFin() ? $o->getDateFin()->format('Y-m-d') : null;
        
        // Retourner tous les projets
        $projets = [];
        foreach ($o->getProjets() as $projet) {
            $projets[] = [
                'id' => $projet->getId(),
                'nom' => $projet->getNom(),
                'statut' => $projet->getStatut(),
            ];
        }
        $out->projets = $projets;
        
        $s = $o->getSppa();
        $out->sppaId = $s ? $s->getId() : null;
        if ($s) $out->sppa = ['id' => $s->getId(), 'nom' => $s->getNom()];
        
        return $out;
    }
    
    public function updateProgression(Objectif $objectif): void
    {
        $ancienStatut = $objectif->getStatut();
        
        $projets = $objectif->getProjets();
        $totalProjets = $projets->count();
        
        if ($totalProjets === 0) {
            $objectif->setProgression(0.0);
            $this->em->flush();
            return;
        }
        
        $projetsTermines = 0;
        foreach ($projets as $projet) {
            if ($projet->getStatut() === 'termine') {
                $projetsTermines++;
            }
        }
        
        $progression = ($projetsTermines / $totalProjets) * 100;
        $objectif->setProgression($progression);
        
        // Si tous les projets sont terminés, marquer l'objectif comme atteint
        if ($progression >= 100) {
            $objectif->setStatut('atteint');
        }
        
        $this->em->flush();
        
        // Si l'objectif vient d'être atteint, donner le bonus XP au SPPA
        if ($ancienStatut !== 'atteint' && $objectif->getStatut() === 'atteint') {
            $this->gamificationService->onObjectifAtteint($objectif);
        }
    }

    // additional methods (list/get/update/delete) can be implemented following ProjetService pattern
}
