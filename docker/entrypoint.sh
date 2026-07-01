#!/bin/bash
set -e

# Carga .env montado en /var/www/html/.env (valores con espacios soportados)
ENV_FILE="/var/www/html/.env"
if [ -f "$ENV_FILE" ]; then
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
      if [ -n "$key" ] && [ -z "${!key:-}" ]; then
        export "${key}=${value}"
      fi
    fi
  done < "$ENV_FILE"
fi

exec docker-php-entrypoint "$@"
