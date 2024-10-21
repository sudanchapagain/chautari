#!/bin/bash

export $(grep -v '^#' .env | xargs)

DB_NAME=${DB_NAME}
DB_USER=${DB_USER}

if [ "$#" -eq 0 ]; then
    echo "Usage: $0 table1 table2 table3 ..."
    exit 1
fi

SQL_COMMAND=""
for TABLE in "$@"; do
    SQL_COMMAND+="TRUNCATE TABLE $TABLE CASCADE; "
done

docker exec -i <postgres_container_name> psql -U "$DB_USER" -d "$DB_NAME" -c "$SQL_COMMAND"

echo "Cleared data from tables: $*"
