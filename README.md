# Wish-app

Веб-приложение для управления вишлистами (списками желаний) с приглашениями по инвайт-кодам.

## Стек

- **Frontend:** React
- **Backend:** PHP 8, REST API
- **База данных:** SQLite
- **Инфраструктура:** Docker

## Возможности

- JWT-аутентификация
- Добавление пользователей в друзья по инвайт-кодам (закрытая система приглашений)
- Создание и управление вишлистами
- Возможность бронирования подарков
- REST API для взаимодействия frontend/backend
- Ролевой доступ (обычные пользователи / администраторы)

## Схема данных

Реляционная схема с внешними ключами между пользователями, вишлистами и инвайт-кодами:
```
users (id, email, password_hash, role, created_at)
invites (id, code, created_by, used_by, created_at)
wishlists (id, user_id, title, created_at)
wishes (id, wishlist_id, title, description, url, created_at)
```

## Структура проекта
````
Wish-app/
├── src/
│   ├── client/              # Frontend на React
│   │   ├── components/
│   │   ├── pages/
│   │   └── ...
│   └── server/               # Backend на PHP (REST API)
│       ├── controllers/
│       ├── models/
│       ├── routes/
│       └── ...
├── .gitignore
├── LICENSE
└── README.md
````

## Запуск локально

### Требования

- Docker и Docker Compose
- Git

### 1. Клонировать репозиторий

```bash
git clone https://github.com/ByakuByaku/Wish-app.git
cd Wish-app
```

### 2. Собрать и запустить контейнер

```bash
docker compose -f src/docker-compose.yml up --build
```

Поднимется PHP + Apache контейнер, обслуживающий backend из `server/` (через `APACHE_DOCUMENT_ROOT=/var/www/html/server`).

### 3. Установить PHP-зависимости через Composer

```bash
docker compose -f src/docker-compose.yml exec php composer install
```

### 4. Проверить, что всё работает

Backend доступен по адресу: `http://localhost:8080`

### 5. Frontend (React)

Frontend разрабатывается отдельно от Docker-окружения:

```bash
cd src/client
npm install
npm run dev
```

### 6. Остановить контейнер

```bash
docker compose -f src/docker-compose.yml down
```

### Полезные команды Composer

```bash
# установить зависимости
docker compose -f src/docker-compose.yml exec php composer install

# добавить пакет
docker compose -f src/docker-compose.yml exec php composer require пакет/имя

# обновить зависимости
docker compose -f src/docker-compose.yml exec php composer update
```
