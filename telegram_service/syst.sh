sudo tee /etc/systemd/system/telegram-service.service > /dev/null <<EOF
[Unit]
Description=Telegram Notification Service
After=network.target

[Service]
Type=simple
User=ubuntuuser
Group=ubuntuuser
WorkingDirectory=/home/ubuntuuser/baza/telegram_service
Environment=TELEGRAM_BOT_TOKEN=6642548401:AAG1df9IFhBijkNYLSVWUPe2FfodsxMOIUg
Environment=DEFAULT_CHAT_ID=6905230450
ExecStart=/usr/bin/python3 /home/ubuntuuser/baza/telegram_service/main.py
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
EOF
