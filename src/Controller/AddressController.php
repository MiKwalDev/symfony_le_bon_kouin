<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\User;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    #[Route('/dashboard/{user}/address/create', name: 'app_address_create')]
    public function create(User $user, AddressType $form, Request $request, EntityManagerInterface $entityManager): Response
    {
        $address = new Address();
        $address->setUser($user);

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($address);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('address/new.html.twig', [
            'form' => $form->createView(),
            'address' => $address
        ]);
    }

    #[Route('/dashboard/edit/{address}', name: 'app_address_edit')]
    public function edit(AddressType $form, Request $request, EntityManagerInterface $entityManager, AddressRepository $repo, Address $address)
    {
        $addressToEdit = $repo->find($address);

        $form = $this->createForm(AddressType::class, $addressToEdit);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($addressToEdit);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('address/new.html.twig', [
            'form' => $form->createView(),
            'address' => $addressToEdit
        ]);
    }

    #[Route('/dashboard/address/delete/{address}', name:"app_address_delete", methods:['DELETE'])]
    public function delete(AddressRepository $repo, Address $address): Response
    {
        $repo->remove($address, true);
        
        return $this->redirectToRoute('app_dashboard');

    }
}
