<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>PostgreSQL Test - Baza Project</title>
    <link rel="stylesheet" href="/static/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🐘 PostgreSQL Connection Test</h1>
            <p class="subtitle">Проверка подключения к базе данных</p>
        </header>

        <?php
        $host = "localhost";
        $dbname = "baza_db";
        $username = "baza_user";
        $password = "your_secure_password_123";

        try {
            $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<div class='status status-online'>✅ Успешное подключение к PostgreSQL!</div>";
            
            // Создание тестовой таблицы если не существует
            $pdo->exec("CREATE TABLE IF NOT EXISTS project_visitors (
                id SERIAL PRIMARY KEY,
                ip_address VARCHAR(45),
                user_agent TEXT,
                page_visited VARCHAR(255),
                visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Запись посещения
            $stmt = $pdo->prepare("INSERT INTO project_visitors (ip_address, user_agent, page_visited) VALUES (?, ?, ?)");
            $stmt->execute([$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'], 'PostgreSQL Test Page']);
            
            // Получение статистики
            $visitors_count = $pdo->query("SELECT COUNT(*) FROM project_visitors")->fetchColumn();
            $last_visitors = $pdo->query("SELECT * FROM project_visitors ORDER BY visited_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<div class='server-info'>";
            echo "<h3>📊 Статистика посещений</h3>";
            echo "<div class='info-grid'>";
            echo "<div class='info-item'><strong>Всего посещений:</strong><br>$visitors_count</div>";
            
            // Информация о БД
            $db_size = $pdo->query("SELECT pg_size_pretty(pg_database_size('baza_db'))")->fetchColumn();
            $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<div class='info-item'><strong>Размер БД:</strong><br>$db_size</div>";
            echo "<div class='info-item'><strong>Таблиц в БД:</strong><br>" . count($tables) . "</div>";
            echo "</div>";
            echo "</div>";
            
            echo "<div class='server-info'>";
            echo "<h3>🗃 Последние 5 посещений</h3>";
            echo "<div class='info-grid'>";
            foreach ($last_visitors as $visitor) {
                echo "<div class='info-item'>";
                echo "<strong>ID: {$visitor['id']}</strong><br>";
                echo "IP: {$visitor['ip_address']}<br>";
                echo "Время: " . date('H:i:s d.m.Y', strtotime($visitor['visited_at']));
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div class='status status-offline'>❌ Ошибка подключения: " . $e->getMessage() . "</div>";
            echo "<div class='server-info'>";
            echo "<h3>🔧 Диагностика</h3>";
            echo "<p>Проверьте:</p>";
            echo "<ul>";
            echo "<li>Запущен ли PostgreSQL: <code>sudo systemctl status postgresql</code></li>";
            echo "<li>Открыт ли порт 5432: <code>sudo netstat -tlnp | grep 5432</code></li>";
            echo "<li>Существует ли пользователь baza_user: <code>sudo -u postgres psql -c '\du'</code></li>";
            echo "</ul>";
            echo "</div>";
        }
        ?>
        
        <div class="button-group" style="margin-top: 20px;">
            <a href="/" class="btn">← На главную</a>
            <a href="/pgsql-admin.php" class="btn btn-api">Admin Panel</a>
            <a href="/pgsql-python-test.py" class="btn btn-secondary">Python Test</a>
        </div>
    </div>
</body>
</html>
