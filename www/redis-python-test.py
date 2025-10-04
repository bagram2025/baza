#!/usr/bin/env python3
import subprocess
import sys

# Попробуем несколько способов импорта redis
try:
    import redis
except ImportError:
    try:
        # Попробуем использовать redis из системных пакетов
        subprocess.check_call([sys.executable, "-c", "import redis"])
    except:
        print("Content-Type: text/html; charset=utf-8\n")
        print("""
        <html>
        <head><title>Redis Python Test</title>
        <link rel="stylesheet" href="/static/style.css">
        </head>
        <body>
        <div class="container">
            <div class="header"><h1>🔴 Redis Python Test</h1></div>
            <div class="service-card">
                <h2 style='color: orange;'>⚠️ Redis Python client not installed</h2>
                <p>Install with: <code>sudo apt install python3-redis</code></p>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="/" class="btn">← На главную</a>
                </div>
            </div>
        </div>
        </body>
        </html>
        """)
        sys.exit(0)

print("Content-Type: text/html; charset=utf-8\n")
print("""
<html>
<head><title>Redis Python Test</title>
<link rel="stylesheet" href="/static/style.css">
</head>
<body>
<div class="container">
    <div class="header"><h1>🔴 Redis Python Test</h1></div>
    <div class="service-card">
""")

try:
    r = redis.Redis(
        host='127.0.0.1',
        port=6379,
        password='baza_redis_password_123',
        decode_responses=True
    )
    
    r.ping()
    print("<h2 style='color: green;'>✅ Redis подключен через Python!</h2>")
    
    # Тестовые операции
    r.set('python:test', 'Hello from Python Redis!')
    test_value = r.get('python:test')
    visits = r.incr('python:visits')
    
    print(f"<p><strong>Python visits:</strong> {visits}</p>")
    print(f"<p><strong>Test value:</strong> {test_value}</p>")
    
    # Информация о сервере
    info = r.info()
    print(f"<p><strong>Redis version:</strong> {info['redis_version']}</p>")
    print(f"<p><strong>Memory used:</strong> {info['used_memory_human']}</p>")
    
except Exception as e:
    print(f"<h2 style='color: red;'>❌ Ошибка подключения: {e}</h2>")

print("""
        <div style="text-align: center; margin-top: 20px;">
            <a href="/" class="btn">← На главную</a>
        </div>
    </div>
</div>
</body>
</html>
""")
