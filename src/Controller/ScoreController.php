<?php

namespace App\Controller;

use App\Entity\Score;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ScoreController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface     $validator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator     = $validator;
    }

    #[Route('/score', name: 'score', methods: ['POST'])]
    public function create(Request $request): Response
    {
        // On crée notre nouvelle entité Score à partir des données reçues
        $score = new Score($request->get('name'), $request->get('time', 0));

        // On vérifie que notre entité est valide (par exemple que le time soit > 0)
        // Pour cela nous avons défini des constraintes de validation au niveau de l'entité
        // cf la contrainte "@Assert\GreaterThanOrEqual(0)" au niveau de la propriété "time" de notre entité "Score"
        $errors = $this->validator->validate($score);
        if ($errors->count() > 0) {
            // On retourne un code HTTP 400 indiquant que la requête est invalide
            // @see https://developer.mozilla.org/fr/docs/Web/HTTP/Status/400
            return new Response($errors->__toString(), 400);
        }

        // On enregistre le score en base de données
        $this->entityManager->persist($score);
        $this->entityManager->flush();

        // On retourne un code HTTP 201, signifiant que l'entité a bien été créée
        // @see https://developer.mozilla.org/fr/docs/Web/HTTP/Status/201
        return new Response(null, 201);
    }
}
