# OrganizeLife

Projet en cours de développement

[Voir le site en ligne - version démo](https://organizelife.servange.fr)

OrganizeLife est une application web conçue pour simplifier la gestion du quotidien, aussi bien dans la sphère familiale que professionnelle. Elle propose une interface conviviale pour planifier des activités, organiser des événements, et mieux coordonner les tâches de chacun.

Pensée en mobile first, l'application vise à offrir une expérience fluide sur tous les supports.

Remarque : Le site en ligne est une version démo, déployée dans le but de présenter mes compétences techniques.
Lors du déploiement, le build des assets SCSS, ainsi que le chargement de certaines images et icônes, n'a pas fonctionné comme prévu. Ce point est en cours de correction.
Le déploiement s’effectuant actuellement via SFTP, les mises à jour prennent un peu plus de temps, mais le projet reste actif et évolutif.

## Fonctionnalités actuelles
Création compte utilisateurs
Création et affichage d’événements
Gestion des tâches quotidiennes
Interface responsive
Système de navigation fluide


## Évolutions prévues
Authentification via Google (OAuth)
Refonte du design pour une meilleure expérience utilisateur
Optimisation du code et refactorisation continue
Ajout de tests automatisés

## Installation


``` bash
git clone https://github.com/lana-12/organizeLife

composer install
npm install

# Lancer le serveur Symfony
symfony serve:start
# ou
php -S localhost:8000 -t public

# Arrêter le serveur
symfony serve:stop

# Build des assets
npm run build
npm run watch

# Nettoyage du dossier de build
rm -rf public/build/*

```