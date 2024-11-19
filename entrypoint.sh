#!/bin/bash

until mysql -h mysql_db -u root -p -e "SELECT 1"; do
  echo "Waiting MySQL..."
  sleep 2
done

php artisan migrate --force

php -S 0.0.0.0:8000 -t public
