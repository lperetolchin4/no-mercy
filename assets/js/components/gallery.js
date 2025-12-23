export function initGallery() {
    const galleryGrid = document.querySelector('.gallery-grid');
    const uploadForm = document.getElementById('gallery-upload');
    
    if (!galleryGrid) return;
    
    // Массив изображений
    const images = [
        'assets/img/gallery/photo1.jpg',
        'assets/img/gallery/photo2.jpg',
        'assets/img/gallery/photo3.jpg',
        'assets/img/gallery/photo4.jpg',
        'assets/img/gallery/photo5.jpg',
        'assets/img/gallery/photo6.jpg',
        'assets/img/gallery/photo7.jpg',
        'assets/img/gallery/photo8.jpg',
        'assets/img/gallery/photo9.jpg'
    ];
    
    // Создание lightbox
    function createLightbox(imageSrc) {
        // Удаляем существующий lightbox
        const existingLightbox = document.getElementById('gallery-lightbox');
        if (existingLightbox) {
            existingLightbox.remove();
        }
        
        // Создаем новый lightbox
        const lightbox = document.createElement('div');
        lightbox.id = 'gallery-lightbox';
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <button class="lightbox-close">&times;</button>
                <button class="lightbox-prev"><</button>
                <img src="${imageSrc}" alt="Изображение галереи">
                <button class="lightbox-next">></button>
            </div>
        `;
        
        document.body.appendChild(lightbox);
        
        // Получаем текущий индекс изображения
        const currentIndex = images.indexOf(imageSrc);
        
        // Обработчики событий
        lightbox.querySelector('.lightbox-close').addEventListener('click', () => {
            lightbox.remove();
        });
        
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                lightbox.remove();
            }
        });
        
        // Навигация
        const prevBtn = lightbox.querySelector('.lightbox-prev');
        const nextBtn = lightbox.querySelector('.lightbox-next');
        
        prevBtn.addEventListener('click', () => {
            const prevIndex = (currentIndex - 1 + images.length) % images.length;
            lightbox.remove();
            createLightbox(images[prevIndex]);
        });
        
        nextBtn.addEventListener('click', () => {
            const nextIndex = (currentIndex + 1) % images.length;
            lightbox.remove();
            createLightbox(images[nextIndex]);
        });
        
        // Навигация клавишами
        document.addEventListener('keydown', function handleKeydown(e) {
            if (!document.getElementById('gallery-lightbox')) {
                document.removeEventListener('keydown', handleKeydown);
                return;
            }
            
            switch(e.key) {
                case 'Escape':
                    lightbox.remove();
                    break;
                case 'ArrowLeft':
                    prevBtn.click();
                    break;
                case 'ArrowRight':
                    nextBtn.click();
                    break;
            }
        });
    }
    
    // Рендеринг галереи
    function renderGallery() {
        galleryGrid.innerHTML = '';
        
        images.forEach((imgSrc, index) => {
            const item = document.createElement('div');
            item.className = 'gallery-item';
           // СТАЛО (без overlay):
            item.innerHTML = `
                <img src="${imgSrc}" alt="Фото клуба ${index + 1}" loading="lazy">
            `;
            
            item.addEventListener('click', () => createLightbox(imgSrc));
            galleryGrid.appendChild(item);
        });
    }
    
    // Обработчик загрузки изображений (фронтенд)
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fileInput = this.querySelector('#image-upload');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Выберите изображение');
                return;
            }
            
            // Проверка типа файла
            if (!file.type.match('image.*')) {
                alert('Выберите файл изображения');
                return;
            }
            
            // Проверка размера (макс 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Файл слишком большой (макс. 5MB)');
                return;
            }
            
            // Создаем URL для предпросмотра
            const reader = new FileReader();
            reader.onload = function(e) {
                // Добавляем изображение в начало галереи
                images.unshift(e.target.result);
                renderGallery();
                
                // Показываем сообщение
                const message = document.createElement('div');
                message.className = 'alert alert-success alert-dismissible fade show mt-3';
                message.innerHTML = `
                    Изображение успешно загружено (только для демонстрации)
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                uploadForm.appendChild(message);
                
                // Сбрасываем форму
                uploadForm.reset();
                
                // Автоудаление сообщения
                setTimeout(() => {
                    message.remove();
                }, 5000);
            };
            
            reader.readAsDataURL(file);
        });
    }
    
    // Стили для lightbox
    const lightboxStyles = document.createElement('style');
    lightboxStyles.textContent = `
        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }
        
        .lightbox-content img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 8px;
        }
        
        .lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: none;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
        }
        
        .lightbox-prev,
        .lightbox-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.7);
            border: none;
            color: white;
            font-size: 2rem;
            padding: 10px;
            cursor: pointer;
        }
        
        .lightbox-prev {
            left: -60px;
        }
        
        .lightbox-next {
            right: -60px;
        }
        
        @media (max-width: 768px) {
            .lightbox-prev {
                left: 10px;
            }
            
            .lightbox-next {
                right: 10px;
            }
            
            .lightbox-close {
                top: 10px;
                right: 10px;
            }
        }
    `;
    
    document.head.appendChild(lightboxStyles);
    
    // Инициализация
    renderGallery();
}