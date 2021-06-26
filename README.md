# API каталога товаров

## Установка

### Шаг 1. Клонирование проекта

```
$ git clone ssh://git@github.com:e1sep0/catalog-api.git && cd catalog-api
```

### Шаг 2. Установка зависимостей

```
$ composer install
```

### Шаг 3. Настраиваем конфигурацию БД и авторизации по токену

```
$ cp .env .env.local
Прописываем DATABASE_URL и JWT_PASSPHRASE
$ php bin/console lexik:jwt:generate-keypair
```

### Шаг 4. Создаем схему БД

```
$ php bin/console doctrine:schema:create
$ php bin/console doctrine:migrations:migrate
```

### Шаг 5. Загружаем фикстуры (Дев окружение).

```
$ php bin/console doctrine:fixtures:load
```
Создастся пользователь Логин: `admin@admin.com`  Пароль: `password`


### Шаг 6. Запускаем сервер

```
$ php bin/console server:start
http://127.0.0.1:8000/api
```

## Пояснения:

Для первой версии апи использовал Api Platform + JWT Token авторизацию
Для получения токена необходимо ввести email и пароль по эндпоинту `POST /authentication_token`

Для использования токена нужно на странице документации нажать `Authorize` и ввести:
`Bearer {token}`

Апи платформа предоставляет отличную документацию по использованию различных эндпоинтов

При добавлении нового товара, можно создать сразу новую категорию для него или привязать к существующим:
```json
{
  "name": "Новый товар",
  "categories": [
    "/api/categories/1",
    "/api/categories/2"
  ],
  "price": 150
}
```
