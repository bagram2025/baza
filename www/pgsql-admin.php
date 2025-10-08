<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>PostgreSQL Admin - Baza Project</title>
    <link rel="stylesheet" href="/static/style.css">
    <style>
        .query-results {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            overflow-x: auto;
        }
        .sql-form {
            margin: 20px 0;
        }
        textarea {
            width: 100%;
            height: 100px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>⚙️ PostgreSQL Admin Panel</h1>
            <p class="subtitle">Управление базой данных Baza Project</p>
        </header>

        <?php
        $host = "localhost";
        $dbname = "baza_db";
        $username = "baza_user";
        $password = "your_secure_password_123";

        try {
            $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo "<div class='status status-online'>✅ Подключено к PostgreSQL</div>";
            
            // Обработка SQL запроса
            if ($_POST['sql_query'] ?? '') {
                $sql = $_POST['sql_query'];
                echo "<div class='sql-form'>";
                echo "<h3>🔍 Результат запроса:</h3>";
                echo "<div class='query-results'>";
                
                try {
                    $stmt = $pdo->query($sql);
                    
                    if ($stmt->columnCount() > 0) {
                        // SELECT запрос
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
                        echo "<tr>";
                        foreach (array_keys($results[0] ?? []) as $column) {
                            echo "<th><strong>$column</strong></th>";
                        }
                        echo "</tr>";
                        
                        foreach ($results as $row) {
                            echo "<tr>";
                            foreach ($row as $value) {
                                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                            }
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "<p><strong>Найдено строк:</strong> " . count($results) . "</p>";
                    } else {
                        // INSERT/UPDATE/DELETE запрос
                        $affected = $stmt->rowCount();
                        echo "<p><strong>Выполнено успешно!</strong> Затронуто строк: $affected</p>";
                    }
                } catch (Exception $e) {
                    echo "<div class='status status-offline'>❌ Ошибка SQL: " . $e->getMessage() . "</div>";
                }
                
                echo "</div>";
                echo "</div>";
            }
            
            // Статистика БД
            echo "<div class='server-info'>";
            echo "<h3>📊 Статистика базы данных</h3>";
            
            $stats = [
                "Размер БД" => "SELECT pg_size_pretty(pg_database_size('baza_db'))",
                "Всего таблиц" => "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public'",
                "Активные подключения" => "SELECT COUNT(*) FROM pg_stat_activity",
                "Версия PostgreSQL" => "SELECT version()"
            ];
            
            echo "<div class='info-grid'>";
            foreach ($stats as $name => $query) {
                $value = $pdo->query($query)->fetchColumn();
                echo "<div class='info-item'><strong>$name:</strong><br>$value</div>";
            }
            echo "</div>";
            echo "</div>";
            
            // Список таблиц
            $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name")->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<div class='server-info'>";
            echo "<h3>🗃 Таблицы базы данных</h3>";
            echo "<div class='info-grid'>";
            foreach ($tables as $table) {
                $size = $pdo->query("SELECT pg_size_pretty(pg_total_relation_size('public.$table'))")->fetchColumn();
                $rows = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "<div class='info-item'>";
                echo "<strong>$table</strong><br>";
                echo "Размер: $size<br>";
                echo "Записей: $rows";
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div class='status status-offline'>❌ Ошибка подключения: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <!-- SQL Query Form -->
        <div class="server-info">
            <h3>⚡ SQL Query Console</h3>
            <form method="post" class="sql-form">
                <textarea name="sql_query" placeholder="Введите SQL запрос..."><?= htmlspecialchars($_POST['sql_query'] ?? 'SELECT * FROM project_visitors LIMIT 5') ?></textarea>
                <div class="button-group">
                    <button type="submit" class="btn">Выполнить запрос</button>
                    <button type="button" onclick="document.querySelector('textarea').value='SELECT * FROM project_visitors LIMIT 5'" class="btn btn-secondary">Пример SELECT</button>
                    <button type="button" onclick="document.querySelector('textarea').value='SELECT version()'" class="btn btn-secondary">Версия PostgreSQL</button>
                </div>
            </form>
        </div>

        <div class="button-group" style="margin-top: 20px;">
            <a href="/" class="btn">← На главную</a>
            <a href="/pgsql-test.php" class="btn">PHP Test</a>
            <a href="/pgsql-python-test.py" class="btn btn-secondary">Python Test</a>
        </div>
    </div>
</body>
</html>
