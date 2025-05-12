i:
	composer install
	yarn install

dbs:
	php artisan migrate:fresh --seed

dev:
	composer run dev

test:
	rm -rf coverage coverage.xml
	php artisan test --coverage
	open coverage/index.html

lint:
	./vendor/bin/pint
	./vendor/bin/phpstan analyse
	npx eslint .
	npx prettier --check .
