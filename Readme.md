##Описание

Простейшая система файлов. 
Создание удаление. 
Файлы хранятся на сервере, описание в базе.
Каждый пользователь имеет свою базу файлов.
Пользователь должень быть авторизировани при помощи laravel Auth().

## Install

Используя Composer

``` bash
$ composer require madlux/filesystem
```

Публикация vendor

``` bash
$ php artisan vendor:publish
```

ДБ

``` bash
$ php artisan migrate
```