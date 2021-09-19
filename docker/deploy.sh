#!/bin/sh

# On se rend dans le dossier du projet
cd /app

# On crée les tables de la base de données
php bin/console doctrine:migration:migrate