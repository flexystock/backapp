DOCKER_BE = docker-symfony-be
DOCKER_MESSENGER = docker-symfony-messenger
DATE = $(shell date +%Y-%m-%d)

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -s ':#'

run: ## Start the containers
	docker network create docker-symfony-network || true
	docker compose up -d
	@echo "Fixing permissions for var/log"
	docker exec -it --user root ${DOCKER_BE} bash -c "mkdir -p /appdata/www/var/log && chown -R www-data:www-data /appdata/www/var/log && chmod -R 775 /appdata/www/var/log"
	@echo "Installing PHP dependencies with Composer"
	docker exec -it ${DOCKER_BE} composer install --no-scripts --no-interaction --optimize-autoloader
	@CONTAINERS=$$(docker ps -a -q); \
		if [ -n "$$CONTAINERS" ]; then \
			docker start $$CONTAINERS; \
		else \
			echo "No stopped containers to start"; \
		fi

fix-permissions: ## Fix permissions for var/log
	docker exec -it --user root ${DOCKER_BE} bash -c "mkdir -p /appdata/www/var/cache /appdata/www/var/log && chown -R www-data:www-data /appdata/www/var/cache /appdata/www/var/log && chmod -R 775 /appdata/www/var/cache /appdata/www/var/log && ls -l /appdata/www/var && ls -l /appdata/www/var/cache && ls -l /appdata/www/var/log"

stop: ## Stop the containers
	docker compose stop

clean: stop ## Clean up containers, volumes, and networks
	docker compose down --remove-orphans
	docker volume prune -f
	docker network prune -f

restart: clean run ## Restart the containers cleanly

build: ## Rebuild all the containers
	docker compose build

prepare: composer-install ## Prepare environment by running necessary backend commands

composer-install: ## Install composer dependencies
	docker exec -it ${DOCKER_BE} composer install --no-scripts --no-interaction --optimize-autoloader

logs: ## Tail the Symfony development log
	docker exec -it ${DOCKER_BE} tail -200f /appdata/www/var/log/dev-$(DATE).log

ssh-be: ## SSH into the backend container
	docker exec -it ${DOCKER_BE} bash

ssh-messenger: ## SSH into the messenger container
	docker exec -it ${DOCKER_MESSENGER} bash

code-style: ## Fix code style according to Symfony rules
	docker exec ${DOCKER_BE} bash -c "PHP_CS_FIXER_IGNORE_ENV=1 php-cs-fixer fix src --rules=@Symfony"
