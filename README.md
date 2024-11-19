# Тестовое задание

## API
Реализовано REST API для записной книжки, содержащее следующие методы:
        
        1.1. GET /api/v1/notebook/
        1.2. POST /api/v1/notebook/
        1.3. GET /api/v1/notebook/<id>/
        1.4. POST /api/v1/notebook/<id>/
        1.5. DELETE /api/v1/notebook/<id>/

Поля для POST запискной книжки имеют вид: 
   
        1. ФИО (обязательное)
        2. Компания
        3. Телефон (обязательное)
        4. Email (обязательное)
        5. Дата рождения 
        6. Фото

Для обработки запросов был создан <a href="https://github.com/M1estere/laravel-notebooks/blob/main/app/Http/Controllers/NotebookController.php">NotebookController</a>, где в каждом методе обрабатываются разные случаи и в зависимости от ситуации возвращаются разные значения\
Была добавлена пагинация (на данный момент выводит по 10 записей на странице, для смены страницы нужно добавить аргумент **page** и указать нужную страницу),\
таким образом, вывод информации при **GET /api/v1/notebook/** выглядит следующим образом:

![image](https://github.com/user-attachments/assets/5b8be974-b3c5-4ea6-b054-005467047a06)


А сама *data* при этом выглядит так:

![image](https://github.com/user-attachments/assets/df5da4cb-8457-408c-98fb-0e7245ad5ce9)

## Swagger
Для документирования методов был использован Swagger, пример для метода POST:

![image](https://github.com/user-attachments/assets/3e981bcc-5063-453a-b439-b47212543b4e)


Также был настроен маршрут /swagger, который при обращении возвращает страницу *swagger.blade.php*\
(данные берутся из **<a href="https://github.com/M1estere/laravel-notebooks/blob/main/storage/api-docs/api-docs.json">storage/api-docs/api-docs.json</a>** через **<a href="https://github.com/M1estere/laravel-notebooks/blob/main/app/Http/Controllers/SwaggerController.php">SwaggerController</a>**, сам api-docs.json генерируется с помощью **php artisan swagger-lume:generate**),\
где можно ознакомиться с методами апи подробнее, посмотреть примеры использования, ответы и тд:

![image](https://github.com/user-attachments/assets/8f5aadae-b53f-480a-9f80-e30bf39cb655)

## Тестирование
Для тестирования апи в **<a href="https://github.com/M1estere/laravel-notebooks/blob/main/tests/Feature/NotebooksTest.php">tests/Feature/NotebooksTest</a>** были написаны различные тесты,\
охватывающие большинство возможных случаев при работе с апи (корректная работа, неправильные данные, отсутствие записей и тд)

Пример теста, проверяющего код на 409 при добавлении записи (создается дубликат):

![image](https://github.com/user-attachments/assets/031b5594-3cef-4275-ad46-e56af724d987)


Также для тестирования работы апи использовался Postman для отправки запросов, пример запроса:

![image](https://github.com/user-attachments/assets/08727fae-a361-417a-aec8-2b437434dd59)


## Docker
Для запуска приложения был написан **<a href="https://github.com/M1estere/laravel-notebooks/blob/main/Dockerfile">Dockerfile</a>** для создания образа для php приложения,\
установки нужных зависимостей и расширений, также он копирует файлы приложения и настраивает веб-сервер
Также был написан **<a href="https://github.com/M1estere/laravel-notebooks/blob/main/docker-compose.yml">docker-compose</a>** файл для поднятия приложения на **laravel** и базы данных **mysql**.\
В нем при запуске laravel контейнера выполняется **<a href="https://github.com/M1estere/laravel-notebooks/blob/main/entrypoint.sh">entrypoint.sh</a>**, который отвечает за выполнение миграций после старта контейнера с бд

![image](https://github.com/user-attachments/assets/e099be3d-2bb8-4f78-b7c4-e57e2fcf8f7b)



