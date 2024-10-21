#!/bin/bash

export $(grep -v '^#' .env | xargs)

DB_NAME=${DB_NAME}
DB_USER=${DB_USER}

docker exec -i <postgres_container_name> psql -U "$DB_USER" "$DB_NAME" -f ../config/sql/seed.sql

echo "Executed seed.sql into the database."
