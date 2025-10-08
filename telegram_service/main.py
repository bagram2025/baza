from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import httpx
import asyncio
import logging
from typing import Optional
import os

app = FastAPI(
    title="Telegram Notification Service",
    description="Микросервис для отправки уведомлений в Telegram",
    version="1.0.0"
)

# Настройка логирования
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Модели запросов
class TelegramMessage(BaseModel):
    chat_id: str
    message: str
    parse_mode: Optional[str] = "HTML"
    disable_notification: Optional[bool] = False

class TelegramMessageSimple(BaseModel):
    message: str
    parse_mode: Optional[str] = "HTML"

# Конфигурация
TELEGRAM_BOT_TOKEN = os.getenv("TELEGRAM_BOT_TOKEN", "YOUR_BOT_TOKEN_HERE")
DEFAULT_CHAT_ID = os.getenv("DEFAULT_CHAT_ID", "YOUR_CHAT_ID_HERE")

async def send_telegram_message(chat_id: str, message: str, parse_mode: str = "HTML", disable_notification: bool = False):
    """Отправка сообщения в Telegram"""
    if not TELEGRAM_BOT_TOKEN or TELEGRAM_BOT_TOKEN == "YOUR_BOT_TOKEN_HERE":
        raise HTTPException(status_code=500, detail="Telegram bot token not configured")
    
    url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage"
    
    payload = {
        "chat_id": chat_id,
        "text": message,
        "parse_mode": parse_mode,
        "disable_notification": disable_notification
    }
    
    try:
        async with httpx.AsyncClient(timeout=30.0) as client:
            response = await client.post(url, json=payload)
            response.raise_for_status()
            
            logger.info(f"Message sent to chat {chat_id}")
            return response.json()
            
    except httpx.HTTPError as e:
        logger.error(f"Telegram API error: {e}")
        raise HTTPException(status_code=500, detail=f"Telegram API error: {str(e)}")
    except Exception as e:
        logger.error(f"Unexpected error: {e}")
        raise HTTPException(status_code=500, detail=f"Unexpected error: {str(e)}")

# Эндпоинты
@app.get("/")
async def root():
    return {
        "service": "Telegram Notification Service",
        "status": "running",
        "endpoints": {
            "health": "/health",
            "send_message": "/send",
            "send_to_default": "/send/default",
            "docs": "/docs"
        }
    }

@app.get("/health")
async def health_check():
    """Проверка здоровья сервиса"""
    return {
        "status": "healthy",
        "service": "telegram",
        "bot_configured": TELEGRAM_BOT_TOKEN != "YOUR_BOT_TOKEN_HERE"
    }

@app.post("/send")
async def send_message(message_data: TelegramMessage):
    """Отправка сообщения в указанный чат"""
    result = await send_telegram_message(
        chat_id=message_data.chat_id,
        message=message_data.message,
        parse_mode=message_data.parse_mode,
        disable_notification=message_data.disable_notification
    )
    return {
        "status": "success",
        "message": "Message sent successfully",
        "telegram_response": result
    }

@app.post("/send/default")
async def send_to_default(message_data: TelegramMessageSimple):
    """Отправка сообщения в чат по умолчанию"""
    if not DEFAULT_CHAT_ID or DEFAULT_CHAT_ID == "YOUR_CHAT_ID_HERE":
        raise HTTPException(status_code=500, detail="Default chat ID not configured")
    
    result = await send_telegram_message(
        chat_id=DEFAULT_CHAT_ID,
        message=message_data.message,
        parse_mode=message_data.parse_mode
    )
    return {
        "status": "success", 
        "message": "Message sent to default chat",
        "telegram_response": result
    }

@app.get("/bot/info")
async def get_bot_info():
    """Получение информации о боте"""
    if not TELEGRAM_BOT_TOKEN or TELEGRAM_BOT_TOKEN == "YOUR_BOT_TOKEN_HERE":
        raise HTTPException(status_code=500, detail="Bot token not configured")
    
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
