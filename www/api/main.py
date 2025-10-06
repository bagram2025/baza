from fastapi import FastAPI, HTTPException
from datetime import datetime
import os

app = FastAPI(
    title="Baza API",
    description="API для проекта Baza",
    version="1.0.0"
)

items = [
    {"id": 0, "name": "Ноутбук", "price": 999.99},
    {"id": 1, "name": "Телефон", "price": 699.99}
]

@app.get("/")
async def root():
    return {
        "message": "Baza API работает",
        "timestamp": datetime.now().isoformat()
    }

@app.get("/health")
async def health():
    return {"status": "healthy"}

@app.get("/items")
async def get_items():
    return {"items": items}

@app.get("/items/{item_id}")
async def get_item(item_id: int):
    if item_id < 0 or item_id >= len(items):
        raise HTTPException(status_code=404, detail="Товар не найден")
    return items[item_id]

print("✅ FastAPI запущен")
