<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentaryController extends AbstractController
{
    #[Route('{post}/commentary/{user}/add', name: 'app_commentary_add')]
    public function add(): Response
    {
        return $this->render('commentary/new.html.twig', [
            'controller_name' => 'CommentaryController',
        ]);
    }
}
