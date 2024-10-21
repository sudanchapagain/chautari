#!/bin/bash

export $(grep -v '^#' .env | xargs)

CONTAINERS=("nginx" "web" "db")

LOG_DIR="./logs"
mkdir -p "$LOG_DIR"

for CONTAINER in "${CONTAINERS[@]}"; do
    echo "Collecting logs from container: $CONTAINER"
    docker logs "$CONTAINER" &> "$LOG_DIR/${CONTAINER}_logs_$(date +'%Y%m%d_%H%M%S').log"
done

echo "Logs collected in $LOG_DIR."
