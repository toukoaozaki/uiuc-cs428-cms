COMPOSER=composer.phar
PHPUNIT=phpunit
CONSOLE=app/console
TEST_LOG_PATH=build/test-reports/phpunit.xml

.PHONY: all install update test server

all: update install

install:
	$(COMPOSER) install

update:
	$(COMPOSER) update

server:
	$(CONSOLE) server:run

test:
	$(CONSOLE) doctrine:schema:update --env=test --force
	$(PHPUNIT) --log-junit $(TEST_LOG_PATH) -c app/
