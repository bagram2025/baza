#!/bin/bash

# Скрипт создания структуры каталогов для сайта в текущей директории
# Использование: ./create_site_structure.sh

echo "Создание структуры каталогов для сайта в текущем каталоге: $(pwd)"

# Создаем основные каталоги
mkdir -p www
mkdir -p www/static
mkdir -p www/api
mkdir -p www/restapi
mkdir -p www/flask
mkdir -p www/django

# Создаем файлы в каждом каталоге

# Основной каталог
touch www/index.html
touch www/main.py
touch www/requirements.txt

# Статические файлы
touch www/static/style.css
touch www/static/script.js
touch www/static/robots.txt
touch www/static/favicon.ico

# FastAPI приложения
touch www/api/main.py
touch www/api/__init__.py
touch www/api/requirements.txt

touch www/restapi/main.py
touch www/restapi/__init__.py
touch www/restapi/requirements.txt

# Flask приложение
touch www/flask/app.py
touch www/flask/__init__.py
touch www/flask/requirements.txt

# Django проект
touch www/django/manage.py
touch www/django/requirements.txt

# Создаем дополнительные подкаталоги для Django
mkdir -p www/django/myproject
touch www/django/myproject/__init__.py
touch www/django/myproject/settings.py
touch www/django/myproject/urls.py
touch www/django/myproject/wsgi.py
touch www/django/myproject/asgi.py

mkdir -p www/django/myapp
touch www/django/myapp/__init__.py
touch www/django/myapp/models.py
touch www/django/myapp/views.py
touch www/django/myapp/urls.py
touch www/django/myapp/admin.py
touch www/django/myapp/apps.py

# Создаем базовые конфигурационные файлы
touch www/nginx.conf
touch www/uwsgi.ini
touch www/gunicorn.conf.py

echo "Структура каталогов создана в ./www"
