#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

VENV_DIR=".venv"
REQUIREMENTS="requirements.txt"
APP_MODULE="api.main:app"

echo "==> Проверка python3"
if ! command -v python3 &>/dev/null; then
    echo "Ошибка: python3 не найден" >&2
    exit 1
fi

echo "==> Создание виртуального окружения в $VENV_DIR"
if [ ! -d "$VENV_DIR" ]; then
    python3 -m venv "$VENV_DIR"
fi

echo "==> Обновление pip"
"$VENV_DIR/bin/python" -m pip install --upgrade pip

echo "==> Установка зависимостей из $REQUIREMENTS"
"$VENV_DIR/bin/pip" install -r "$REQUIREMENTS"

echo "==> Запуск проекта: uvicorn $APP_MODULE"
export PYTHONPATH="$ROOT_DIR"
"$VENV_DIR/bin/uvicorn" "$APP_MODULE" --reload --host 0.0.0.0 --port 8000
