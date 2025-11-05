<?php

namespace App\Controller\Api;

use App\Dto\ProjetInput;
use App\Service\ProjetService;
use App\Entity\Projet;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/projets")
 */
class ProjetController extends AbstractController
{
    private ProjetService $service;
    private ProjetRepository $repo;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(ProjetService $service, ProjetRepository $repo, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->service = $service;
        $this->repo = $repo;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="projet_list", methods={"GET"})
     */
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = min(100, max(1, (int)$request->query->get('limit', 20)));
        $result = $this->service->list($page, $limit);
        return $this->json($result);
    }

    /**
     * @Route("/{id}", name="projet_show", methods={"GET"})
     */
    public function show(int $id): JsonResponse
    {
        $out = $this->service->get($id);
        if (!$out) {
            return $this->json(['error' => 'Projet not found'], Response::HTTP_NOT_FOUND);
        }
        return $this->json($out, Response::HTTP_OK, [], ['groups' => 'projet:read']);
    }

    /**
     * @Route("", name="projet_create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $content = (string)$request->getContent();
        try {
            /** @var ProjetInput $input */
            $input = $this->serializer->deserialize($content, ProjetInput::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON payload'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            $err = [];
            foreach ($errors as $violation) {
                $err[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return $this->json(['errors' => $err], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        $out = $this->service->create($input, $user);
        return $this->json($out, Response::HTTP_CREATED, [], ['groups' => 'projet:read']);
    }

    /**
     * @Route("/{id}", name="projet_update", methods={"PUT","PATCH"})
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $projet = $this->repo->find($id);
        if (!$projet) {
            return $this->json(['error' => 'Projet not found'], Response::HTTP_NOT_FOUND);
        }

        $content = (string)$request->getContent();
        try {
            $input = $this->serializer->deserialize($content, ProjetInput::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid JSON payload'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            $err = [];
            foreach ($errors as $violation) {
                $err[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return $this->json(['errors' => $err], Response::HTTP_BAD_REQUEST);
        }

        $out = $this->service->update($projet, $input);
        return $this->json($out, Response::HTTP_OK, [], ['groups' => 'projet:read']);
    }

    /**
     * @Route("/{id}", name="projet_delete", methods={"DELETE"})
     */
    public function delete(int $id): JsonResponse
    {
        $projet = $this->repo->find($id);
        if (!$projet) {
            return $this->json(['error' => 'Projet not found'], Response::HTTP_NOT_FOUND);
        }
        // permission check: only owner or ROLE_ADMIN
        $user = $this->getUser();
        if ($user === null || ($projet->getUser() && $projet->getUser()->getId() !== $user->getId() && !$this->isGranted('ROLE_ADMIN'))) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $this->service->delete($projet);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
