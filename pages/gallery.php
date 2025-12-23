<section class="py-5">
    <div class="container">
        <h1 class="fw-bold mb-4">Галерея</h1>
        
        <!-- Загрузка изображений -->
        <div class="card bg-dark border-gray-800 mb-5">
            <div class="card-body">
                <h3 class="card-title fw-bold mb-3">Добавить фото</h3>
                <p class="text-gray-400 mb-3">Загрузите изображение для демонстрации функционала (не сохраняется на сервере)</p>
                <form id="gallery-upload">
                    <div class="mb-3">
                        <label for="image-upload" class="form-label">Выберите изображение</label>
                        <input type="file" class="form-control bg-black border-gray-800 text-white" id="image-upload" accept="image/*" required>
                        <div class="form-text">Поддерживаемые форматы: JPG, PNG, GIF. Макс. размер: 5MB</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Загрузить</button>
                </form>
            </div>
        </div>
        
        <!-- Сетка галереи -->
        <h3 class="fw-bold mb-3">Фотоархив</h3>
        <p class="text-gray-400 mb-4">Кликните на изображение для увеличения</p>
        
        <div class="gallery-grid">
            <!-- Изображения будут загружены через JavaScript -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <p class="mt-3">Загрузка галереи...</p>
            </div>
        </div>
        
        <!-- Информация -->
        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="card bg-dark border-gray-800 h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Правила</h5>
                        <p class="card-text text-gray-400">Все фотографии защищены авторским правом.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-dark border-gray-800 h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Для болельщиков</h5>
                        <p class="card-text text-gray-400">Присылайте свои фотографии с матчей на email: photos@nomercity.fc.ru</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-dark border-gray-800 h-100">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">Архив</h5>
                        <p class="card-text text-gray-400">Полный фотоархив клуба с 2021 года доступен в официаьной группе VK.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>