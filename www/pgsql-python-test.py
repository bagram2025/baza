#!/usr/bin/env python3
import psycopg2
import json
from datetime import datetime

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
    
    result = {
        "status": "success",
        "database": {
            "name": db_name,
            "version": db_version,
            "tables_count": table_count,
            "size": db_size
        },
        "timestamp": datetime.now().isoformat()
    }
    
    cursor.close()
    conn.close()
    
except Exception as e:
    result = {
        "status": "error",
        "message": str(e),
        "timestamp": datetime.now().isoformat()
    }

print(json.dumps(result, indent=2, ensure_ascii=False))
