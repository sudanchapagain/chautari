#!/bin/sh

COMMAND=$1
shift

case "$COMMAND" in
  format)
    TARGET_PATH=${1:-.}
    podman compose run --rm web vendor/bin/php-cs-fixer fix "$TARGET_PATH" --config=.php-cs-fixer.dist.php "$@"
    ;;
  analyse)
    podman compose run --rm web vendor/bin/phpstan analyse "$@"
    ;;
  seed)
    export $(grep -v '^#' .env | xargs)
    DB_NAME=${DB_NAME}
    DB_USER=${DB_USER}
    podman exec -i chautari-db-1 psql -U "$DB_USER" "$DB_NAME" -f ../.docker/conf/postgres/seed/seed.sql
    echo "Executed seed.sql into the database."
    ;;
  *)
    echo "Usage: $0 {format|analyse} [args]"
    exit 1
    ;;
esac

