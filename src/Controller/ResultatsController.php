<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\DonneeEconomiqueRepository;
use App\Repository\LocationRepository;
use App\Repository\PretRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @Route("/resultat")
 */
class ResultatsController extends AbstractController
{
    /**
     * @Route("/week", name="resultats_week", methods={"GET"})
     */
    public function index(PretRepository $pretRepository, LocationRepository $locationRepository): Response
    {
        return $this->render('resultat/index.html.twig', [
            'prets_categorie_7_jours_glissants' => $pretRepository->findPretsByCategorieByDateDebutForLastWeek(),
            'montant_total_prets' => $pretRepository->findMontantTotalPretsForLastWeek()[0],
            'locations_categorie_7_jours_glissants' => $locationRepository->findLocationsByCategorieByDateDebutForLastWeek(),
            'montant_total_locations' => $locationRepository->findMontantTotalLocationsForLastWeek()[0]
        ]);
    }

    /**
     * @Route("/{id}", name="resultats_produit", methods={"GET"})
     */
    public function showProductDatas($id,
                                     ProduitRepository $produitRepository,
                                     DonneeEconomiqueRepository $donneeEconomiqueRepository,
                                     ChartBuilderInterface $chartBuilder): Response
    {
        $donnees = $donneeEconomiqueRepository->findMontantPretsEtLocationByProduct($id);
        $produit = $produitRepository->find($id);

        $labels = [];
        $datasLocation = [];
        $datasRente = [];


        foreach ($donnees as $donnee){
            $labels[] =  'Semaine ' . $donnee['Semaine'];
            $datasLocation[] = (float)$donnee['Somme Location'];
            $datasRente[] = (float)$donnee['Somme Rente'];
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Montant hebdomadaire encaissé',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $datasLocation,
                ],
                [
                    'label' => 'Montant hebdomadaire décaissé',
                    'backgroundColor' => 'rgb(132, 99, 255)',
                    'borderColor' => 'rgb(132, 99, 255)',
                    'data' => $datasRente,
                ]
            ],
        ]);

        $chart->setOptions([
            'responsive' =>'false',
            'legend' => [
                'position' => 'bottom',
                'align' => 'end'
            ],
            'title' => [
                'text' => $produit->getNom(),
                'display' => 'true'
            ],
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => max($datasLocation)]],
                ],
            ],
        ]);

        return $this->render('resultat/show.html.twig', [
            'chart' => $chart,
            'produit' => $produit
        ]);
    }
}
