from fastapi import FastAPI, HTTPException
from fastapi.openapi.docs import get_swagger_ui_html
from fastapi.openapi.utils import get_openapi
from datetime import datetime

app = FastAPI(
    title="Baza API",
    description="API для проекта Baza", 
    version="1.0.0",
    docs_url=None,  # Отключаем стандартный docs
    redoc_url=None  # Отключаем стандартный redoc
)

# Кастомная документация Swagger UI
@app.get("/docs", include_in_schema=False)
async def custom_swagger_ui_html():
    return get_swagger_ui_html(
        openapi_url="/api/openapi.json",  # ← ВАЖНО: указываем правильный путь
        title="Baza API - Swagger UI"
    )

# Кастомная документация ReDoc
@app.get("/redoc", include_in_schema=False)
async def redoc_html():
    return get_redoc_html(
        openapi_url="/api/openapi.json",  # ← ВАЖНО: указываем правильный путь
        title="Baza API - ReDoc"
    )

items = [
    {"id": 0, "name": "Ноутбук", "price": 999.99},
    {"id": 1, "name": "Телефон", "price": 699.99}
]

@app.get("/")
async def root():
    return {"message": "Baza API работает", "timestamp": datetime.now().isoformat()}

@app.get("/health")
async def health():
    return {"status": "healthy"}

@app.get("/items")
async def get_items():
    return {"items": items}

@app.get("/items/{item_id}")
async def get_item(item_id: int):
    if item_id < 0 or item_id >= len(items):
        raise HTTPException(status_code=404, detail="Item not found")
    return items[item_id]

# Функция для ReDoc (если нужно)
from fastapi.responses import HTMLResponse
def get_redoc_html(openapi_url: str, title: str) -> HTMLResponse:
    return HTMLResponse(
        f"""
        <!DOCTYPE html>
        <html>
        <head>
            <title>{title}</title>
            <meta charset="utf-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,700|Roboto:300,400,700" rel="stylesheet">
            <style> body {{ margin: 0; padding: 0; }} </style>
        </head>
        <body>
            <redoc spec-url='{openapi_url}'></redoc>
            <script src="https://cdn.jsdelivr.net/npm/redoc@next/bundles/redoc.standalone.js"> </script>
        </body>
        </html>
        """
    )

print("✅ FastAPI с исправленной документацией запущен")
