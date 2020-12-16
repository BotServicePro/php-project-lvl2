install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 bin src tests

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 bin src tests

test:
	composer phpunit tests

test-coverage:
	composer phpunit tests -- --coverage-clover build/logs/clover.xml