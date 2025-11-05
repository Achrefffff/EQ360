<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    /**
     * This route should be intercepted by the JSON login firewall. If you see the response
     * from this method it means the firewall didn't handle the request.
     *
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        return new JsonResponse([
            'error' => 'Login route reached controller - the firewall did not intercept the request. Check your security firewall configuration.'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
