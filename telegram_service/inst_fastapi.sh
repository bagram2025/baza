# Обновляем pip
pip3 install --upgrade pip

# Устанавливаем FastAPI и зависимости
pip3 install fastapi uvicorn httpx

# Проверяем установку
python3 -c "from fastapi import FastAPI; print('✅ FastAPI установлен')"
