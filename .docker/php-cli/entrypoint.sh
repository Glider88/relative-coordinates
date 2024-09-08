#!/bin/sh

su - "$USER" -c "cd /usr/src/relative; composer install"

tail -f /dev/null
