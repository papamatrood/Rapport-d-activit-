<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Form\RapportType;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\RapportRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rapport')]
#[IsGranted('ROLE_USER')]
class RapportController extends AbstractController
{
    #[Route('/', name: 'app_rapport_index', methods: ['GET', 'POST'])]
    public function index(Request $request, RapportRepository $rapportRepository, ChartBuilderInterface $chartBuilder): Response
    {
        $rapports = $rapportRepository->findAll();
        $rapport = new Rapport();
        $installations = 0;
        $interqualites = 0;
        $interdepannages = 0;
        $visites = 0;
        $recuperations = 0;

        foreach ($rapports as $value) {
            $installations += $value->getInstallation();
            $interqualites += $value->getInterqualite();
            $interdepannages += $value->getInterdepannage();
            $visites += $value->getVisite();
            $recuperations += $value->getRecuperation();
        }

        $camembert = $chartBuilder->createChart(Chart::TYPE_PIE);

        $camembert->setData([
            'labels' => ['Installations', 'Inter-Qualités', 'Inter-dépannages', 'Visites', 'Récuperations'],
            'datasets' => [
                [
                    'label' => 'Graphe du rapport d\'activité',
                    'backgroundColor' => ['#6666ff', '#b366ff', '#66b3ff', '#b2fdb3', '#fc4f4c'],
                    'data' => [$installations, $interqualites, $interdepannages, $visites, $recuperations],
                ],
            ],
        ]);

        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rapportRepository->save($rapport, true);

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rapport/index.html.twig', [
            'rapports' => $rapports,
            'rapport' => $rapport,
            'form' => $form,
            'camembert' => $camembert,
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


    #[Route('/{id}/edit', name: 'app_rapport_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, RapportRepository $rapportRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rapport->getId(), $request->request->get('_token'))) {
            $rapportRepository->remove($rapport, true);
        }

        return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
    }
}
