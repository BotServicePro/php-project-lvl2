install:
	composer install

validate:
	composer validate

make lint:
	composer run-script phpcs -- --standard=PSR12 src bin

console:
	composer exec --verbose psysh

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml