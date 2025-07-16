#!/bin/bash

# Script de nettoyage pour les tests PHPUnit

echo "Nettoyage des fichiers de cache PHPUnit..."

# Supprimer les fichiers de cache PHPUnit
if [ -d ".phpunit.cache" ]; then
    rm -rf .phpunit.cache
    echo "Cache PHPUnit supprimé"
fi

# Supprimer les fichiers de couverture
if [ -f "coverage.xml" ]; then
    rm coverage.xml
    echo "Fichier coverage.xml supprimé"
fi

if [ -d "coverage" ]; then
    rm -rf coverage
    echo "Dossier coverage supprimé"
fi

# Nettoyer les sessions temporaires
if [ -d "/tmp/sessions" ]; then
    rm -rf /tmp/sessions/*
    echo "Sessions temporaires nettoyées"
fi

# Créer les dossiers nécessaires
mkdir -p tests

echo "Nettoyage terminé!"
echo "Vous pouvez maintenant exécuter les tests avec : ./vendor/bin/phpunit"