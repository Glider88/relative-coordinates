#!/bin/sh

su - "$USER" -c "composer install"

tail -f /dev/null
