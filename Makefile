.PHONY: build up down restart ps logs shell composer-install db-migrate test

# Build the containers
build:
	docker-compose build

# Start the containers
up:
	docker-compose up -d

# Stop the containers
down:
	docker-compose down

# Restart the containers
restart:
	docker-compose restart

# Show container status
ps:
	docker-compose ps

# View container logs
logs:
	docker-compose logs -f

# Enter the PHP container
shell:
	docker-compose exec app bash

# Run composer install inside the container
composer-install:
	docker-compose exec app composer install

# Run database migrations
db-migrate:
	docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# Run tests
test:
	docker-compose exec app php bin/phpunit
