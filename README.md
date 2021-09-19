Exemple de jeu de Memory
===

A travers cet exemple de jeu de Memory, nous allons aborder plusieurs notions :

- La création d'une application web avec Symfony
- L'utilisation d'un préprocesseur CSS (SASS) pour un code maintenable
- Les animations CSS
- L'utilisation de Javascript pour rendre notre application interactive
- L'écriture de tests pour une meilleure qualité et maintenabilité
- La création d'environnements de développement, de test et de production avec Docker

Si vous souhaitez modifier le jeu, vous pouvez lancer le projet en mode "développement". Vous aurez alors accès à des
logs plus détaillés et à la debug toolbar de Symfony.

Lancer le projet en mode "développement" avec Docker
---

Il s'agit de la solution la plus simple, il vous suffit d'avoir Docker et Docker Compose installés sur votre machine :

- [Installer Docker](https://docs.docker.com/engine/install/)
- [Installer Docker Compose](https://docs.docker.com/compose/install/)

Ensuite on lance les services Docker (PHP, Nginx et MySQL) :

    docker-compose up -d

On installe les dépendances composer:

    docker-compose exec php composer install

On crée la base de données :

    docker-compose exec php ./bin/console doctrine:migrations:migrate

Et on a accès à notre jeu :

    http://localhost:8000/

Lancer le projet en mode "développement" sans Docker
---

Si vous avez déjà un environnement de développement configuré pour Symfony avec PHP 8, MySQL 8 et un serveur web, vous
pouvez simplement modifier la variable `DATABASE_URL` dans le fichier `.env` et faire pointer votre serveur web vers le
dossier `public`.

Vous pouvez ensuite installer les dépendances composer:

    php composer install

Et créer la base de données :

    php ./bin/console doctrine:migrations:migrate

Configuration du jeu
---

Le jeu peut être configuré via les paramètres du fichier `config/services.yaml` :

    parameters:
        scores: 5 # nombre de score à afficher en page d'accueil
        pairs: 18 # nombre de paires consitutant nore jeu
        time: 120 # temps de la partie (en secondes)

Lancer les tests
---

On crée les containers Docker de test (en indiquant le fichier de configuration `docker-compose-test.yml`) :

    docker-compose -f docker-compose-test.yml up -d

On installe les dépendances composer:

    docker-compose -f docker-compose-test.yml exec php_test composer install

On crée la base de données de test :

    docker-compose -f docker-compose-test.yml exec php_test bin/console doctrine:schema:create --env=test

On lance les tests :

    docker-compose -f docker-compose-test.yml exec php_test ./bin/phpunit

Déployer le jeu en production
---

Pour cela, il nous faut un serveur de production avec Docker ainsi qu'un accès SSH à ce serveur.

Ensuite, nous allons nous servir de Docker Compose pour créer une version de production de notre application, c'est à
dire une version avec seulement les élements nécessaires à la production.

Par exemple, nous allons retirer les tests, la base de données servant aux tests, les paquets composer servant au
développement et nous allons configurer Symfony pour qu'il s'execute en mode production (`APP_ENV=prod`) et sans
informations de debug (`APP_DEBUG=0`).

Ensuite il suffit de créer un contexte Docker de production en indiquant l'adresse de notre serveur :

    docker context create prod ‐‐docker "host=ssh://user@remotemachine"

Puis basculer vers le contexte Docker de production :

    docker context use prod

Et enfin lancer les containers Docker en spécifiant la configuration de production (`docker-compose-prod.yml`) :

    docker-compose ‐‐context remote -f docker-compose-prod.yml up -d
