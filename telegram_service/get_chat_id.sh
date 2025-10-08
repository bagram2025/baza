#!/bin/bash
echo "📱 Отправь любое сообщение своему боту в Telegram..."
echo "⏳ Ожидаю 10 секунд..."
sleep 10

echo "🔍 Получаю Chat ID..."
response=$(curl -s "https://api.telegram.org/bot6642548401:AAG1df9IFhBijkNYLSVWUPe2FfodsxMOIUg/getUpdates")

echo "📋 Результат:"
echo "$response" | python3 -m json.tool

# Автоматическое извлечение Chat ID
chat_id=$(echo "$response" | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2)
if [ ! -z "$chat_id" ]; then
    echo "✅ Твой Chat ID: $chat_id"
else
    echo "❌ Сообщений не найдено. Отправь сообщение боту и повтори."
fi
