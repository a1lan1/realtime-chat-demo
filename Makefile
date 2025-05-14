i:
	composer install
	yarn install

dbs:
	php artisan migrate:fresh --seed

dev:
	composer run dev

test:
	rm -rf coverage coverage.xml
	php artisan config:clear --env=testing
	php artisan test --coverage --parallel
	open coverage/index.html

lint:
	./vendor/bin/pint
	./vendor/bin/phpstan analyse
	npx eslint .
	npx prettier --check .
