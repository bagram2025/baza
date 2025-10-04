<!DOCTYPE html>
<html>
<head>
    <title>Redis Test - Baza Project</title>
    <link rel="stylesheet" href="/static/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔴 Redis Test</h1>
            <p>Тестирование Redis на локальном сервере</p>
        </div>
        <div class="service-card">
            <h2>Redis Connection Status</h2>
            <?php
            try {
                $redis = new Redis();
                $redis->connect('127.0.0.1', 6379, 2.5);
                $redis->auth('baza_redis_password_123');
                
                echo '<div style="color: green; font-weight: bold;">✅ Redis подключен и работает!</div>';
                
                // Основная информация
                $info = $redis->info();
                $install_time = $redis->get('ansible:install:time');
                $project_status = $redis->get('project:baza:status');
                $visits = $redis->incr('web:page:visits');
                
                echo '<div style="margin: 20px 0;">';
                echo '<p><strong>Версия Redis:</strong> ' . $info['redis_version'] . '</p>';
                echo '<p><strong>Использовано памяти:</strong> ' . $info['used_memory_human'] . '</p>';
                echo '<p><strong>Подключений:</strong> ' . $info['connected_clients'] . '</p>';
                echo '<p><strong>Время установки:</strong> ' . ($install_time ?: 'Не установлено') . '</p>';
                echo '<p><strong>Статус проекта:</strong> ' . ($project_status ?: 'Не установлен') . '</p>';
                echo '<p><strong>Посещений страницы:</strong> ' . $visits . '</p>';
                echo '</div>';
                
                // Демонстрация работы с разными типами данных
                echo '<h3>Демонстрация работы:</h3>';
                
                // Строки
                $redis->set('demo:string', 'Hello Redis!');
                echo '<p><strong>String:</strong> ' . $redis->get('demo:string') . '</p>';
                
                // Списки
                $redis->lpush('demo:list', 'item1', 'item2', 'item3');
                $list_items = $redis->lrange('demo:list', 0, -1);
                echo '<p><strong>List:</strong> ' . implode(', ', $list_items) . '</p>';
                
                // Хэши
                $redis->hset('demo:hash', 'name', 'Baza Project');
                $redis->hset('demo:hash', 'redis_port', 6379);
                $hash_data = $redis->hgetall('demo:hash');
                echo '<p><strong>Hash:</strong> ' . json_encode($hash_data) . '</p>';
                
            } catch (Exception $e) {
                echo '<div style="color: red; font-weight: bold;">❌ Ошибка: ' . $e->getMessage() . '</div>';
                echo '<p>Проверьте:</p>';
                echo '<ul>';
                echo '<li>Установлен ли php-redis: <code>sudo apt install php-redis</code></li>';
                echo '<li>Запущен ли Redis: <code>sudo systemctl status redis-server</code></li>';
                echo '<li>Правильный ли пароль в конфигурации</li>';
                echo '</ul>';
            }
            ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/" class="btn">← На главную</a>
            </div>
        </div>
    </div>
</body>
</html>
