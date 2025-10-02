<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Test - Senior Project</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        header {
            background: #2c3e50;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .server-info {
            background: #34495e;
            padding: 1rem;
            text-align: center;
            color: #ecf0f1;
            font-family: 'Courier New', monospace;
        }
        .content {
            padding: 2rem;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #3498db;
        }
        .info-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
        }
        .form-section {
            background: #e3f2fd;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        input, select, button {
            padding: 0.75rem;
            margin: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #2980b9;
        }
        .result {
            background: #d4edda;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        footer {
            background: #ecf0f1;
            padding: 1.5rem;
            text-align: center;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🐘 PHP Testовай страница для php.</h1>
            <p>Senior Project - Testing PHP Functionality</p>
        </header>
        
        <div class="server-info">
            Server: <?php echo $_SERVER['SERVER_NAME']; ?> | 
            Time: <?php echo date('Y-m-d H:i:s'); ?> | 
            PHP: <?php echo phpversion(); ?>
        </div>

        <div class="content">
            <div class="info-grid">
                <div class="info-card success">
                    <h3>✅ PHP Information</h3>
                    <p><strong>Version:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>Server API:</strong> <?php echo php_sapi_name(); ?></p>
                    <p><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
                    <p><strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s</p>
                </div>

                <div class="info-card">
                    <h3>📊 System Information</h3>
                    <p><strong>OS:</strong> <?php echo php_uname('s'); ?></p>
                    <p><strong>Host:</strong> <?php echo php_uname('n'); ?></p>
                    <p><strong>Architecture:</strong> <?php echo php_uname('m'); ?></p>
                    <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                </div>

                <div class="info-card warning">
                    <h3>🔧 Loaded Extensions</h3>
                    <p><strong>MySQL:</strong> <?php echo extension_loaded('mysql') ? '✅' : '❌'; ?></p>
                    <p><strong>PostgreSQL:</strong> <?php echo extension_loaded('pgsql') ? '✅' : '❌'; ?></p>
                    <p><strong>cURL:</strong> <?php echo extension_loaded('curl') ? '✅' : '❌'; ?></p>
                    <p><strong>GD:</strong> <?php echo extension_loaded('gd') ? '✅' : '❌'; ?></p>
                </div>
            </div>

            <!-- PHP Calculator -->
            <div class="form-section">
                <h2>🧮 PHP Calculator</h2>
                <form method="post">
                    <input type="number" name="num1" placeholder="Enter first number" step="any" required
                           value="<?php echo isset($_POST['num1']) ? htmlspecialchars($_POST['num1']) : ''; ?>">
                    
                    <select name="operation">
                        <option value="add" <?php echo (isset($_POST['operation']) && $_POST['operation'] == 'add') ? 'selected' : ''; ?>>+ Addition</option>
                        <option value="subtract" <?php echo (isset($_POST['operation']) && $_POST['operation'] == 'subtract') ? 'selected' : ''; ?>>- Subtraction</option>
                        <option value="multiply" <?php echo (isset($_POST['operation']) && $_POST['operation'] == 'multiply') ? 'selected' : ''; ?>>× Multiplication</option>
                        <option value="divide" <?php echo (isset($_POST['operation']) && $_POST['operation'] == 'divide') ? 'selected' : ''; ?>>÷ Division</option>
                    </select>
                    
                    <input type="number" name="num2" placeholder="Enter second number" step="any" required
                           value="<?php echo isset($_POST['num2']) ? htmlspecialchars($_POST['num2']) : ''; ?>">
                    
                    <button type="submit">Calculate</button>
                </form>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['num1']) && isset($_POST['num2'])) {
                    $num1 = floatval($_POST['num1']);
                    $num2 = floatval($_POST['num2']);
                    $operation = $_POST['operation'];
                    $result = '';
                    $symbol = '';
                    
                    switch ($operation) {
                        case 'add':
                            $result = $num1 + $num2;
                            $symbol = '+';
                            break;
                        case 'subtract':
                            $result = $num1 - $num2;
                            $symbol = '-';
                            break;
                        case 'multiply':
                            $result = $num1 * $num2;
                            $symbol = '×';
                            break;
                        case 'divide':
                            if ($num2 != 0) {
                                $result = $num1 / $num2;
                                $symbol = '÷';
                            } else {
                                $result = 'Error: Division by zero';
                                $symbol = '÷';
                            }
                            break;
                    }
                    
                    echo "<div class='result'>";
                    echo "<h3>Calculation Result:</h3>";
                    echo "<p>$num1 $symbol $num2 = <strong>$result</strong></p>";
                    echo "</div>";
                }
                ?>
            </div>

            <!-- Session Test -->
            <div class="form-section">
                <h2>💾 Session Test</h2>
                <?php
                session_start();
                
                if (!isset($_SESSION['visit_count'])) {
                    $_SESSION['visit_count'] = 1;
                } else {
                    $_SESSION['visit_count']++;
                }
                
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['session_data'])) {
                    $_SESSION['user_data'] = $_POST['session_data'];
                }
                ?>
                
                <p><strong>Page Visits:</strong> <?php echo $_SESSION['visit_count']; ?></p>
                
                <form method="post">
                    <input type="text" name="session_data" placeholder="Enter data to store in session" 
                           value="<?php echo isset($_SESSION['user_data']) ? htmlspecialchars($_SESSION['user_data']) : ''; ?>">
                    <button type="submit">Store in Session</button>
                </form>
                
                <?php if (isset($_SESSION['user_data'])): ?>
                    <div class="result">
                        <p><strong>Stored Session Data:</strong> <?php echo htmlspecialchars($_SESSION['user_data']); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div style="text-align: center; margin-top: 2rem;">
                <h3>🔗 Quick Links</h3>
                <p>
                    <a href="/">Main Site</a> | 
                    <a href="/api/docs">FastAPI</a> | 
                    <a href="/simple.py">Python</a> | 
                    <a href="/static/">Static Files</a>
                </p>
            </div>
        </div>

        <footer>
            <p>Senior Project • PHP Test Page • <?php echo date('Y'); ?></p>
            <p>Server: <?php echo $_SERVER['SERVER_ADDR']; ?> • Client: <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
        </footer>
    </div>
</body>
</html>
