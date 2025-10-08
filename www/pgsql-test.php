<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>PostgreSQL Test</title>
    <link rel="stylesheet" href="/static/style.css">
</head>
<body>
    <div class="container">
        <h1>🐘 PostgreSQL Connection Test</h1>
        
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
            $pdo->exec("CREATE TABLE IF NOT EXISTS visitors (
                id SERIAL PRIMARY KEY,
                ip_address VARCHAR(45),
                user_agent TEXT,
                visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Запись посещения
            $stmt = $pdo->prepare("INSERT INTO visitors (ip_address, user_agent) VALUES (?, ?)");
            $stmt->execute([$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
            
            // Получение статистики
            $visitors_count = $pdo->query("SELECT COUNT(*) FROM visitors")->fetchColumn();
            $last_visitors = $pdo->query("SELECT * FROM visitors ORDER BY visited_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>📊 Статистика посещений:</h3>";
            echo "<p>Всего посещений: <strong>$visitors_count</strong></p>";
            
            echo "<h4>Последние 5 посещений:</h4>";
            echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>IP</th><th>Время</th></tr>";
            foreach ($last_visitors as $visitor) {
                echo "<tr>";
                echo "<td>{$visitor['id']}</td>";
                echo "<td>{$visitor['ip_address']}</td>";
                echo "<td>{$visitor['visited_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Информация о БД
            $db_size = $pdo->query("SELECT pg_size_pretty(pg_database_size('baza_db'))")->fetchColumn();
            $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<h3>🗃 Информация о базе данных:</h3>";
            echo "<p>Размер БД: <strong>$db_size</strong></p>";
            echo "<p>Таблицы: <strong>" . implode(', ', $tables) . "</strong></p>";
            
        } catch (PDOException $e) {
            echo "<div class='status status-offline'>❌ Ошибка подключения: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <div style="margin-top: 20px;">
            <a href="/" class="btn">← На главную</a>
            <a href="/pgsql-admin.php" class="btn btn-api">Admin Panel</a>
        </div>
    </div>
</body>
</html>
