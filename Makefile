#!/bin/bash

DOCKER_BE = docker-symfony-be
UID = $(shell id -u)

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -s ':#'

run: ## Start the containers
	docker network create docker-symfony-network || true
	U_ID=${UID} docker-compose up -d

stop: ## Stop the containers
	U_ID=${UID} docker-compose stop

clean: stop ## Clean up containers, volumes, and networks
	docker-compose down --remove-orphans
	docker volume prune -f
	docker network prune -f

restart: clean run ## Restart the containers cleanly

build: ## Rebuild all the containers
	U_ID=${UID} docker-compose build

prepare: composer-install ## Prepare environment by running necessary backend commands

composer-install: ## Install composer dependencies
	U_ID=${UID} docker exec --user ${UID} -it ${DOCKER_BE} composer install --no-scripts --no-interaction --optimize-autoloader

be-logs: ## Tail the Symfony development log
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} tail -f var/log/dev.log

ssh-be: ## SSH into the backend container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash

code-style: ## Fix code style according to Symfony rules
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} php-cs-fixer fix src --rules=@Symfony
