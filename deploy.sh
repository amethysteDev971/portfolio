#!/bin/bash
set -e

# Chemin vers votre projet
PROJECT_DIR="/var/www/portfolio"

echo "Modification de la propriété et des permissions..."
sudo chown -R www-data:www-data "$PROJECT_DIR/var/cache" "$PROJECT_DIR/public/uploads"
sudo chmod -R 775 "$PROJECT_DIR/var/cache" "$PROJECT_DIR/public/uploads"

echo "Vider et réchauffer le cache en prod..."
sudo -u www-data php "$PROJECT_DIR/bin/console" cache:clear --env=prod
sudo -u www-data php "$PROJECT_DIR/bin/console" cache:warmup --env=prod

echo "Redémarrage des services..."
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx

echo "Déploiement terminé."
