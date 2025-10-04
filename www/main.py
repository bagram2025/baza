#!/usr/bin/env python3
print("Content-Type: text/html; charset=utf-8\n\n")
print("<html><head><title>Python Test</title>")
print("<link rel='stylesheet' href='/static/style.css'>")
print("</head><body>")
print("<div class='container'>")
print("<div class='header'><h1>🐍 Python Works!</h1></div>")
print("<div class='service-card'>")
print("<h2>✅ Python CGI is working!</h2>")
print("<p><strong>Python Version:</strong>")
import sys
print(sys.version)
print("</p><p><strong>Server Time:</strong>")
import datetime
print(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"))
print("</p><a href='/' class='btn'>← Back to main</a>")
print("</div></div></body></html>")
