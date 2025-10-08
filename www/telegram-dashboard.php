<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Dashboard - Baza Project</title>
    <link rel="stylesheet" href="/static/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🤖 Telegram Dashboard</h1>
            <p class="subtitle">Панель управления уведомлениями</p>
        </header>

        <!-- Отправка сообщений -->
        <section class="server-info">
            <h3>📨 Отправка сообщений</h3>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;">
                <strong>🚀 Шаблоны:</strong>
                <button onclick="loadTemplate('server_status')" class="btn btn-secondary" style="margin: 5px;">📊 Статус</button>
                <button onclick="loadTemplate('error_alert')" class="btn btn-secondary" style="margin: 5px;">🚨 Ошибка</button>
                <button onclick="loadTemplate('deployment')" class="btn btn-secondary" style="margin: 5px;">🔧 Деплой</button>
            </div>

            <form id="messageForm">
                <div style="margin-bottom: 15px;">
                    <label><strong>💬 Сообщение:</strong></label>
                    <textarea id="messageText" name="message" rows="6" style="width: 100%;"></textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <input type="radio" id="send_default" name="send_type" value="default" checked>
                    <label for="send_default">Чат по умолчанию</label>
                    
                    <input type="radio" id="send_custom" name="send_type" value="custom" style="margin-left: 20px;">
                    <label for="send_custom">Указанный чат</label>
                    <input type="text" id="customChatId" placeholder="Chat ID" style="margin-left: 10px;" disabled>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn">📤 Отправить</button>
                    <button type="button" onclick="testService()" class="btn btn-secondary">🔍 Тест API</button>
                </div>
            </form>
        </section>

        <!-- Тестирование -->
        <section class="server-info">
            <h3>🔧 Тестирование</h3>
            <div class="button-group">
                <button onclick="testHealth()" class="btn">❤️ Health</button>
                <button onclick="testBotInfo()" class="btn">🤖 Bot Info</button>
                <a href="/telegram/docs" class="btn btn-api" target="_blank">📚 Docs</a>
            </div>
            <div id="testResult" style="margin-top: 15px;"></div>
        </section>

        <div class="button-group">
            <a href="/" class="btn">← На главную</a>
        </div>

        <div id="result" style="margin: 20px 0;"></div>
    </div>

    <script>
        const templates = {
            server_status: `🖥️ Статус сервера Baza Project\n📊 Сервер: 81.94.156.217\n🕐 ${new Date().toLocaleString()}\n✅ Все системы работают нормально`,

            error_alert: `🚨 Ошибка сервера!\n📡 Сервер: 81.94.156.217\n🕐 ${new Date().toLocaleString()}\n❗ Требуется вмешательство`,

            deployment: `🔧 Деплой завершен\n📦 Baza Project v1.2.0\n🕐 ${new Date().toLocaleString()}\n✅ Успешно`
        };

        function loadTemplate(templateName) {
            document.getElementById('messageText').value = templates[templateName];
        }

        document.getElementById('send_custom').addEventListener('change', function() {
            document.getElementById('customChatId').disabled = !this.checked;
        });

        document.getElementById('send_default').addEventListener('change', function() {
            document.getElementById('customChatId').disabled = this.checked;
        });

        document.getElementById('messageForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const sendType = document.querySelector('input[name="send_type"]:checked').value;
            const message = document.getElementById('messageText').value;
            const chatId = document.getElementById('customChatId').value;
            
            const endpoint = sendType === 'default' ? '/telegram/send/default' : '/telegram/send';
            const payload = sendType === 'default' ? { message } : { chat_id: chatId, message };
            
            try {
                showResult('⏳ Отправка...', 'info');
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();
                showResult(response.ok ? '✅ Отправлено!' : `❌ ${result.detail}`, response.ok ? 'success' : 'error');
                
            } catch (error) {
                showResult(`❌ Ошибка: ${error.message}`, 'error');
            }
        });

        async function testHealth() {
            try {
                const response = await fetch('/telegram/health');
                const result = await response.json();
                showTestResult(JSON.stringify(result, null, 2));
            } catch (error) {
                showTestResult(`❌ ${error.message}`);
            }
        }

        async function testBotInfo() {
            try {
                const response = await fetch('/telegram/bot/info');
                const result = await response.json();
                showTestResult(JSON.stringify(result, null, 2));
            } catch (error) {
                showTestResult(`❌ ${error.message}`);
            }
        }

        async function testService() {
            try {
                const response = await fetch('/telegram/health');
                const result = await response.json();
                showResult(result.status === 'healthy' ? '✅ Сервис работает' : '❌ Сервис не работает', 
                         result.status === 'healthy' ? 'success' : 'error');
            } catch (error) {
                showResult('❌ Сервис недоступен', 'error');
            }
        }

        function showResult(message, type) {
            const className = type === 'success' ? 'status-online' : 
                            type === 'error' ? 'status-offline' : 'status-warning';
            document.getElementById('result').innerHTML = `<div class="status ${className}">${message}</div>`;
        }

        function showTestResult(data) {
            document.getElementById('testResult').innerHTML = 
                `<pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;">${data}</pre>`;
        }
    </script>
</body>
</html>
