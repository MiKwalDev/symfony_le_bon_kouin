<?php

namespace App\Controller;

use App\Entity\Bank;
use App\Form\BankType;
use App\Repository\BankRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\Rule\Parameters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BankController extends AbstractController
{
    #[Route('/dashboard/bank/edit/{bank}', name: 'app_bank_edit')]
    public function edit(BankType $form, Request $request, EntityManagerInterface $entityManager, BankRepository $repo, Bank $bank): Response
    {
        $amount = $repo->find($bank);

        $form = $this->createForm(BankType::class, $amount);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $amount->setAmount($amount->getAmount() + $request->request->get('addAmount'));

            $entityManager->persist($amount);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('bank/edit.html.twig', [
            'form' => $form->createView(),
            'bank' => $amount
        ]);
    }
}
