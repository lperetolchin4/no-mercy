// Вспомогательные функции для проекта

/**
 * Форматирование даты
 * @param {string|Date} date - Дата для форматирования
 * @param {string} format - Формат ('ru', 'en', 'time')
 * @returns {string} Отформатированная дата
 */
export function formatDate(date, format = 'ru') {
    const d = new Date(date);
    
    if (isNaN(d.getTime())) return 'Некорректная дата';
    
    switch(format) {
        case 'ru':
            return d.toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        case 'en':
            return d.toISOString().split('T')[0];
        case 'time':
            return d.toLocaleTimeString('ru-RU', {
                hour: '2-digit',
                minute: '2-digit'
            });
        case 'full':
            return d.toLocaleDateString('ru-RU', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        default:
            return d.toLocaleDateString('ru-RU');
    }
}

/**
 * Валидация email
 * @param {string} email - Email для проверки
 * @returns {boolean} Результат валидации
 */
export function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Показать уведомление
 * @param {string} message - Текст сообщения
 * @param {string} type - Тип ('success', 'error', 'warning', 'info')
 * @param {number} duration - Длительность показа (мс)
 */
export function showNotification(message, type = 'info', duration = 3000) {
    // Создаем контейнер для уведомлений, если его нет
    let container = document.getElementById('notifications-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 300px;
        `;
        document.body.appendChild(container);
    }
    
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.style.marginBottom = '10px';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    container.appendChild(notification);
    
    // Автоматическое скрытие
    if (duration > 0) {
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
    }
    
    // Обработчик закрытия
    notification.querySelector('.btn-close').addEventListener('click', () => {
        notification.remove();
    });
}

/**
 * Дебаунс функция
 * @param {Function} func - Функция для выполнения
 * @param {number} wait - Время ожидания (мс)
 * @returns {Function} Дебаунсированная функция
 */
export function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Получение параметров из URL
 * @returns {Object} Объект с параметрами
 */
export function getUrlParams() {
    const params = {};
    const queryString = window.location.search.slice(1);
    const pairs = queryString.split('&');
    
    pairs.forEach(pair => {
        const [key, value] = pair.split('=');
        if (key) {
            params[decodeURIComponent(key)] = decodeURIComponent(value || '');
        }
    });
    
    return params;
}

/**
 * Сохранение данных в LocalStorage с проверкой
 * @param {string} key - Ключ
 * @param {any} value - Значение
 */
export function safeLocalStorageSet(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (e) {
        console.warn('LocalStorage переполнен или недоступен:', e);
        return false;
    }
}

/**
 * Получение данных из LocalStorage с проверкой
 * @param {string} key - Ключ
 * @returns {any} Значение или null
 */
export function safeLocalStorageGet(key) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : null;
    } catch (e) {
        console.warn('Ошибка чтения из LocalStorage:', e);
        return null;
    }
}

/**
 * Анимация плавного скролла
 * @param {HTMLElement|string} target - Элемент или селектор
 * @param {number} duration - Длительность анимации
 */
export function smoothScroll(target, duration = 500) {
    const element = typeof target === 'string' 
        ? document.querySelector(target) 
        : target;
    
    if (!element) return;
    
    const targetPosition = element.getBoundingClientRect().top + window.pageYOffset;
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition;
    let startTime = null;
    
    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;
        const timeElapsed = currentTime - startTime;
        const run = ease(timeElapsed, startPosition, distance, duration);
        window.scrollTo(0, run);
        if (timeElapsed < duration) {
            requestAnimationFrame(animation);
        }
    }
    
    function ease(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    }
    
    requestAnimationFrame(animation);
}

/**
 * Проверка мобильного устройства
 * @returns {boolean} true если мобильное устройство
 */
export function isMobile() {
    return window.matchMedia('(max-width: 768px)').matches;
}

/**
 * Копирование текста в буфер обмена
 * @param {string} text - Текст для копирования
 * @returns {Promise<boolean>} Результат операции
 */
export async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showNotification('Текст скопирован в буфер обмена', 'success');
        return true;
    } catch (err) {
        console.error('Ошибка копирования:', err);
        showNotification('Не удалось скопировать текст', 'error');
        return false;
    }
}