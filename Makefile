SYMFONY_ROOT=cms
COMPOSER=composer.phar
PHPUNIT=phpunit
TEST_LOG_PATH=build/test-reports/phpunit.xml

.PHONY: all install update test server

all: update install

install:
	cd $(SYMFONY_ROOT); $(COMPOSER) install

update:
	cd $(SYMFONY_ROOT); $(COMPOSER) update

server:
	cd $(SYMFONY_ROOT); app/console server:run

test:
	$(PHPUNIT) --log-junit $(TEST_LOG_PATH) -c cms/app/
