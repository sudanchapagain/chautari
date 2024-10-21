#!/bin/bash

export $(grep -v '^#' .env | xargs)

BACKUP_DIR=${BACKUP_DIR}
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
BACKUP_FILE="$BACKUP_DIR/backup_$(date +'%Y%m%d_%H%M%S').sql"

mkdir -p "$BACKUP_DIR"

docker exec -t <postgres_container_name> pg_dump -U "$DB_USER" "$DB_NAME" > "$BACKUP_FILE"

echo "Backup created at $BACKUP_FILE"

