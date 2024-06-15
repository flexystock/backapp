DOCKER_BE = docker-symfony-be

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -s ':#'

run: ## Start the containers
	docker network create docker-symfony-network || true
	docker compose up -d
	@echo "Fixing permissions for var/log"
	docker exec -it --user root docker-symfony-be bash -c "chown -R www-data:www-data /appdata/www/var/log && chmod -R 775 /appdata/www/var/log"

fix-permissions: ## Fix permissions for var/log
	docker exec -it --user root docker-symfony-be bash -c "chown -R www-data:www-data /appdata/www/var/cache /appdata/www/var/log && chmod -R 775 /appdata/www/var/cache /appdata/www/var/log && ls -l /appdata/www/var && ls -l /appdata/www/var/cache && ls -l /appdata/www/var/log"

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

be-logs: ## Tail the Symfony development log
	docker exec -it ${DOCKER_BE} tail -f var/log/dev.log

ssh-be: ## SSH into the backend container
	docker exec -it ${DOCKER_BE} bash

code-style: ## Fix code style according to Symfony rules
	docker exec -it ${DOCKER_BE} php-cs-fixer fix src --rules=@Symfony
