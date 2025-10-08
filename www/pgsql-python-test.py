#!/usr/bin/env python3
import psycopg2
import json
from datetime import datetime
import os

print("Content-Type: application/json\n")

try:
    conn = psycopg2.connect(
        host="localhost",
        database="baza_db",
        user="baza_user",
        password="your_secure_password_123"
    )
    
    cursor = conn.cursor()
    
    # Получаем информацию о БД
    cursor.execute("SELECT version()")
    db_version = cursor.fetchone()[0]
    
    cursor.execute("SELECT current_database()")
    db_name = cursor.fetchone()[0]
    
    cursor.execute("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public'")
    table_count = cursor.fetchone()[0]
    
    cursor.execute("SELECT pg_size_pretty(pg_database_size('baza_db'))")
    db_size = cursor.fetchone()[0]
    
    # Активные подключения
    cursor.execute("SELECT COUNT(*) FROM pg_stat_activity")
    active_connections = cursor.fetchone()[0]
    
    # Список таблиц
    cursor.execute("""
        SELECT table_name, 
               pg_size_pretty(pg_total_relation_size('public.' || table_name)) as size
        FROM information_schema.tables 
        WHERE table_schema = 'public'
        ORDER BY table_name
    """)
    tables = cursor.fetchall()
    
    result = {
        "status": "success",
        "database": {
            "name": db_name,
            "version": db_version.split(',')[0],
            "tables_count": table_count,
            "size": db_size,
            "active_connections": active_connections
        },
        "tables": [{"name": table[0], "size": table[1]} for table in tables],
        "timestamp": datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
        "project": "Baza Project"
    }
    
    cursor.close()
    conn.close()
    
except Exception as e:
    result = {
        "status": "error",
        "message": str(e),
        "timestamp": datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
        "project": "Baza Project"
    }

print(json.dumps(result, indent=2, ensure_ascii=False))
