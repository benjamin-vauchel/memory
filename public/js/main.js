// Variables de l'application
const splashSreen = document.querySelector('.splash-screen');
const gameScreen = document.querySelector('.game-screen');
const newGameButton = document.getElementById('new-game');
const gameTime = parseInt(document.getElementById('time').value) * 1000; // en millisecondes
let playerName = null;
let playerTime = 0;
let interval = null;

// Lancement d'une nouvelle partie lors du clic sur le bouton "Nouvelle partie"
newGameButton.addEventListener('click', event => {
    // On empeche la soumission du formulaire
    event.preventDefault();

    // On cache l'écran d'accueil et on affiche l'écran de jeu
    splashSreen.style.display = 'none';
    gameScreen.style.display = ' block';

    // On prend soin de sauvegarder également le nom indiqué par le joueur
    playerName = document.getElementById('name').value;

    // On déclenche le compte à rebour et on met à jour la barre de progression toutes les 10 ms
    const refreshInterval = 10; // en millisecondes
    const progressBar = document.querySelector('#progress-bar .progress-bar-inner');
    interval = setInterval(function () {
        // On met à jour le temps de jeu du joueur
        playerTime += refreshInterval;

        // On augmente la barre de progression
        progressBar.style.width = playerTime / gameTime * 100 + '%';

        // Si le temps est écoulé, le joueur a perdu
        if (playerTime >= gameTime) {
            // On stoppe le compte à rebour
            clearInterval(interval);

            // On affiche un message
            alert('Désolé ' + playerName + ', vous avez perdu :(');

            // On recharge le jeu
            window.location.reload()
        }
    }, refreshInterval);

});

// Pour faire fonctionner notre jeu de carte, nous allons
// intercepter les clic effectués sur chaque carte :
//
// Si une paire a été révélée, nous déclencherons une animation victorieuse
// et appliquerons une classe "card--flipped-right" aux 2 cartes
//
// Sinon, nous déclencherons une animation de défaite avant de retourner les 2 cartes face cachées
// et de leur appliquer une classe "card--flipped-wrong"
//
// Une fois toutes les paires trouvées, nous afficherons un message au joueur et
// sauvegarderons son score en base de données.
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('click', cardClicked);
})

function cardClicked(event) {
    // event.target permet de récupérer l'élément HTML cliqué, dans notre cas, une carte
    card = event.target;

    // On "retourne" la carte pour afficher le fruit
    // en appliquant simplement la classe card--flipped
    // @see .card--flipped dans _game_screen.scss
    card.className = 'card card--flipped';

    // On récupère l'ensemble des cartes retournées à vérifier
    // c'est à dire celles avec la classe "card--flipped" mais sans les classes "card--flipped-right" ou "card--flipped-wrong"
    const flippedCards = document.querySelectorAll('.card.card--flipped:not(.card--flipped-right):not(.card--flipped-wrong)');

    // Si 2 cartes sont retournées on vérifie qu'il s'agit des mêmes
    if (flippedCards.length === 2) {
        const firstCardNumber = flippedCards[0].getAttribute('data-card-number');
        const secondCardNumber = flippedCards[1].getAttribute('data-card-number');

        // Si le numéro de la premier carte retournée correspond au numéro
        // de la deuxieme carte retournée la paire est valide
        if (firstCardNumber === secondCardNumber) {
            flippedCards.forEach(cardToUpdate => {
                // On applique la classe "card--flipped-right" pour indiquer
                // que cette carte retournée a été vérifiée et déclencher une animation victorieuse
                cardToUpdate.className += ' card--flipped-right';

                // On désactive le clic sur cette carte, elle ne peut plus être retournée
                cardToUpdate.removeEventListener('click', cardClicked);
            });
        } else {
            // Si la paire n'est pas valide
            flippedCards.forEach(cardToUpdate => {
                // On applique la classe "card--flipped-wrong" pour indiquer
                // que cette carte retournée ne fait pas partie d'une paire valide
                // et déclencher une animation de défaite
                cardToUpdate.className += ' card--flipped-wrong';

                // Une fois l'animation terminée, on retire la class "card-flipped"
                // pour retourner face cachée la carte
                setTimeout(function () {
                    cardToUpdate.className = cardToUpdate.className.replaceAll(' card--flipped', ' ');
                }, 1000);
            });
        }
    }

    // Est ce que le jeu est terminé ?
    // Pour cela on compte s'il reste des cartes non retournées, c'est à dire sans
    // la classe "card--flipped-right"
    if (document.querySelectorAll('.card:not(.card--flipped-right)').length === 0) {
        // Si le jeu est terminé, on stoppe le compte à rebour
        clearInterval(interval);

        // On converti le temps écoulé du joueur en secondes
        const playerTimeInSeconds = Math.round(playerTime / 1000);

        // On affiche au joueur un message de félicitation
        alert('Bravo ' + playerName + ', vous avez terminé le jeu en ' + playerTimeInSeconds + 's !');

        // Et on envoie le nom du joueur ainsi que son temps à une API
        // pour les sauvegarder en base de données
        const url = '/score';
        const data = new FormData();
        data.append('name', playerName);
        data.append('time', playerTimeInSeconds);

        const options = {
            method: 'POST',
            body: data,
        }

        fetch(url, options)
            // Une fois, l'appel API effecuté on recharge la page
            .then(res => window.location.reload());
    }
}