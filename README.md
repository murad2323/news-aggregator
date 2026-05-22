# Новостной агрегатор

Тестовое задание: сбор новостей из RSS, хранение в MySQL, отображение с фильтрацией и кеширование через Memcached.

## Стек
- PHP 8.4 (native, без фреймворков, автозагрузка через Composer PSR-4)
- MySQL 8.4
- Memcached
- Docker (Apache + PHP, MySQL, Memcached)

## Структура проекта
- `src/App/Database.php` – подключение к MySQL
- `src/App/Cache.php` –  подключение Memcached
- `src/App/NewsRepository.php` – получение новостей и категорий с кешированием
- `src/App/RssParser.php` – парсинг RSS и сохранение в БД
- `src/index.php` – веб-интерфейс с фильтрацией
- `src/parser.php` – консольный скрипт парсинга
- `schema.sql` – структура БД (автоматически применяется при первом запуске)
- `composer.json` – автозагрузка PSR-4
- `Dockerfile`, `docker-compose.yml` – Docker-окружение

## Быстрый старт

1. Убедитесь, что установлены Docker и Docker Compose.

2. Клонируйте репозиторий:
   git clone <URL>
   cd news-aggregator
   
3. Запустите сервисы:
   docker compose up -d --build
   
4. Выполните парсинг RSS:
   docker compose exec app php /var/www/html/parser.php
   
5. Откройте в браузере http://localhost:8080

Фильтрация
   Выбор категории из выпадающего списка
   Диапазон дат (с - по)
   Кнопка «Сбросить» для очистки фильтров

Повторный парсинг
   docker compose exec app php /var/www/html/parser.php
