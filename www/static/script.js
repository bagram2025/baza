// Функция для копирования IP-адреса
function copyIP() {
    const ip = '81.94.156.217:8080';
    navigator.clipboard.writeText(ip).then(() => {
        showNotification('IP-адрес скопирован: ' + ip);
    }).catch(err => {
        console.error('Ошибка копирования: ', err);
        showNotification('Ошибка копирования', 'error');
    });
}

// Функция для показа уведомлений
function showNotification(message, type = 'success') {
    // Создаем элемент уведомления
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'error' ? '#f44336' : '#4CAF50'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    // Добавляем стили для анимации
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    
    // Удаляем уведомление через 3 секунды
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Проверка статуса сервисов
async function checkServiceStatus() {
    const services = [
        { name: 'Основной сайт', url: '/', element: 'main-status' },
        { name: 'FastAPI API', url: '/api/docs', element: 'api-status' },
        { name: 'REST API', url: '/restapi/docs', element: 'restapi-status' },
        { name: 'Flask', url: '/flask/', element: 'flask-status' },
        { name: 'Django', url: '/django/', element: 'django-status' }
    ];

    for (const service of services) {
        try {
            const response = await fetch(service.url, { 
                method: 'HEAD',
                cache: 'no-cache'
            });
            updateServiceStatus(service.element, response.ok);
        } catch (error) {
            updateServiceStatus(service.element, false);
        }
    }
}

// Обновление статуса сервиса
function updateServiceStatus(elementId, isOnline) {
    const element = document.getElementById(elementId);
    if (element) {
        element.className = `status ${isOnline ? 'status-online' : 'status-offline'}`;
        element.title = isOnline ? 'Сервис онлайн' : 'Сервис офлайн';
    }
}

// Загрузка информации о сервере
function loadServerInfo() {
    const serverInfo = {
        ip: '81.94.156.217',
        port: '8080',
        os: 'Ubuntu',
        timestamp: new Date().toLocaleString('ru-RU')
    };
    
    // Обновляем timestamp на странице
    const timestampElement = document.getElementById('server-timestamp');
    if (timestampElement) {
        timestampElement.textContent = serverInfo.timestamp;
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Загружаем информацию о сервере
    loadServerInfo();
    
    // Проверяем статус сервисов
    checkServiceStatus();
    
    // Обновляем статус каждые 30 секунд
    setInterval(checkServiceStatus, 30000);
    
    // Добавляем обработчики для всех кнопок копирования
    document.querySelectorAll('.copy-ip').forEach(button => {
        button.addEventListener('click', copyIP);
    });
    
    // Анимация появления карточек
    animateCards();
});

// Анимация появления карточек
function animateCards() {
    const cards = document.querySelectorAll('.service-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Функция для тестирования всех сервисов
function testAllServices() {
    showNotification('Запуск проверки всех сервисов...', 'info');
    checkServiceStatus();
    setTimeout(() => {
        showNotification('Проверка сервисов завершена');
    }, 2000);
}

// Добавляем глобальные функции
window.copyIP = copyIP;
window.testAllServices = testAllServices;
