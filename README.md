ToDoList
========

* Symfony 3.4 framework
* CSS : Bootstrap 4

## Prérequis

* Php 7.3.1
* Mysql 5.7
- PHPUnit (Tests)
- BlackFire (Performance)

## Installation
Clone Project https://github.com/ChoiAbraham/TodoList
```
$ git clone https://github.com/ChoiAbraham/TodoList
$ cd TodoList
```
Install composer dependencies and press enter to skip credentials

```
$ composer install
```
Create a database sqlite
```
$ php bin/console doctrine:database:create
```
```
$ php bin/console doctrine:schema:update --force
```
Set data with fixtures
```
$ php bin/console doctrine:fixtures:load --group=data
```
NB : for a local server database, you can set your credentials are set in /app/config/parameters.yml

Login : 

username : admin
password : admin