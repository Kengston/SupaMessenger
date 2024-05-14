start:
	symfony serve --no-tls

build:
	npm run build

dc_build:
	docker-compose up

dc_start:
	docker-compose start

dc_stop:
	docker-compose stop

dc_up:
	docker-compose up -d --remove-orphans

dc_ps:
	docker-compose ps

dc_logs:
	docker-compose -f


