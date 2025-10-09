from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import httpx
import os
from typing import Optional

app = FastAPI(
    title="Telegram Notification Service",
    description="Микросервис для отправки уведомлений в Telegram",
    version="1.0.0"
)

# Модели данных
class TelegramMessage(BaseModel):
    chat_id: str
    message: str
    parse_mode: Optional[str] = "HTML"

class SimpleMessage(BaseModel):
    message: str
    parse_mode: Optional[str] = "HTML"

# Конфигурация (замени на свои значения)
TELEGRAM_BOT_TOKEN = os.getenv("TELEGRAM_BOT_TOKEN", "6642548401:AAG1df9IFhBijkNYLSVWUPe2FfodsxMOIUg")
DEFAULT_CHAT_ID = os.getenv("DEFAULT_CHAT_ID", "6905230450")

async def send_telegram_message(chat_id: str, message: str, parse_mode: str = "HTML"):
    """Отправка сообщения в Telegram"""
    if TELEGRAM_BOT_TOKEN.startswith("6642548401:"):
        # Токен настроен, можно использовать
        pass
    else:
        raise HTTPException(status_code=500, detail="Telegram bot token not configured")
    
    url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage"
    
    payload = {
        "chat_id": chat_id,
        "text": message,
        "parse_mode": parse_mode
    }
    
    try:
        async with httpx.AsyncClient(timeout=30.0) as client:
            response = await client.post(url, json=payload)
            response.raise_for_status()
            return response.json()
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Telegram API error: {str(e)}")

# Эндпоинты
@app.get("/")
async def root():
    return {
        "service": "Telegram Notification Service",
        "status": "running",
        "bot_configured": True
    }

@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "service": "telegram",
        "bot_configured": True
    }

@app.post("/send")
async def send_message(message_data: TelegramMessage):
    result = await send_telegram_message(
        chat_id=message_data.chat_id,
        message=message_data.message,
        parse_mode=message_data.parse_mode
    )
    return {
        "status": "success",
        "message": "Message sent successfully",
        "response": result
    }

@app.post("/send/default")
async def send_to_default(message_data: SimpleMessage):
    result = await send_telegram_message(
        chat_id=DEFAULT_CHAT_ID,
        message=message_data.message,
        parse_mode=message_data.parse_mode
    )
    return {
        "status": "success", 
        "message": "Message sent to default chat",
        "response": result
    }

@app.get("/bot/info")
async def get_bot_info():
    """Получение информации о боте"""
    url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/getMe"
    
    try:
        async with httpx.AsyncClient() as client:
            response = await client.get(url)
            response.raise_for_status()
            return response.json()
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8005)
