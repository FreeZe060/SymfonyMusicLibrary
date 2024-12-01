<!-- DB -->

php bin/console doctrine:fixtures:load

php bin/console doctrine:migrations:list

php bin/console doctrine:migrations:migrate

php bin/console make:migration

php bin/console doctrine:database:drop --force

php bin/console doctrine:database:create

<!-- symfony -->

symfony server:start

php bin/console doctrine:database:validate

php bin/console doctrine:database:create

composer install

php -m

<!-- encore / tailwind -->

npm run watch