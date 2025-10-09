sudo tee /etc/systemd/system/telegram-service.service > /dev/null <<EOF
[Unit]
Description=Telegram Notification Service
After=network.target

[Service]
Type=simple
User=ubuntuuser
Group=ubuntuuser
WorkingDirectory=/home/ubuntuuser/baza/telegram_service
Environment=PATH=/home/ubuntuuser/baza/telegram_service/venv/bin:/usr/bin:/usr/local/bin
ExecStart=/home/ubuntuuser/baza/telegram_service/venv/bin/python3 /home/ubuntuuser/baza/telegram_service/main.py
Restart=always
RestartSec=5
Environment=PYTHONUNBUFFERED=1

[Install]
WantedBy=multi-user.target
EOF
