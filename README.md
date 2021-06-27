# API каталога товаров

## Установка

### Шаг 1. Клонирование проекта

```
$ git clone git@github.com:e1sep0/catalog-api.git && cd catalog-api
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
$ php bin/console doctrine:database:create --if-not-exists
$ php bin/console doctrine:migrations:migrate

Для тестов
$ php bin/console doctrine:database:create --env=test
$ php bin/console doctrine:migrations:migrate --env=test
```

### Шаг 5. Загружаем фикстуры (Дев окружение).

```
$ php bin/console doctrine:fixtures:load
$ php bin/console doctrine:fixtures:load --env=test
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

Или привязать к текущей и создать новую:
```json
{
  "name": "Новый товар",
  "categories": [
    "/api/categories/1",
    {
      "name": "Новая категория"
    }
  ],
  "price": 350
}
```

# API v.2

Была создана для демонстрации возможностей без Апи платформы

## Роуты для запросов:
`/authentication_token` - Для получения токена остается тот же метод

Данные для запроса:
```json
{
  "email": "admin@admin.com",
  "password": "password"
}
```

Ответ:
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2MjQ4MDAyMzEsImV4cCI6MTYyNDgwMzgzMSwicm9sZXMiOlsiUk9MRV9BUEkiLCJST0xFX1VTRVIiXSwidXNlcm5hbWUiOiJhZG1pbkBhZG1pbi5jb20ifQ.Z0QNIcAGLW39uecK29cXr4w_G9XGVVDtQhqWMQFYXL8ZP1Ls-RoVqReLPmtbwbyRZKITK3llSLNoHLxyRBK1shBVftHVzU5bYZ_SgGVjrx_hWMcYyJToPAb92GZyndTx5n20IMbHd9DSXZ3L8vcykPkF8F9fZd2-ihXCPViRGyT0OugCRs5Q8QdegVGxDnzUJDrlLhefKisURjYzBRKcMRgZ4-9oHo0JtPih17Vj4YgleSU1L6bnoJCqWontYhZ28b059sbRRqXEo_xFM8VjROc0or9ymtZHMeAgZv7jKHbHy0NMj-VprZY-5-qW6j3jqj-KuyaOp1k74SlF97cG8A"
}
```

Для запросов, требующих авторизацию, необходимо в заголовки добавлять ключ
`Authorization` со значением `Bearer {token}`

`GET /api/v2/categories` - Список категорий

`GET /api/v2/categories/{categoryId}/goods` - Список товаров категории

`POST /api/v2/categories` - Создание категории

Запрос:
```json
{
  "name": "Новая категория"
}
```

`POST /api/v2/categories/{categoryId}` - Изменение категории

Запрос:
```json
{
  "name": "Измененная категория"
}
```

`DELETE /api/v2/categories/{categoryId}` - Удаление категории

`POST /api/v2/goods` - Создание товара

Запрос:
```json
{
    "name": "Test",
    "price": 100,
    "categories": [
        1,2
    ]
}
```

Ответ:
```json
{
    "id": 1,
    "name": "Test",
    "categories": [
        {
            "id": 1,
            "name": "123"
        },
        {
            "id": 2,
            "name": "Новая категория"
        }
    ],
    "price": 100
}
```

`POST /api/v2/goods/{goodId}` - Изменение товара

Запрос:
```json
{
    "name": "Test",
    "price": 200,
    "categories": [
        2
    ]
}
```

Ответ:
```json
{
    "id": 1,
    "name": "Test",
    "categories": [
        {
            "id": 2,
            "name": "Новая категория"
        }
    ],
    "price": 200
}
```

`DELETE /api/v2/goods/{goodId}` - Удаление товара


## Тесты
Для выполнения тестов:
```
$ php bin/phpunit
```

## Время
* Разворот проекта с зависимостями: 1 час
* Авторизация с JWT токеном: 1 час
* Добавление сущностей: 15 минут
* Настройка связей, эндпоинтов и групп: 1 час
* Написание документации: 30 минут
* Тесты: 2 часа

### 2ая версия АПИ
* Контроллеры: 2 часа
* Тесты: 2 часа
