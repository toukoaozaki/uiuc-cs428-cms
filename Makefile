SYMFONY_ROOT=cms
COMPOSER=composer.phar
TEST_LOG_PATH=build/test-reports/phpunit.xml

.PHONY: all

all: update install

install: update
	cd $(SYMFONY_ROOT); $(COMPOSER) install

update:
	cd $(SYMFONY_ROOT); $(COMPOSER) update

server:
	cd $(SYMFONY_ROOT); app/console server:run

test:
	phpunit --log-junit $(TEST_LOG_PATH) -c cms/app/
