php bin/console about
php bin/console debug:container --env-vars
php bin/console doctrine:migrations:migrate
make shell
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console doctrine:schema:validate
php bin/console debug:router
php bin/console about
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:migrate
docker compose exec database psql -U symfony -d symfony
psql -U symfony
grep -r "NewsImage" src/Entity/
grep -r "news_image" src/
php bin/console doctrine:schema:drop --force --full-database
php bin/console cache:clear
rm -rf migrations/*.php
php bin/console doctrine:migrations:generate
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
php bin/console doctrine:schema:validate
php bin/console doctrine:fixtures:load
make up
symfony serve
ls
exit
ls -la public/index.php
cat public/index.php
exit
 ./bin/phpunit tests/
exit;
