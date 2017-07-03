cache-clear:
	@test -f bin/console && bin/console cache:clear --no-warmup || rm -rf var/cache/*
.PHONY: cache-clear

cache-warmup: cache-clear
	@test -f bin/console && bin/console cache:warmup || echo "cannot warmup the cache (needs symfony/console)"
.PHONY: cache-warmup

CONSOLE=bin/console
sf_console:
	@test -f $(CONSOLE) || printf "Run \033[32mcomposer require cli\033[39m to install the Symfony console.\n"
	@exit

serve_as_sf: sf_console
	@test -f $(CONSOLE) && $(CONSOLE)|grep server:start > /dev/null || ${MAKE} serve_as_php
	@$(CONSOLE) server:start || exit 1

	@printf "Quit the server with \033[32;49mbin/console server:stop.\033[39m\n"

serve_as_php:
	@printf "\033[32;49mServer listening on http://127.0.0.1:8000\033[39m\n";
	@printf "Quit the server with CTRL-C.\n"
	@printf "Run \033[32mcomposer require symfony/web-server-bundle\033[39m for a better web server\n"
	php -S 127.0.0.1:8000 -t public

serve:
	@${MAKE} serve_as_sf
.PHONY: sf_console serve serve_as_sf serve_as_php
