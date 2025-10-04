<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Info Dashboard</title>
    <link rel="stylesheet" href="/static/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            color: #667eea;
            margin: 5px 0;
        }
        .code-block {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            margin: 10px 0;
            max-height: 150px;
            overflow-y: auto;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐘 PHP Dashboard</h1>
            <p>Системная информация и диагностика</p>
        </div>

        <!-- Основные метрики -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📊 Память</h3>
                <div class="stat-value">
                    <?php echo round(memory_get_usage(true) / 1024 / 1024, 1); ?> MB
                </div>
                <small>Пик: <?php echo round(memory_get_peak_usage(true) / 1024 / 1024, 1); ?> MB</small>
            </div>

            <div class="stat-card">
                <h3>💾 Диск</h3>
                <div class="stat-value">
                    <?php echo round(disk_free_space("/") / 1024 / 1024 / 1024, 1); ?> GB
                </div>
                <small>Свободно</small>
            </div>

            <div class="stat-card">
                <h3>⚡ PHP</h3>
                <div class="stat-value">
                    <?php echo phpversion(); ?>
                </div>
                <small>Версия</small>
            </div>

            <div class="stat-card">
                <h3>👤 Пользователь</h3>
                <div class="stat-value">
                    <?php echo get_current_user(); ?>
                </div>
                <small>UID: <?php echo getmyuid(); ?></small>
            </div>
        </div>

        <div class="service-card">
            <h3>🛠️ Быстрые проверки</h3>
            
            <div class="btn-group">
                <button onclick="pingGoogle()" class="btn">Ping Google</button>
                <button onclick="checkPort()" class="btn btn-secondary">Проверить порт 8080</button>
                <button onclick="listFiles()" class="btn btn-api">Список файлов</button>
            </div>

            <div id="result" class="code-block">
                Нажмите кнопку для проверки...
            </div>
        </div>

        <div class="service-card">
            <h3>📋 Системная информация</h3>
            
            <p><strong>ОС:</strong> <?php echo php_uname('s'); ?> <?php echo php_uname('r'); ?></p>
            <p><strong>Время сервера:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Документ рут:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
            <p><strong>Загруженные модули:</strong> <?php echo count(get_loaded_extensions()); ?> шт</p>
            
            <details>
                <summary>Показать все модули PHP</summary>
                <div class="code-block">
                    <?php echo implode(', ', get_loaded_extensions()); ?>
                </div>
            </details>
        </div>

        <div class="btn-group">
            <a href="/" class="btn">← На главную</a>
            <a href="/info.php" class="btn btn-secondary">Полная PHP информация</a>
            <a href="/quick-check.php" class="btn btn-api">JSON API</a>
        </div>
    </div>

    <script>
        function pingGoogle() {
            showLoading('Выполняю ping google.com...');
            fetch('?action=ping&host=google.com')
                .then(r => r.text())
                .then(html => {
                    document.getElementById('result').innerHTML = html;
                });
        }

        function checkPort() {
            showLoading('Проверяю порт 8080...');
            fetch('?action=check_port&host=localhost&port=8080')
                .then(r => r.text())
                .then(html => {
                    document.getElementById('result').innerHTML = html;
                });
        }

        function listFiles() {
            showLoading('Получаю список файлов...');
            fetch('?action=list_files')
                .then(r => r.text())
                .then(html => {
                    document.getElementById('result').innerHTML = html;
                });
        }

        function showLoading(msg) {
            document.getElementById('result').innerHTML = '<em>' + msg + '</em>';
        }
    </script>

    <?php
    // Обработка AJAX запросов
    if(isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'ping':
                $host = $_GET['host'] ?? 'google.com';
                $output = [];
                exec("ping -c 2 " . escapeshellarg($host), $output);
                echo implode("\n", $output);
                exit;
                
            case 'check_port':
                $host = $_GET['host'] ?? 'localhost';
                $port = $_GET['port'] ?? 8080;
                $fp = @fsockopen($host, $port, $errno, $errstr, 2);
                if($fp) {
                    echo "✅ Порт $port на $host ОТКРЫТ";
                    fclose($fp);
                } else {
                    echo "❌ Порт $port на $host ЗАКРЫТ: $errstr";
                }
                exit;
                
            case 'list_files':
                $files = array_diff(scandir('.'), ['.', '..']);
                foreach($files as $file) {
                    $size = is_dir($file) ? 'DIR' : round(filesize($file) / 1024, 1) + ' KB';
                    echo "$file ($size)\n";
                }
                exit;
        }
    }
    ?>
</body>
</html>
