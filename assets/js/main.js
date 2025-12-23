// Импорт модулей
import { initCountdown } from './components/countdown.js';
import { initTeamFilter } from './components/team-filter.js';
import { initSchedule } from './components/schedule.js';
import { initGallery } from './components/gallery.js';

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    // Инициализация компонентов в зависимости от страницы
    if (document.querySelector('.match-countdown')) {
        initCountdown();
    }
    
    if (document.querySelector('.player-filters')) {
        initTeamFilter();
    }
    
    if (document.querySelector('#schedule-table')) {
        initSchedule();
    }
    
    if (document.querySelector('.gallery-grid')) {
        initGallery();
    }
    
    // Общие функции
    console.log('Сайт No MERCY FC загружен');
});