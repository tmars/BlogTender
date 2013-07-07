#BlogTender

Конкурс блогов

##Установка

1. Клонирование и настройка репозитория.
```
    git clone https://github.com/tmars/BlogTender.git
    cd BlogTender
    git config core.filemode false
    git submodule init
    git submodule update
    git submodule foreach git config core.filemode false
    chmod -R 777 * 
```

2. Настройка базы данных.
Необходимо создать файл `app/config/db_parameters.yml` со следующим содержанием в соответствии с вашими настройками
```yaml
parameters:
    database_driver:   pdo_mysql
    database_host:     localhost
    database_port:     ~
    database_name:     blog_tender
    database_user:     user
    database_password: password
```

3.  Разворачивание базы данных
```
    php app/console doctrine:schema:update --force
    php app/console doctrine:fixtures:load
```

4. Распаковка файлов в папку `web/`
```
    php app/console assets:install
```
