<?php

namespace App\Controller;

use App\Repository\ScoreRepository;
use App\Service\CardGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private CardGenerator   $cardGenerator;
    private ScoreRepository $scoreRepository;
    private int             $scores;
    private int             $pairs;
    private int             $time;

    public function __construct(
        CardGenerator   $cardGenerator,
        ScoreRepository $scoreRepository,
        int             $scores,
        int             $pairs,
        int             $time
    )
    {
        $this->cardGenerator   = $cardGenerator;
        $this->scoreRepository = $scoreRepository;
        $this->scores          = $scores;
        $this->pairs           = $pairs;
        $this->time            = $time;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // On récupère les scores dans notre base de données
        $scores = $this->scoreRepository->findBestScore($this->scores);

        // On génère les cartes de notre jeu
        $cards = $this->cardGenerator->generateCards($this->pairs);

        // On génère le code HTML de la page
        return $this->render('home/index.html.twig', [
            'scores' => $scores,
            'cards'  => $cards,
            'pairs'  => $this->pairs,
            'time'   => $this->time,
        ]);
    }
}
