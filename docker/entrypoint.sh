#!/bin/sh
set -e

# O Render define a variável PORT dinamicamente (geralmente 10000).
# Localmente (docker-compose), usamos 8080 como padrão.
PORT="${PORT:-8080}"

# Ajusta o Apache para escutar na porta correta
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

exec "$@"
