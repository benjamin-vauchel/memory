<?php
require_once dirname(__DIR__) . '/src/Score.php';
require_once dirname(__DIR__) . '/src/ScoreRepository.php';

try {
    // On récupére les éventuelles données postées
    $name = $_POST['name'] ?? null;
    $time = $_POST['time'] ?? null;

    // On ne fait jamais confiance aux données provenant de l'extérieur
    // On les vérifie avant de les insérer en base de données
    if (null === $name) {
        throw new Exception('Le nom ne doit pas être vide');
    }
    if (null === $time) {
        throw new Exception('Le temps ne doit pas être vide');
    }
    if (false === filter_var($_REQUEST['time'], FILTER_VALIDATE_INT)) {
        throw new Exception('Le temps doit être un entier');
    }

    // On crée le nouveau score à partir des données reçues
    $score = new Score(null, $name, $time);

    // On enregistre le score en base de données
    $scoreRepository = new ScoreRepository(new PDO('sqlite:' . dirname(__DIR__) . '/database.sqlite'));
    $scoreRepository->save($score);

    // On envoie le code HTTP 201 qui signifie que le score a bien été enregistré en base
    // @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/201
    http_response_code(201);

} catch (Exception $e) {
    // On envoie le code HTTP 400 qui signifie que la requête est invalide
    // @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/400
    http_response_code(400);

    // On affiche le message d'erreur.
    // Attention : en affichant ainsi l'ensemble des messages d'erreurs
    // il est possible d'exposer des informations compromettant
    // la sécurité de notre application.
    echo $e->getMessage();
}