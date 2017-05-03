cache-clear:
	@test -f bin/console && bin/console cache:clear --no-warmup || rm -rf var/cache/*
.PHONY: cache-clear

cache-warmup: cache-clear
	@test -f bin/console && bin/console cache:warmup || echo "cannot warmup the cache (needs symfony/console)"
.PHONY: cache-warmup

serve:
	@echo -e "\033[32;49mServer listening on http://127.0.0.1:8000\033[39m"
	@echo "Quit the server with CTRL-C."
	@echo -e "Run \033[32mcomposer require symfony/web-server-bundle\033[39m for a better web server"
	php -S 127.0.0.1:8000 -t web
.PHONY: serve
