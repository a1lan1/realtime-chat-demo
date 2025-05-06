lint:
	./vendor/bin/pint
	./vendor/bin/phpstan analyse
	npx eslint .
	npx prettier --check .
