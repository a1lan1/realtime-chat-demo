i:
	composer install
	yarn install

dbs:
	php artisan migrate:fresh --seed

dev:
	composer run dev

test:
	php artisan test

lint:
	./vendor/bin/pint
	./vendor/bin/phpstan analyse
	npx eslint .
	npx prettier --check .
