<?php

namespace App\Controller;

use App\Entity\Commentary;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentaryType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/post/{post}', name: 'app_post')]
    public function show(Post $post, CommentaryType $form, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Commentary();
        $comment->setUser($this->getUser());
        $comment->setPost($post);
        
        $form = $this->createForm(CommentaryType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_post', ['post' => $post->getId()] );
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

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
