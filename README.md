# Amocrm интеграция

## Установка
- Скачать репозиторий
- Создать файл .env и скопировать в него содержимое .env.example
- Установить значение API_KEY в .env
- docker-compose up -d
- docker exec -it {container name} bash

Уже внутри docker контейнера:
- composer install
- php artisan key:generate
- php artisan migrate
- php artisan schedule:work > /dev/null 2>&1 &

## Дополнительно
По умолчанию запуск задачи происходит каждый два часа.
Получение данных и сохранение их в БД реализовано в app/Jobs/WbJob.php
