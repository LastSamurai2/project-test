.PHONY: build
build:
	git submodule init
	git submodule update --remote
	cat laradock-fork/env-example | sed -e 's#PHP_VERSION=7.3#PHP_VERSION=7.4#g' -e 's#~/.laradock/data#~/.laradock/adminapp-data#g' > laradock-fork/.env;
	cd laradock-fork && docker-compose build nginx mysql mailhog workspace php-fpm

.PHONY: pull
pull:
	cd laradock-fork && docker-compose pull nginx mysql mailhog workspace

.PHONY: up
up:
	$(MAKE) pull
	cd laradock-fork && docker-compose up -d --remove-orphan nginx mysql mailhog workspace
	cd laradock-fork && docker-compose run -u laradock workspace composer install
	cd laradock-fork && docker-compose run -u laradock workspace php artisan migrate
	cd laradock-fork && docker-compose run -u laradock workspace php artisan storage:link
	cd laradock-fork && docker-compose run -u laradock workspace npm install
	cd laradock-fork && docker-compose run -u laradock workspace npm run dev


.PHONY: down
down:
	cd laradock-fork && docker-compose down -v
