#!/bin/bash

# Скрипт для создания структуры каталогов Ansible и установки Ansible

set -e  # Прерывать выполнение при ошибках

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Создание структуры каталогов Ansible...${NC}"

# Создаем основную директорию ansible
mkdir -p ansible

# Создаем стандартные подкаталоги Ansible
mkdir -p ansible/group_vars
mkdir -p ansible/host_vars
mkdir -p ansible/roles
mkdir -p ansible/inventories
mkdir -p ansible/playbooks
mkdir -p ansible/files
mkdir -p ansible/templates
mkdir -p ansible/tasks
mkdir -p ansible/handlers
mkdir -p ansible/vars
mkdir -p ansible/defaults
mkdir -p ansible/meta
mkdir -p ansible/library
mkdir -p ansible/module_utils
mkdir -p ansible/filter_plugins

echo -e "${GREEN}Структура каталогов создана:${NC}"
tree ansible/ || ls -la ansible/

# Создаем базовые файлы конфигурации
echo -e "${YELLOW}Создание базовых файлов конфигурации...${NC}"

# Создаем минимальный ansible.cfg
cat > ansible/ansible.cfg << EOF
[defaults]
inventory = inventories/
host_key_checking = False
remote_user = root
roles_path = roles
library = library
module_utils = module_utils
filter_plugins = filter_plugins

[ssh_connection]
ssh_args = -o ControlMaster=auto -o ControlPersist=60s
control_path = ~/.ssh/ansible-%%r@%%h:%%p
EOF

# Создаем пример inventory файла
cat > ansible/inventories/hosts << EOF
[all:vars]
ansible_connection=ssh
ansible_user=root

[local]
localhost ansible_connection=local

[example_servers]
# server1.example.com
# server2.example.com
EOF

# Создаем пример playbook
cat > ansible/playbooks/site.yml << EOF
---
- name: Example Playbook
  hosts: local
  become: yes
  tasks:
    - name: Ensure package is installed
      package:
        name: htop
        state: present
EOF

echo -e "${GREEN}Базовые файлы конфигурации созданы${NC}"

# Проверяем установлен ли Ansible
echo -e "${YELLOW}Проверка установки Ansible...${NC}"

if command -v ansible &> /dev/null; then
    ANSIBLE_VERSION=$(ansible --version | head -n1)
    echo -e "${GREEN}Ansible уже установлен: $ANSIBLE_VERSION${NC}"
else
    echo -e "${YELLOW}Ansible не найден. Установка...${NC}"
    
    # Определяем дистрибутив и устанавливаем Ansible
    if [[ -f /etc/os-release ]]; then
        source /etc/os-release
        case $ID in
            ubuntu|debian)
                sudo apt update
                sudo apt install -y software-properties-common
                sudo apt-add-repository --yes --update ppa:ansible/ansible
                sudo apt install -y ansible
                ;;
            centos|rhel|fedora)
                if command -v dnf &> /dev/null; then
                    sudo dnf install -y ansible
                else
                    sudo yum install -y ansible
                fi
                ;;
            arch)
                sudo pacman -Sy --noconfirm ansible
                ;;
            *)
                echo -e "${RED}Неизвестный дистрибутив. Установите Ansible вручную.${NC}"
                echo "Посетите: https://docs.ansible.com/ansible/latest/installation_guide/intro_installation.html"
                exit 1
                ;;
        esac
    else
        echo -e "${RED}Не удалось определить дистрибутив. Установите Ansible вручную.${NC}"
        exit 1
    fi
    
    # Проверяем успешность установки
    if command -v ansible &> /dev/null; then
        ANSIBLE_VERSION=$(ansible --version | head -n1)
        echo -e "${GREEN}Ansible успешно установлен: $ANSIBLE_VERSION${NC}"
    else
        echo -e "${RED}Ошибка установки Ansible${NC}"
        exit 1
    fi
fi

# Создаем README файл
cat > ansible/README.md << EOF
# Ansible Project Structure

Это автоматически созданная структура каталогов для проекта Ansible.

## Структура каталогов:

- **group_vars/** - переменные для групп хостов
- **host_vars/** - переменные для отдельных хостов  
- **roles/** - роли Ansible
- **inventories/** - файлы инвентаризации
- **playbooks/** - плейбуки
- **files/** - статические файлы
- **templates/** - шаблоны Jinja2
- **tasks/** - отдельные файлы задач
- **handlers/** - обработчики
- **vars/** - переменные
- **defaults/** - переменные по умолчанию для ролей
- **meta/** - метаданные ролей
- **library/** - кастомные модули
- **module_utils/** - утилиты для модулей
- **filter_plugins/** - кастомные фильтры

## Использование:

\`\`\`bash
cd ansible
ansible-playbook -i inventories/hosts playbooks/site.yml
\`\`\`
EOF

echo -e "${GREEN}Готово!${NC}"
echo -e "${GREEN}Структура проекта создана в директории: $(pwd)/ansible${NC}"
echo -e "${YELLOW}Перейдите в директорию ansible для начала работы: cd ansible${NC}"
