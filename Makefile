# Makefile
setup:
	docker-compose up -d --build
	docker-compose exec backend composer install
	docker-compose exec backend bin/console doctrine:database:create --if-not-exists
	docker-compose exec backend bin/console doctrine:migrations:migrate --no-interaction
 