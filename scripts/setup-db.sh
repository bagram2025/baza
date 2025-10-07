#!/bin/bash

# setup_postgres.sh
set -e

echo "🐘 Настройка PostgreSQL 16.10..."

# Параметры
DB_USER="baza_user"
DB_PASSWORD="your_secure_password_123"
DB_NAME="baza_db"
PG_VERSION="16"

# Функция для вывода сообщений
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1"
}

# Проверка прав
if [ "$EUID" -ne 0 ]; then
    echo "❌ Запусти скрипт с sudo: sudo ./setup_postgres.sh"
    exit 1
fi

log "Проверка установки PostgreSQL..."
if ! command -v psql &> /dev/null; then
    log "Установка PostgreSQL..."
    apt update
    apt install -y postgresql postgresql-contrib postgresql-client libpq-dev
fi

log "Запуск PostgreSQL..."
systemctl start postgresql
systemctl enable postgresql

log "Создание пользователя $DB_USER..."
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASSWORD' CREATEDB LOGIN;" 2>/dev/null || log "Пользователь уже существует"

log "Создание базы данных $DB_NAME..."
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;" 2>/dev/null || log "База данных уже существует"

log "Настройка конфигурации..."
# Настройка listen_addresses
sed -i "s/#listen_addresses = 'localhost'/listen_addresses = '*'/g" /etc/postgresql/$PG_VERSION/main/postgresql.conf

# Добавление правила доступа
if ! grep -q "host all all 0.0.0.0/0 md5" /etc/postgresql/$PG_VERSION/main/pg_hba.conf; then
    echo "host all all 0.0.0.0/0 md5" >> /etc/postgresql/$PG_VERSION/main/pg_hba.conf
fi

log "Перезапуск PostgreSQL..."
systemctl restart postgresql

log "Настройка фаервола..."
ufw allow 5432/tcp

log "Создание тестовых данных..."
sudo -u postgres psql -d $DB_NAME -c "
CREATE TABLE IF NOT EXISTS test_table (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO test_table (name) VALUES 
('Test Record 1'),
('Test Record 2')
ON CONFLICT DO NOTHING;
"

log "✅ Настройка завершена!"
echo ""
echo "📊 Информация о подключении:"
echo "   Хост: localhost"
echo "   Порт: 5432"
echo "   База: $DB_NAME"
echo "   Пользователь: $DB_USER"
echo "   Пароль: $DB_PASSWORD"
echo ""
echo "🔗 Проверка подключения:"
echo "   psql -h localhost -U $DB_USER -d $DB_NAME -W"
