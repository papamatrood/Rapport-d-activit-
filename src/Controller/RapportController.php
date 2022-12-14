<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Form\RapportType;
use App\Repository\RapportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// #[Route('/rapport')]
class RapportController extends AbstractController
{
    #[Route('/', name: 'app_rapport_index', methods: ['GET', 'POST'])]
    public function index(Request $request, RapportRepository $rapportRepository): Response
    {
        $rapport = new Rapport();
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rapportRepository->save($rapport, true);

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport/index.html.twig', [
            'rapports' => $rapportRepository->findAll(),
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    // #[Route('/rapport/new', name: 'app_rapport_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, RapportRepository $rapportRepository): Response
    // {
    //     $rapport = new Rapport();
    //     $form = $this->createForm(RapportType::class, $rapport);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $rapportRepository->save($rapport, true);

    //         return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('rapport/new.html.twig', [
    //         'rapport' => $rapport,
    //         'form' => $form,
    //     ]);
    // }


    #[Route('/rapport/{id}/edit', name: 'app_rapport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rapport $rapport, RapportRepository $rapportRepository): Response
    {
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rapportRepository->save($rapport, true);

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rapport/edit.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/rapport/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, RapportRepository $rapportRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rapport->getId(), $request->request->get('_token'))) {
            $rapportRepository->remove($rapport, true);
        }

        return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
    }
}
