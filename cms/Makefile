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
	$(CONSOLE) cache:clear --env=test
	$(CONSOLE) doctrine:schema:drop --env=test --force
	$(CONSOLE) doctrine:schema:create --env=test
	$(PHPUNIT) --log-junit $(TEST_LOG_PATH) -c app/
