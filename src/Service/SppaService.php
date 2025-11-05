<?php

namespace App\Service;

use App\Dto\SppaInput;
use App\Dto\SppaOutput;
use App\Entity\SPPA;
use App\Entity\User;
use App\Repository\SPPARepository;
use Doctrine\ORM\EntityManagerInterface;

class SppaService
{
    private EntityManagerInterface $em;
    private SPPARepository $repo;

    public function __construct(EntityManagerInterface $em, SPPARepository $repo)
    {
        $this->em = $em;
        $this->repo = $repo;
    }

    public function create(SppaInput $input, ?User $user = null): SPPA
    {
        $s = new SPPA();
        // required/minimum fields
        if (property_exists($input, 'nom')) $s->setNom($input->nom);
        if (property_exists($input, 'description')) $s->setDescription($input->description ?? '');

        // optional fields with sensible defaults to avoid DB NOT NULL errors
        if (property_exists($input, 'avatar')) $s->setAvatar($input->avatar ?? null);
        if (property_exists($input, 'couleur')) $s->setCouleur($input->couleur ?? null);
        if (property_exists($input, 'competences') && is_array($input->competences)) $s->setCompetences($input->competences);
        else $s->setCompetences([]);
        if (property_exists($input, 'valeurs') && is_array($input->valeurs)) $s->setValeurs($input->valeurs);
        else $s->setValeurs([]);
        // numeric fields: provide default 0.0 if not provided
        if (property_exists($input, 'niveau') && $input->niveau !== null) $s->setNiveau((float)$input->niveau);
        else $s->setNiveau(0.0);
        if (property_exists($input, 'heuresAccumulees') && $input->heuresAccumulees !== null) $s->setHeuresAccumulees((float)$input->heuresAccumulees);
        else $s->setHeuresAccumulees(0.0);
        if (property_exists($input, 'experienceXp') && $input->experienceXp !== null) $s->setExperienceXp((float)$input->experienceXp);
        else $s->setExperienceXp(0.0);
        if ($user !== null) {
            $s->setUser($user);
        }

        $this->em->persist($s);
        $this->em->flush();
        return $s;
    }

    public function list(int $page = 1, int $limit = 20): array
    {
        $qb = $this->repo->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($qb);
        $items = [];
        foreach ($paginator as $item) {
            $items[] = $this->toOutput($item);
        }
        return ['items' => $items, 'total' => count($paginator), 'page' => $page, 'limit' => $limit];
    }

    public function get(int $id): ?SppaOutput
    {
        $s = $this->repo->find($id);
        if (!$s) return null;
        return $this->toOutput($s);
    }

    public function update(SPPA $sppa, SppaInput $input): SppaOutput
    {
        if (property_exists($input, 'nom') && $input->nom !== null) $sppa->setNom($input->nom);
        if (property_exists($input, 'description')) $sppa->setDescription($input->description ?? null);
        $this->em->flush();
        return $this->toOutput($sppa);
    }

    public function delete(SPPA $sppa): void
    {
        $this->em->remove($sppa);
        $this->em->flush();
    }

    private function toOutput(SPPA $s): SppaOutput
    {
        $out = new SppaOutput();
        $out->id = $s->getId();
        $out->nom = method_exists($s, 'getNom') ? $s->getNom() : null;
        $out->description = method_exists($s, 'getDescription') ? $s->getDescription() : null;
        return $out;
    }
}
