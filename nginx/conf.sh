#!/bin/bash

# Скрипт переноса конфигурации Baza в Nginx
# Автор: [Ваше имя]
# Дата: $(date +%Y-%m-%d)

set -e  # Прерывать выполнение при ошибках

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Функции для цветного вывода
print_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Переменные
CONFIG_FILE="baza"
NGINX_CONF_DIR="/etc/nginx"
SITES_AVAILABLE_DIR="$NGINX_CONF_DIR/sites-available"
SITES_ENABLED_DIR="$NGINX_CONF_DIR/sites-enabled"
BACKUP_DIR="/etc/nginx/backup"

# Проверка прав администратора
check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "Этот скрипт должен запускаться с правами root"
        exit 1
    fi
}

# Проверка существования исходного файла конфигурации
check_source_config() {
    if [[ ! -f "$CONFIG_FILE" ]]; then
        print_error "Файл конфигурации '$CONFIG_FILE' не найден в текущей директории"
        print_info "Текущая директория: $(pwd)"
        print_info "Содержимое текущей директории:"
        ls -la
        exit 1
    fi
    
    print_info "Найден файл конфигурации: $CONFIG_FILE"
}

# Создание резервной копии существующей конфигурации
create_backup() {
    local target_file="$SITES_AVAILABLE_DIR/$CONFIG_FILE"
    
    if [[ -f "$target_file" ]]; then
        print_info "Создание резервной копии существующей конфигурации..."
        
        # Создаем директорию для бэкапов если её нет
        mkdir -p "$BACKUP_DIR"
        
        local backup_file="$BACKUP_DIR/${CONFIG_FILE}.backup.$(date +%Y%m%d_%H%M%S)"
        cp "$target_file" "$backup_file"
        print_info "Резервная копия создана: $backup_file"
    fi
}

# Копирование конфигурации
copy_config() {
    print_info "Копирование конфигурации в $SITES_AVAILABLE_DIR/"
    
    cp "$CONFIG_FILE" "$SITES_AVAILABLE_DIR/"
    chmod 644 "$SITES_AVAILABLE_DIR/$CONFIG_FILE"
    
    print_info "Конфигурация успешно скопирована"
}

# Активация конфигурации (создание симлинка)
enable_site() {
    local enabled_link="$SITES_ENABLED_DIR/$CONFIG_FILE"
    
    if [[ ! -L "$enabled_link" ]]; then
        print_info "Активация сайта..."
        ln -s "$SITES_AVAILABLE_DIR/$CONFIG_FILE" "$enabled_link"
        print_info "Сайт активирован"
    else
        print_info "Сайт уже активирован"
    fi
}

# Проверка синтаксиса Nginx конфигурации
test_nginx_config() {
    print_info "Проверка синтаксиса Nginx конфигурации..."
    
    if nginx -t; then
        print_info "Синтаксис конфигурации Nginx корректен"
        return 0
    else
        print_error "Обнаружены ошибки в конфигурации Nginx"
        return 1
    fi
}

# Перезапуск Nginx
reload_nginx() {
    print_info "Перезапуск Nginx..."
    
    # Пытаемся сделать плавный перезапуск
    if systemctl reload nginx 2>/dev/null; then
        print_info "Nginx успешно перезапущен"
    elif service nginx reload 2>/dev/null; then
        print_info "Nginx успешно перезапущен"
    else
        print_warning "Не удалось выполнить плавный перезапуск, пробуем полный restart..."
        systemctl restart nginx || service nginx restart
        print_info "Nginx перезапущен"
    fi
}

# Проверка статуса Nginx
check_nginx_status() {
    print_info "Проверка статуса Nginx..."
    
    if systemctl is-active --quiet nginx; then
        print_info "Nginx работает нормально"
    else
        print_error "Nginx не запущен!"
        exit 1
    fi
}

# Основная функция
main() {
    print_info "Начало переноса конфигурации Baza в Nginx"
    print_info "Время начала: $(date)"
    
    # Выполняем шаги по порядку
    check_root
    check_source_config
    create_backup
    copy_config
    enable_site
    
    # Проверяем конфигурацию перед перезапуском
    if test_nginx_config; then
        reload_nginx
        sleep 2  # Даем время для применения изменений
        check_nginx_status
        
        print_info "Конфигурация успешно применена!"
        print_info "Файл конфигурации: $SITES_AVAILABLE_DIR/$CONFIG_FILE"
        print_info "Активирован в: $SITES_ENABLED_DIR/$CONFIG_FILE"
    else
        print_error "Конфигурация содержит ошибки. Nginx не был перезапущен."
        print_info "Исправьте ошибки и запустите скрипт снова"
        exit 1
    fi
    
    print_info "Процесс завершен успешно!"
    print_info "Время завершения: $(date)"
}

# Обработка сигналов
trap 'print_error "Скрипт прерван пользователем"; exit 1' INT TERM

# Запуск основной функции
main "$@"
