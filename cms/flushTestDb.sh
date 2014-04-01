app/console doctrine:schema:update --env dev_sqlite --force
app/console doctrine:fixtures:load --env dev_sqlite
