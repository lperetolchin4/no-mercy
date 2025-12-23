export function initCountdown() {
    const countdownElement = document.getElementById('countdown');
    if (!countdownElement) return;
    
    // Установите дату вашего предстоящего матча
    const matchDate = new Date('2025-12-27T22:00:00').getTime(); // 27 декабря 2024, 22:00
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = matchDate - now;
        
        if (distance < 0) {
            // Если матч уже прошел, показываем результат
            countdownElement.innerHTML = `
                <div class="text-center">
                    <h4 class="text-success fw-bold">Матч завершен!</h4>
                    <p class="text-gray-400">Следите за анонсами следующего матча</p>
                </div>
            `;
            return;
        }
        
        // Рассчитываем время до матча
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Отображаем обратный отсчет
        countdownElement.innerHTML = `
            <div class="d-flex justify-content-center gap-3">
                <div class="text-center">
                    <div class="fs-1 fw-bold">${days}</div>
                    <div class="text-gray-400">дней</div>
                </div>
                <div class="text-center">
                    <div class="fs-1 fw-bold">${hours}</div>
                    <div class="text-gray-400">часов</div>
                </div>
                <div class="text-center">
                    <div class="fs-1 fw-bold">${minutes}</div>
                    <div class="text-gray-400">минут</div>
                </div>
                <div class="text-center">
                    <div class="fs-1 fw-bold">${seconds}</div>
                    <div class="text-gray-400">секунд</div>
                </div>
            </div>
        `;
    }
    
    // Сразу обновляем и запускаем таймер
    updateCountdown();
    setInterval(updateCountdown, 1000);
}