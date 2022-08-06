<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController {

    #[Route('/')]
    public function test(): Response {
        return $this->render('base.html.twig');
    }

    #[Route("/user/{username}")]
    public function profile($username): Response
    {
        die($username);
    }

}
