.DEFAULT_GOAL := help

help: ## This help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z0-9_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

start: ## Start application
	@cp -n .env.example .env
	@docker-compose up -d
	@docker-compose exec php composer install
	@docker-compose exec php bin/console lexik:jwt:generate-keypair --skip-if-exists

stop: ## Stop application
	@docker-compose down

migrate: ## Run migrations
	@docker-compose exec php bin/console doctrine:migrations:migrate

seed: ## Run seeds
	@docker-compose exec php bin/console hautelook:fixtures:load
