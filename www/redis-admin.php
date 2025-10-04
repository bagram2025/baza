<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redis Admin - Baza Project</title>
    <link rel="stylesheet" href="/static/style.css">
    <style>
        .redis-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #dc382c;
            margin: 5px 0;
        }
        .key-list {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
            max-height: 300px;
            overflow-y: auto;
        }
        .key-item {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-family: monospace;
            display: flex;
            justify-content: space-between;
        }
        .key-item:hover {
            background: #e9ecef;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8em;
        }
        .form-inline {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }
        .form-inline input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔴 Redis Admin Panel</h1>
            <p>Управление и мониторинг Redis</p>
        </div>

        <?php
        $redis = new Redis();
        $host = '127.0.0.1';
        $port = 6379;
        $password = 'baza_redis_password_123';
        
        try {
            $redis->connect($host, $port, 2.5);
            $redis->auth($password);
            
            $info = $redis->info();
            $keys_count = $redis->dbSize();
            $memory_used = $info['used_memory_human'];
            $connected_clients = $info['connected_clients'];
            $uptime = gmdate("H:i:s", $info['uptime_in_seconds']);
            
            // Обработка действий
            if ($_POST['action'] ?? false) {
                switch($_POST['action']) {
                    case 'set_key':
                        if (!empty($_POST['key']) && isset($_POST['value'])) {
                            $redis->set($_POST['key'], $_POST['value']);
                            echo '<div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">✅ Ключ "'.htmlspecialchars($_POST['key']).'" установлен</div>';
                        }
                        break;
                        
                    case 'delete_key':
                        if (!empty($_POST['key'])) {
                            $redis->del($_POST['key']);
                            echo '<div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;">🗑️ Ключ "'.htmlspecialchars($_POST['key']).'" удален</div>';
                        }
                        break;
                        
                    case 'flush_db':
                        $redis->flushDb();
                        echo '<div style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 10px 0;">⚠️ База данных очищена</div>';
                        break;
                }
            }
        ?>
        
        <div class="redis-stats">
            <div class="stat-card">
                <h3>📊 Ключи</h3>
                <div class="stat-value"><?php echo $keys_count; ?></div>
                <small>Всего в БД</small>
            </div>
            <div class="stat-card">
                <h3>💾 Память</h3>
                <div class="stat-value"><?php echo $memory_used; ?></div>
                <small>Использовано</small>
            </div>
            <div class="stat-card">
                <h3>👥 Клиенты</h3>
                <div class="stat-value"><?php echo $connected_clients; ?></div>
                <small>Подключено</small>
            </div>
            <div class="stat-card">
                <h3>⏱️ Uptime</h3>
                <div class="stat-value"><?php echo $uptime; ?></div>
                <small>Время работы</small>
            </div>
        </div>

        <div class="service-card">
            <h3>🔧 Быстрые действия</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin: 15px 0;">
                <form method="post" style="margin: 0;">
                    <input type="hidden" name="action" value="flush_db">
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('Очистить всю базу данных?')">🧹 Очистить БД</button>
                </form>
                
                <form method="post" class="form-inline">
                    <input type="hidden" name="action" value="set_key">
                    <input type="text" name="key" placeholder="Имя ключа" required>
                    <input type="text" name="value" placeholder="Значение" required>
                    <button type="submit" class="btn btn-small">➕ Добавить</button>
                </form>
            </div>
        </div>

        <div class="service-card">
            <h3>🗂️ Ключи в базе</h3>
            <div class="key-list">
                <?php
                $keys = $redis->keys('*');
                if (empty($keys)) {
                    echo '<div style="text-align: center; color: #666; padding: 20px;">База данных пуста</div>';
                } else {
                    foreach($keys as $key) {
                        $type = $redis->type($key);
                        $ttl = $redis->ttl($key);
                        $value = $redis->get($key);
                        if (strlen($value) > 50) {
                            $value = substr($value, 0, 50) . '...';
                        }
                        
                        echo '<div class="key-item">';
                        echo '<div>';
                        echo '<strong>' . htmlspecialchars($key) . '</strong>';
                        echo '<br><small style="color: #666;">' . $type . ' • ' . ($ttl > 0 ? 'TTL: '.$ttl.'s' : 'no TTL') . '</small>';
                        echo '</div>';
                        echo '<div style="text-align: right;">';
                        echo '<small style="color: #888;">' . htmlspecialchars($value) . '</small><br>';
                        echo '<form method="post" style="display: inline;">';
                        echo '<input type="hidden" name="action" value="delete_key">';
                        echo '<input type="hidden" name="key" value="' . htmlspecialchars($key) . '">';
                        echo '<button type="submit" class="btn-small" style="background: #dc3545; color: white; border: none; padding: 3px 8px; border-radius: 3px; cursor: pointer;" onclick="return confirm(\'Удалить ключ '.htmlspecialchars($key).'?\')">🗑️</button>';
                        echo '</form>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div class="service-card">
            <h3>📈 Статистика Redis</h3>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;">
                <strong>Server:</strong> <?php echo $info['redis_version']; ?> (<?php echo $info['os']; ?>)<br>
                <strong>Process ID:</strong> <?php echo $info['process_id']; ?><br>
                <strong>TCP Port:</strong> <?php echo $info['tcp_port']; ?><br>
                <strong>Connections:</strong> <?php echo $info['total_connections_received']; ?> total<br>
                <strong>Commands:</strong> <?php echo $info['total_commands_processed']; ?> processed<br>
                <strong>Memory Peak:</strong> <?php echo $info['used_memory_peak_human']; ?><br>
                <strong>Clients:</strong> <?php echo $info['connected_clients']; ?> connected, <?php echo $info['blocked_clients']; ?> blocked<br>
            </div>
        </div>

        <?php
        } catch (Exception $e) {
            echo '<div class="service-card">';
            echo '<h2 style="color: red;">❌ Ошибка подключения к Redis</h2>';
            echo '<p>' . $e->getMessage() . '</p>';
            echo '</div>';
        }
        ?>

        <div style="text-align: center; margin-top: 20px;">
            <a href="/" class="btn">← На главную</a>
        </div>
    </div>
</body>
</html>
