.PHONY: build up down parse shell-db shell-cache stats

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

parse:
	docker compose exec app php /var/www/html/parser.php

shell-db:
	docker compose exec db mysql -u news_user -p news_aggregator

shell-cache:
	docker compose exec cache telnet localhost 11211

stats:
	docker compose exec cache bash -c "echo stats | nc localhost 11211"
