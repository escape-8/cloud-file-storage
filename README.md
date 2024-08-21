# cloud-file-storage

## Проект "Облачное хранилище"

Многопользовательское файловое облако. Пользователи сервиса могут использовать его для загрузки и хранения файлов. Источником вдохновения для проекта является Google Drive.

## Технологии
- PHP 8.1
- MariaDB
- Laravel framework
- Docker / Docker Compose
- NoSQL storage (s3, Redis)
- JQuery


## Мотивация проекта
- Реализация одностраничного приложения.
- Работа с хранилищами NoSQL.
- Получение практического опыта работы с Docker / Docker Compose.
- Погружение в jQuery/JavaScript.

## Функционал приложения

##### Работа с пользователями:
- Регистрация
- Авторизация
- Выход

##### Работа с файлами и папками:
- Загрузка файлов и папок
- Создание новой пустой папки (аналогично созданию новой папки в Проводнике)
- Удаление
- Переименование

## Настройка
### Локальная, для разработки

```
git clone https://github.com/escape-8/cloud-file-storage.git
```
```
cp .env.example .env
```

Открыть .env и заполнить следующие поля
```
APP_NAME="Cloud File Storage"
APP_URL=http://localhost:9993/ 
```
Добавить в .env SANCTUM_STATEFUL_DOMAINS и написать имя вашего домена, это нужно для локальной работы фронтенда.
```
SANCTUM_STATEFUL_DOMAINS=localhost:9993
```
```
DB_CONNECTION=mysql
DB_HOST=db-dev
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_user
DB_PASSWORD=your_password
```
```
SESSION_DRIVER=redis
REDIS_HOST=redis-session
REDIS_PASSWORD=null
REDIS_PORT=6379
```
В этом проекте как было уже описано используется s3, конкретно альтернативное S3-совместимое хранилище Minio.
```
AWS_ACCESS_KEY_ID="ваш_логин"
AWS_SECRET_ACCESS_KEY="ваш_пароль"
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET="ваше_имя_бакета"
AWS_URL=http://localhost:9991/local (опционально, дает доступ к клиенту и возможность зайти в minio через бразуер по логину и паролю, если не нужно просто нужно стереть значение переменной)
AWS_ENDPOINT=http://s3:9992/
AWS_USE_PATH_STYLE_ENDPOINT=true
```


### Запуск приложения
```
docker compose up -d --build
```
```
docker compose run composer install
```
```
docker compose run artisan migrate
```
```
docker compose run artisan key:generate
```
```
docker compose run npm install && npm run build
```

Проверить в браузере localhost:9993
