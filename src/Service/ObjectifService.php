<?php

namespace App\Service;

use App\Dto\ObjectifInput;
use App\Dto\ObjectifOutput;
use App\Entity\Objectif;
use App\Entity\Projet;
use App\Repository\ObjectifRepository;
use App\Repository\ProjetRepository;
use App\Repository\SPPARepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class ObjectifService
{
    private EntityManagerInterface $em;
    private ObjectifRepository $repo;
    private ProjetRepository $projetRepo;
    private SPPARepository $sppaRepo;

    public function __construct(EntityManagerInterface $em, ObjectifRepository $repo, ProjetRepository $projetRepo, SPPARepository $sppaRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->projetRepo = $projetRepo;
        $this->sppaRepo = $sppaRepo;
    }

    public function create(ObjectifInput $input, ?User $user = null): Objectif
    {
        $obj = new Objectif();
        // Map input.titre -> entity.nom
        $obj->setNom($input->titre ?? '');
        $obj->setDescription($input->description ?? null);

        // The Objectif entity has several NOT NULL fields (domaineVie, horizon, priorite, statut,
        // dateDebut, progression). Provide sensible defaults so creating an objectif with a minimal
        // payload doesn't trigger a DB integrity error. Preferably these should be supplied by the
        // client and validated via DTOs, but defaults are safer for now.
        $obj->setDomaineVie('Général');
        $obj->setHorizon('Moyen');
        $obj->setPriorite('Moyenne');
        $obj->setStatut('en_cours');
        $obj->setDateDebut(new \DateTime());
        $obj->setProgression(0.0);

        if ($user !== null) {
            $obj->setUser($user);
        }
        if ($input->projetId !== null) {
            $p = $this->projetRepo->find($input->projetId);
            if ($p) $obj->setProjet($p);
        }

        // SPPA is a non-nullable relation on Objectif. Prefer explicit sppaId from input.
        $sppa = null;
        if (property_exists($input, 'sppaId') && $input->sppaId !== null) {
            $sppa = $this->sppaRepo->find($input->sppaId);
        }
        // If not provided, try to find a SPPA for the current user
        if ($sppa === null && $user !== null) {
            $sppa = $this->sppaRepo->findOneBy(['user' => $user]);
        }
        // Fallback: take any existing SPPA to satisfy NOT NULL constraint (safe default)
        if ($sppa === null) {
            $sppa = $this->sppaRepo->findOneBy([]);
        }
        if ($sppa !== null) {
            $obj->setSppa($sppa);
        }
        $this->em->persist($obj);
        $this->em->flush();
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
        if ($input->projetId !== null) {
            $p = $this->projetRepo->find($input->projetId);
            if ($p instanceof Projet) $objectif->setProjet($p);
        }
        if (property_exists($input, 'sppaId') && $input->sppaId !== null) {
            $s = $this->sppaRepo->find($input->sppaId);
            if ($s) $objectif->setSppa($s);
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
        // Map entity.nom -> output.titre for API consistency
        $out->titre = $o->getNom();
        $out->description = $o->getDescription();
        $p = $o->getProjet();
        if ($p) $out->projet = ['id' => $p->getId(), 'nom' => $p->getNom()];
        return $out;
    }

    // additional methods (list/get/update/delete) can be implemented following ProjetService pattern
}
