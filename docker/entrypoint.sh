#!/bin/bash
set -e

# Carga .env fuera del DocumentRoot. El montaje antiguo en /var/www/html/.env sigue como respaldo.
ENV_FILE=""
for candidate in /var/www/secrets/.env /var/www/html/.env; do
  if [ -f "$candidate" ]; then
    ENV_FILE="$candidate"
    break
  fi
done
if [ -n "$ENV_FILE" ]; then
  while IFS= read -r line || [ -n "$line" ]; do
    line="${line%$'\r'}"
    case "$line" in
      ''|\#*) continue ;;
    esac
    if [[ "$line" == *=* ]]; then
      key="${line%%=*}"
      value="${line#*=}"
      key="$(echo -n "$key" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"
      value="$(echo -n "$value" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"
      value="${value%\"}"
      value="${value#\"}"
      value="${value%\'}"
      value="${value#\'}"
      if [ -n "$key" ]; then
        export "${key}=${value}"
      fi
    fi
  done < "$ENV_FILE"
fi

exec docker-php-entrypoint "$@"
