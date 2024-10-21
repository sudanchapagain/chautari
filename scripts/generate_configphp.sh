#!/bin/bash

export $(grep -v '^#' .env | xargs)

CONFIG_FILE="./config/config.php"

echo "<?php" > "$CONFIG_FILE"

echo "
// Generated config.php
return [
    'DB_HOST' => '${DB_HOST}',
    'DB_USER' => '${DB_USER}',
    'DB_PASS' => '${DB_PASS}',
    'DB_NAME' => '${DB_NAME}',
    'DB_PORT' => '${DB_PORT}',
];
" >> "$CONFIG_FILE"

echo "config.php generated successfully in $CONFIG_FILE."
