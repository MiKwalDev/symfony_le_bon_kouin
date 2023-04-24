<?php

namespace App\Controller;

use App\Entity\Acquisition;
use App\Entity\Post;
use App\Form\AcquisitionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcquisitionController extends AbstractController
{
    #[Route('/acquisition/{post}/buy', name: 'app_acquisition')]
    public function buy(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userAddresses = $user->getAddress();
        $bank = $user->getBank();

        if ( $userAddresses->isEmpty() ) {
            return $this->redirectToRoute('app_address_create', [ "user" => $user->getId()]);
        } else {
            $acquisition = new Acquisition();
            $acquisition->setUser($user);
            $acquisition->setPost($post);

            $form = $this->createForm(AcquisitionType::class, $acquisition);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

                if ($bank->getAmount() < $post->getPrice()) {
                    $this->addFlash('error', 'Vous n\'avez pas assez d\'argent pour acheter ce produit');
                } else {
                    $bank->setAmount($bank->getAmount() - $post->getPrice());
                    $post->setIsVisible(false);

                    $entityManager->persist($post);
                    $entityManager->persist($bank);
                    $entityManager->persist($acquisition);
                    $entityManager->flush();
    
                    return $this->redirectToRoute('app_dashboard');
                }

            }

            return $this->render('acquisition/index.html.twig', [
                'user' => $user,
                'userAddresses' => $userAddresses,
                'bank' => $bank,
                'form' => $form->createView()
            ]);
        }

        /* $acquisition = new Acquisition();
        $acquisition->setUser($user);
        $acquisition->setPost($post);

        $form = $this->createForm(AcquisitionType::class, $acquisition);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($acquisition);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('acquisition/index.html.twig', [
            'controller_name' => 'AcquisitionController',
        ]); */
    }
}
