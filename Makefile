SYMFONY_ROOT=cms

.PHONY: all install update test server

all: update install

install update server test :
	make -C $(SYMFONY_ROOT) $@
