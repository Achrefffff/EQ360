<?php

namespace App\Controller\Api;

use App\Dto\TacheInput;
use App\Service\TacheService;
use App\Repository\TacheRepository;
use App\Entity\Tache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/taches")
 */
class TacheController extends AbstractController
{
    private TacheService $service;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private TacheRepository $repo;

    public function __construct(TacheService $service, TacheRepository $repo, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->service = $service;
        $this->repo = $repo;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /** @Route("", name="tache_create", methods={"POST"}) */
    public function create(Request $request): JsonResponse
    {
        $content = (string)$request->getContent();
        try {
            $input = $this->serializer->deserialize($content, TacheInput::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid payload'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            $err = [];
            foreach ($errors as $v) $err[] = $v->getPropertyPath() . ': ' . $v->getMessage();
            return $this->json(['errors' => $err], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Authentication required'], Response::HTTP_UNAUTHORIZED);
        }

        $t = $this->service->create($input, $user);
        return $this->json(['id' => $t->getId()], Response::HTTP_CREATED);
    }

    /** @Route("", name="tache_list", methods={"GET"}) */
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = min(100, max(1, (int)$request->query->get('limit', 20)));
        $result = $this->service->list($page, $limit);
        return $this->json($result);
    }

    /** @Route("/{id}", name="tache_show", methods={"GET"}) */
    public function show(int $id): JsonResponse
    {
        $out = $this->service->get($id);
        if (!$out) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return $this->json($out, Response::HTTP_OK, [], ['groups' => 'tache:read']);
    }

    /** @Route("/{id}", name="tache_update", methods={"PUT","PATCH"}) */
    public function update(Request $request, int $id): JsonResponse
    {
        $tache = $this->repo->find($id);
        if (!$tache) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);

        try {
            $input = $this->serializer->deserialize((string)$request->getContent(), TacheInput::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid payload'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            $err = [];
            foreach ($errors as $v) $err[] = $v->getPropertyPath() . ': ' . $v->getMessage();
            return $this->json(['errors' => $err], Response::HTTP_BAD_REQUEST);
        }

        $out = $this->service->update($tache, $input);
        return $this->json($out, Response::HTTP_OK, [], ['groups' => 'tache:read']);
    }

    /** @Route("/{id}", name="tache_delete", methods={"DELETE"}) */
    public function delete(int $id): JsonResponse
    {
        $tache = $this->repo->find($id);
        if (!$tache) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $this->service->delete($tache);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
