
# Создаем виртуальное окружение
python3 -m venv venv
source venv/bin/activate

# Устанавливаем зависимости в venv
pip3 install fastapi uvicorn httpx

python3 -c "import fastapi, uvicorn, httpx; print('✅ Все зависимости установлены')"
