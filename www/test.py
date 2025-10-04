#!/usr/bin/env python3
import cgi
import datetime

print("Content-Type: text/html\n")
print("""
<!DOCTYPE html>
<html>
<head>
    <title>Python CGI Test</title>
    <link rel="stylesheet" href="/static/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐍 Python CGI Test</h1>
            <p>Python through CGI interface</p>
        </div>
        
        <div class="service-card">
            <h2>Server Information</h2>
            <p><strong>Python Version:</strong> {} </p>
            <p><strong>Current Time:</strong> {} </p>
            <p><strong>Server Software:</strong> {} </p>
            
            <h3>Environment Variables:</h3>
            <pre>""".format(
    import sys; sys.version.split()[0],
    datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
    import os; os.environ.get('SERVER_SOFTWARE', 'N/A')
))

import os
for key in sorted(os.environ.keys()):
    if key.startswith(('HTTP_', 'SERVER_', 'REQUEST_')):
        print(f"{key}: {os.environ[key]}")

print("""
            </pre>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="/" class="btn">← На главную</a>
            </div>
        </div>
    </div>
</body>
</html>
""")
