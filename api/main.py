from fastapi import FastAPI, HTTPException
from datetime import datetime
import os

app = FastAPI(
    title="Baza FastAPI",
    description="API для проекта Baza",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# Хранилище в памяти
items = [
    {"id": 0, "name": "Ноутбук", "price": 999.99, "category": "электроника"},
    {"id": 1, "name": "Телефон", "price": 699.99, "category": "электроника"},
    {"id": 2, "name": "Книга", "price": 29.99, "category": "образование"}
]

@app.get("/")
async def root():
    return {
        "message": "Baza FastAPI работает!",
        "timestamp": datetime.now().isoformat(),
        "endpoints": [
            "/docs - документация",
            "/health - статус сервиса", 
            "/items - список товаров",
            "/items/{id} - товар по ID"
        ]
    }

@app.get("/health")
async def health():
    return {
        "status": "healthy",
        "service": "fastapi",
        "port": 8001,
        "timestamp": datetime.now().isoformat()
    }

@app.get("/items")
async def get_items():
    return {"items": items, "count": len(items)}

@app.get("/items/{item_id}")
async def get_item(item_id: int):
    if item_id < 0 or item_id >= len(items):
        raise HTTPException(status_code=404, detail="Товар не найден")
    return items[item_id]

@app.get("/info")
async def system_info():
    return {
        "python": os.sys.version,
        "platform": os.sys.platform,
        "server_time": datetime.now().isoformat()
    }

print("✅ FastAPI приложение загружено")
