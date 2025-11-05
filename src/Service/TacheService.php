<?php

namespace App\Service;

use App\Dto\TacheInput;
use App\Dto\TacheOutput;
use App\Entity\Tache;
use App\Entity\Projet;
use App\Entity\User;
use App\Repository\TacheRepository;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;

class TacheService
{
    private EntityManagerInterface $em;
    private TacheRepository $repo;
    private ProjetRepository $projetRepo;

    public function __construct(EntityManagerInterface $em, TacheRepository $repo, ProjetRepository $projetRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->projetRepo = $projetRepo;
    }

    public function create(TacheInput $input, ?User $user = null): Tache
    {
        $t = new Tache();
        $t->setNom($input->titre ?? '');
        $t->setDescription($input->description ?? null);
        // The Tache entity currently stores a single date field `dateEcheance`.
        // Map incoming dateDebut/dateFin to dateEcheance (prefer dateFin if provided).
        if ($input->dateFin) {
            $t->setDateEcheance((new \DateTime($input->dateFin))->format('Y-m-d'));
        } elseif ($input->dateDebut) {
            $t->setDateEcheance((new \DateTime($input->dateDebut))->format('Y-m-d'));
        }
        if ($input->projetId) {
            $p = $this->projetRepo->find($input->projetId);
            if ($p) $t->setProjet($p);
        }
        // Provide sensible defaults for NOT NULL columns to avoid DB constraint errors.
        $t->setType('general');
        $t->setDomaine('Général');
        $t->setValeurAjoutee('');
        $t->setPriorite('Moyenne');
        $t->setDureeEstimee(0.0);
        $t->setDifficulte(1);
        $t->setEnthousiasme(1);
        $t->setStatut('todo');
        $t->setPieceJoint(null);

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
        if ($input->titre !== null) $tache->setNom($input->titre);
        if ($input->description !== null) $tache->setDescription($input->description);
        if ($input->dateFin) {
            $tache->setDateEcheance((new \DateTime($input->dateFin))->format('Y-m-d'));
        } elseif ($input->dateDebut) {
            $tache->setDateEcheance((new \DateTime($input->dateDebut))->format('Y-m-d'));
        }
        if ($input->projetId) {
            $p = $this->projetRepo->find($input->projetId);
            if ($p instanceof Projet) $tache->setProjet($p);
        }
        $this->em->flush();
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
        $out->titre = $t->getNom();
        $out->description = $t->getDescription();
    // Because entity stores only dateEcheance (string), map it to both dateDebut/dateFin in output for now.
    $out->dateDebut = $t->getDateEcheance() ? $t->getDateEcheance() : null;
    $out->dateFin = $t->getDateEcheance() ? $t->getDateEcheance() : null;
        $p = $t->getProjet();
        if ($p) $out->projet = ['id' => $p->getId(), 'nom' => $p->getNom()];
        return $out;
    }
}
