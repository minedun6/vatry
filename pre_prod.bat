php app/console doctrine:database:drop --force --env=%1
php app/console doctrine:database:create --env=%1
php app/console doctrine:schema:update --force --env=%1
php app/console doctrine:fixtures:load --append --env=%1