from flask import Flask, jsonify, request, render_template
import os
import datetime

app = Flask(__name__)

@app.route('/')
def index():
    """Главная страница Flask приложения"""
    return '''
    <!DOCTYPE html>
    <html>
    <head>
        <title>Flask App - Baza Project</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
            .header { text-align: center; margin-bottom: 40px; }
            .card { background: white; padding: 20px; margin: 20px 0; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
            .status { color: #28a745; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>🐍 Flask Application</h1>
            <p class="status">✅ Статус: Онлайн</p>
            <p>Сервер: 81.94.156.217:8080</p>
        </div>
        
        <div class="card">
            <h2>🚀 О приложении</h2>
            <p>Это Flask приложение работает как часть Baza Project.</p>
            <p><strong>Время сервера:</strong> ''' + datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S") + '''</p>
        </div>

        <div class="card">
            <h2>🔧 API Endpoints</h2>
            <a href="/flask/health" class="btn">❤️ Health Check</a>
            <a href="/flask/api/info" class="btn">📊 System Info</a>
            <a href="/flask/api/time" class="btn">🕐 Server Time</a>
        </div>

        <div class="card">
            <h2>🔗 Навигация</h2>
            <a href="/" class="btn">🏠 На главную</a>
            <a href="/flask-dashboard.php" class="btn">📊 Dashboard</a>
        </div>
    </body>
    </html>
    '''

@app.route('/health')
def health():
    """Health check endpoint"""
    return jsonify({
        "status": "healthy",
        "service": "flask",
        "timestamp": datetime.datetime.now().isoformat(),
        "version": "1.0.0"
    })

@app.route('/api/info')
def api_info():
    """System information"""
    return jsonify({
        "server": "81.94.156.217",
        "port": 8003,
        "environment": "production",
        "timestamp": datetime.datetime.now().isoformat()
    })

@app.route('/api/time')
def api_time():
    """Server time"""
    return jsonify({
        "server_time": datetime.datetime.now().isoformat(),
        "timezone": "UTC"
    })

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8003, debug=False)
