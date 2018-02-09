#!/bin/bash
echo "borrando cache..."
app/console cache:clear --env=dev
app/console assets:install web --symlink
rm -Rf app/cache/* app/logs/*