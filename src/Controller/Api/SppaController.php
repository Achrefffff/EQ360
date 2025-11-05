<?php

namespace App\Controller\Api;

use App\Dto\SppaInput;
use App\Service\SppaService;
use App\Repository\SPPARepository;
use App\Entity\SPPA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/sppas")
 */
class SppaController extends AbstractController
{
    private SppaService $service;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private SPPARepository $repo;

    public function __construct(SppaService $service, SPPARepository $repo, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->service = $service;
        $this->repo = $repo;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /** @Route("", name="sppa_create", methods={"POST"}) */
    public function create(Request $request): JsonResponse
    {
        $content = (string)$request->getContent();
        try {
            $input = $this->serializer->deserialize($content, SppaInput::class, 'json');
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

        $s = $this->service->create($input, $user);
        return $this->json(['id' => $s->getId()], Response::HTTP_CREATED);
    }

    /** @Route("", name="sppa_list", methods={"GET"}) */
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = min(100, max(1, (int)$request->query->get('limit', 20)));
        $result = $this->service->list($page, $limit);
        return $this->json($result);
    }

    /** @Route("/{id}", name="sppa_show", methods={"GET"}) */
    public function show(int $id): JsonResponse
    {
        $out = $this->service->get($id);
        if (!$out) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        return $this->json($out, Response::HTTP_OK, [], ['groups' => 'sppa:read']);
    }

    /** @Route("/{id}", name="sppa_update", methods={"PUT","PATCH"}) */
    public function update(Request $request, int $id): JsonResponse
    {
        $s = $this->repo->find($id);
        if (!$s) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);

        try {
            $input = $this->serializer->deserialize((string)$request->getContent(), SppaInput::class, 'json');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Invalid payload'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            $err = [];
            foreach ($errors as $v) $err[] = $v->getPropertyPath() . ': ' . $v->getMessage();
            return $this->json(['errors' => $err], Response::HTTP_BAD_REQUEST);
        }

        $out = $this->service->update($s, $input);
        return $this->json($out, Response::HTTP_OK, [], ['groups' => 'sppa:read']);
    }

    /** @Route("/{id}", name="sppa_delete", methods={"DELETE"}) */
    public function delete(int $id): JsonResponse
    {
        $s = $this->repo->find($id);
        if (!$s) return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        $this->service->delete($s);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
