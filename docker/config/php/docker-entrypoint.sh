#!/bin/sh
set -e

# Crée le dossier de cache (et ses parents) s'il n'existe pas
mkdir -p /app/var/cache/dev

# Change le propriétaire de /app/var (donc de tous ses sous-dossiers) à www-data
chown -R www-data:www-data /app/var

exec "$@"
