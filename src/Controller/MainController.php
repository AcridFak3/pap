<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user) {
            return $this->render('main/dashboard.html.twig');
        }

        return $this->render('main/homepage.html.twig');
    }
}
