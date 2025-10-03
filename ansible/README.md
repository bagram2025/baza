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

```bash
cd ansible
ansible-playbook -i inventories/hosts playbooks/site.yml
```
