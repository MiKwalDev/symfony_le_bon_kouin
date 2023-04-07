<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/dashboard/{user}/post/create', name: 'app_post_create')]
    public function create(User $user, PostType $form, Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $post->setUser($user);
        $post->setIsVisible(false);

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
            'post' => $post
        ]);
    }

    #[Route('/dashboard/post/{post}/togglevisibility', name: 'app_post_togglevisibility')]
    public function toggleVisibility(Post $post, PostRepository $repo, EntityManagerInterface $entityManager): Response
    {
        if ($post->isIsVisible()) {
            $post->setIsVisible(false);
        } else {
            $post->setIsVisible(true);
        }
        
        
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
