<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Form\RapportType;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\RapportRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    #[Route('/api', name: 'api_rapport_index', methods: ['GET', 'POST'])]
    public function rapports(Request $request, RapportRepository $rapportRepository, ChartBuilderInterface $chartBuilder, SerializerInterface $serializer): JsonResponse
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

        // return new JsonResponse([
        //     'rapports' => $rapports,
        //     'rapport' => $rapport,
        //     'form' => $form,
        //     'camembert' => $camembert,
        // ]);
        //$data = [$rapports, $rapport, $form, $camembert];
        $jsonData = $serializer->serialize($rapports, 'json');
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }
}
