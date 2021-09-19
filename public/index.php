<?php
require_once dirname(__DIR__).'/src/Score.php';
require_once dirname(__DIR__).'/src/ScoreRepository.php';
require_once dirname(__DIR__).'/src/Game.php';
?><!doctype html>
<html lang="fr">
<head>
    <!--
    On spécifie comment sont encodés nos caractères, c'est un vaste sujet.
    Pour l'instant il faut simplement garder à l'esprit que cet encodage doit
    correspondre à celui de votre base de données et que l'UTF-8 est l'encodage le plus répandu
    -->
    <meta charset="UTF-8">

    <!--
    On spéficie comment les périphériques mobile doivent dimensionner leur viewport.
    C'est à dire dans quelles dimensions ils doivent afficher la page.
    Là encore, il s'agit du réglage recommandé.
    -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- On spécifie simplement le titre de la page,-->
    <title>Memory</title>

    <!-- On rend notre jeu plus agréable à l'oeil grâce à quelques styles CSS -->
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<h1>Memory Game</h1>

<!--
Notre jeu se compose de 2 écrans, 1 seul pouvant être affiché à la fois :

    - le "splash screen" : l'écran d'accueil affichant le tableau de score,
    le bouton pour lancer une nouvelle partie et les règles du jeu

    - le "game screen" : l'écran affichant le timer et les cartes du jeu.
-->

<!-- Ecran "splash screen" -->
<div class="splash-screen">
    <!-- Tableau des scores -->
    <table class="score-board">
        <thead>
        <tr>
            <th>Rang</th>
            <th>Nom</th>
            <th>Temps</th>
        </tr>
        </thead>
        <?php
        // Récupérons les 5 meilleurs scores à partir de notre base de données et affichons les
        $scoreRepository = new ScoreRepository(new PDO('sqlite:' . dirname(__DIR__) . '/database.sqlite'));
        $scores = $scoreRepository->findBestScores(5);
        foreach ($scores as $i => $score) {
            ?>
            <tr>
                <td>#<?= $i + 1 ?></td>
                <td><?= $score->getName() ?></td>
                <td><?= $score->getTime() ?>s</td>
            </tr>
            <?php
        }
        ?>
    </table>

    <!-- Nouvelle partie -->
    <div class="form">
        <input type="text" id="name" class="input" placeholder="VOTRE NOM" maxlength="3" autofocus>
        <button class="button" id="new-game">Nouvelle partie</button>
    </div>

    <!-- Règles du jeu -->
    <p class="rules">Vous avez 2 minutes pour reconstituer les 18 paires ! Good Luck !</p>
</div>

<!-- Ecran "game screen" -->
<div class="game-screen">
    <!-- Timer -->
    <div class="timer">
        <div class="progress-bar" id="progress-bar">
            <div class="progress-bar-inner"></div>
        </div>
    </div>

    <!-- Jeu -->
    <div class="memory-game">
        <?php
        // Créeons un nouveau jeu de 18 paires
        $game = new Game(2);

        // Affichons les cartes
        foreach ($game->getCards() as $card) {
            echo '<div class="card" data-card="' . $card . '"></div >';
        }
        ?>
    </div>
</div>

<!-- Rendons notre jeu intéractif grâce à un peu de Javascript -->
<script src="js/main.js"></script>
</body>
</html>