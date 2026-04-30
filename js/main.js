const menuData = {
    "clothes": {
        links: [
            { text: "Платья", url: "category.php?id=2" },
            { text: "Брюки", url: "category.php?id=3" },
            { text: "Свитеры и толстовки", url: "category.php?id=4" },
            { text: "Жакеты и жилеты", url: "category.php?id=5" },
            { text: "Юбки", url: "category.php?id=6" },
            { text: "Деним", url: "category.php?id=7" },
            { text: "Верхняя одежда", url: "category.php?id=8" }
        ],
        images: [
            { src: "src/4foto.jpg", title: "ПРИЗРАЧНАЯ КРАСОТА" },
            { src: "src/5foto.jpg", title: "ТИХАЯ МУЗА" },
            { src: "src/6foto.jpg", title: "NEW COLLECTION" }
        ]
    },
    //"new": {
    //    links: [{ text: "КАТАЛОГ", url: "category.php?id=1" }],
    //   images: [
    //        { src: "src/4foto.jpg", title: "ПРИЗРАЧНАЯ КРАСОТА" },
    //        { src: "src/5foto.jpg", title: "ТИХАЯ МУЗА" }
    //    ]
    //},
    "accs": {
        links: [
            { text: "Сумки", url: "category.php?id=9" },
            { text: "Ремни", url: "category.php?id=10" },
            { text: "Украшения", url: "category.php?id=11" },
            { text: "Очки", url: "category.php?id=12" }
        ],
        images: [
            { src: "src/a1foto.jpg", title: "ACC 1" },
            { src: "src/a2foto.jpg", title: "ACC 2" },
            { src: "src/a3foto.jpg", title: "ACC 3" }
        ]
    }
};

// Мега-меню логика (без изменений)
const megaMenu = document.getElementById('mega-menu');
const linksContainer = document.getElementById('menu-links');
const imagesContainer = document.getElementById('menu-previews');
const menuItems = document.querySelectorAll('.menu-item');

menuItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
        const type = this.getAttribute('data-type');
        if (menuData[type]) {
            linksContainer.innerHTML = menuData[type].links.map(l => {
                return typeof l === 'object' 
                    ? `<a href="${l.url}">${l.text}</a>` 
                    : `<a href="#">${l}</a>`;
            }).join('');
            
            imagesContainer.innerHTML = menuData[type].images.map(img => `
                <div class="menu-card">
                    <img src="${img.src}" alt="img">
                    ${img.title ? `<span>${img.title}</span>` : ''}
                </div>
            `).join('');
            megaMenu.classList.add('show');
        } else {
            megaMenu.classList.remove('show');
        }
    });
});

document.querySelector('.navbar').addEventListener('mouseleave', (e) => {
    if (megaMenu && !megaMenu.contains(e.relatedTarget)) {
        megaMenu.classList.remove('show');
    }
});

megaMenu.addEventListener('mouseleave', () => {
    megaMenu.classList.remove('show');
});

// ФУНКЦИЯ ПЕРЕКЛЮЧЕНИЯ ТАБОВ (Вход/Регистрация)
function switchAuth(mode) {
    const loginForm = document.getElementById('login-form');
    const regForm = document.getElementById('reg-form');
    const tabs = document.querySelectorAll('.auth-tab');

    if (mode === 'login') {
        loginForm.style.display = 'block';
        regForm.style.display = 'none';
        tabs[0].classList.add('active');
        tabs[1].classList.remove('active');
    } else {
        loginForm.style.display = 'none';
        regForm.style.display = 'block';
        tabs[0].classList.remove('active');
        tabs[1].classList.add('active');
    }
}

// ГЛАВНАЯ ФУНКЦИЯ САЙДБАРА
function openSidebar(type) {
    const content = document.getElementById('sidebar-content');
    const sidebar = document.getElementById('sidebar-container');
    const overlay = document.getElementById('overlay');

    if (type === 'favorites' || type === 'heart') {
        content.innerHTML = '<p>Войдите или зарегистрируйтесь для просмотра избранного</p>';
        loadFavorites(); 
    }
    else if (type === 'profile') {
        if (typeof userIsLogged !== 'undefined' && userIsLogged) {
            // Проверяем, является ли пользователь админом
            const adminLink = (typeof userRole !== 'undefined' && userRole === 'admin') 
                ? `<li><a href="admin.php" style="display:block; padding:15px 0; border-bottom:1px solid #eee; text-decoration:none; color:red; font-weight:bold;">АДМИН-ПАНЕЛЬ</a></li>` 
                : '';

            content.innerHTML = `
                <div class="sidebar-header-simple" style="margin-bottom: 20px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase;">
                    Аккаунт
                </div>
                <div class="profile-menu">
                    <ul style="list-style:none; padding:0; margin:0;">
                        ${adminLink} 
                        <li>
                            <a href="profile.php" style="display:flex; justify-content: space-between; align-items: center; padding: 18px 0; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: #000; font-size: 13px; transition: 0.3s;">
                                ДАННЫЕ АККАУНТА <span>→</span>
                            </a>
                        </li>
                        <li>
                            <a href="auth/logout.php" style="display:block; padding: 25px 0; text-decoration: none; color: #ff3b30; font-size: 12px; font-weight: bold; letter-spacing: 1px;">
                                ВЫЙТИ
                            </a>
                        </li>
                    </ul>
                </div>`;        } else {
            // Твой старый код формы логина/регистрации (без изменений)
            content.innerHTML = `
                <div class="sidebar-auth-choice">
                    <button class="auth-tab active" onclick="switchAuth('login')">Авторизация</button>
                    <button class="auth-tab" onclick="switchAuth('reg')">Регистрация</button>
                </div>
                <form id="login-form" action="auth/login.php" method="POST" class="auth-form">
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label>Пароль</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="auth-submit-btn">ВОЙТИ</button>
                </form>
                <form id="reg-form" action="auth/register.php" method="POST" class="auth-form" style="display:none;">
                    <div class="input-group">
                        <label>ФИО</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="input-group">
                        <label>Пароль</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="auth-submit-btn">ЗАРЕГИСТРИРОВАТЬСЯ</button>
                </form>`;
        }
    }
    else if (type === 'basket') {
        content.innerHTML = '<p>Войдите или зарегистрируйтесь для просмотра корзины</p>';
        loadCart();
    } else if (type === 'profile') {
        // Твоя логика профиля (вход/регистрация)
    }
    else if (type === 'search') {
        content.innerHTML = `
            <div class="sidebar-header-simple" style="font-weight: bold; margin-bottom: 10px;">Поиск</div>
            <div style="padding: 10px 0;">
                <input type="text" id="main-search" class="search-input" placeholder="Начните вводить..." 
                       style="width:100%; padding:10px; border:none; border-bottom:1px solid #000; outline:none;">
            </div>
            <div id="search-results" style="margin-top: 20px;"></div>`;
        
        const searchInput = document.getElementById('main-search');
        
        // Автофокус
        setTimeout(() => searchInput.focus(), 100);

        // Слушаем ввод текста
        searchInput.addEventListener('input', async (e) => {
            const query = e.target.value.trim();
            const resultsContainer = document.getElementById('search-results');

            if (query.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }

            try {
                const response = await fetch(`auth/search.php?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.status === 'success') {
                    if (data.items.length === 0) {
                        resultsContainer.innerHTML = '<p style="color: gray; font-size: 13px;">Ничего не найдено</p>';
                    } else {
                        resultsContainer.innerHTML = data.items.map(item => `
                            <a href="item.php?id=${item.id}" style="display: flex; gap: 15px; margin-bottom: 15px; text-decoration: none; color: #000; align-items: center;">
                                <img src="src/${item.main_image}" style="width: 50px; height: 60px; object-fit: cover;">
                                <div>
                                    <div style="font-size: 12px; text-transform: uppercase;">${item.name}</div>
                                    <div style="font-weight: bold; font-size: 13px;">${Number(item.price).toLocaleString()} ₸</div>
                                </div>
                            </a>
                        `).join('');
                    }
                }
            } catch (err) {
                console.error("Ошибка поиска:", err);
            }
        });
    }

    sidebar.classList.add('active');
    overlay.classList.add('active');
}

function closeSidebar() {
    const container = document.getElementById('sidebar-container');
    const overlay = document.getElementById('overlay');
    if (container) container.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
}

document.addEventListener('DOMContentLoaded', () => {
    const closeBtn = document.getElementById('close-sidebar');
    const overlay = document.getElementById('overlay');

    if (closeBtn) closeBtn.onclick = closeSidebar;
    if (overlay) overlay.onclick = closeSidebar;

    // Закрытие по ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === "Escape") closeSidebar();
    });
});

// Функция для добавления товара (вызывать на кнопках в item.php или new.php)
async function toggleAction(productId, actionType, btn) {
    if (!userIsLogged) {
        openSidebar('profile');
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', actionType);

    try {
        const response = await fetch('auth/action_handler.php', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.status === 'success') {
            if (actionType === 'favorite') {
                const img = btn.querySelector('img');
                
                // Проверяем значение из ключа 'action'
                if (result.action === 'added') {
                    img.src = 'src/heart-filled.png';
                    img.style.filter = 'none'; // Убираем черный фильтр
                } else if (result.action === 'removed') {
                    img.src = 'src/heart.png';
                    img.style.filter = 'brightness(0)'; // Возвращаем черный фильтр
                }
            }
            
            if (actionType === 'cart') {
                alert("Товар добавлен в корзину!");
            }
        }
    } catch (error) {
        console.error('Ошибка:', error);
    }
}
async function addToCart(productId) {
    // Проверяем переменную, которую мы передали из PHP в Head
    if (!userIsLogged) {
        alert("Войдите в аккаунт, чтобы добавить товар в корзину");
        openSidebar('profile');
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);

    try {
        const response = await fetch('auth/add_to_cart.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            alert("Товар добавлен в корзину!");
            // Тут можно вызвать отрисовку корзины в сайдбаре, если она готова
        } else {
            alert("Ошибка: " + result.message);
        }
    } catch (error) {
        console.error("Ошибка запроса:", error);
    }
}
// Функция загрузки корзины с кнопкой удаления и контейнером для скролла
// Функция отрисовки корзины со скроллом и кнопкой
// main.js

// 1. Отрисовка корзины со скроллом
async function loadCart() {
    const content = document.getElementById('sidebar-content');
    
    try {
        const response = await fetch('auth/get_cart.php');
        const data = await response.json();

        if (data.status === 'success') {
            if (!data.items || data.items.length === 0) {
                content.innerHTML = '<h3>Корзина</h3><p style="padding:20px;">Ваша корзина пуста</p>';
                return;
            }

            // Добавляем контейнер со скроллом (max-height: 400px)
            let html = `
                <h3>Корзина</h3>
                <div id="cart-scroll-container" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
            `;

            data.items.forEach(item => {
                html += `
                    <div class="cart-item" style="display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <img src="src/${item.main_image}" style="width: 60px; height: 80px; object-fit: cover;">
                        <div style="flex: 1;">
                            <div style="font-size: 12px; font-weight: bold; text-transform: uppercase;">${item.name}</div>
                            <div style="font-size: 14px;">${Number(item.price).toLocaleString()} ₸</div>
                            <button onclick="removeFromCart(${item.id})" 
                                    style="background:none; border:none; color:red; cursor:pointer; font-size:10px; padding:0; text-decoration:underline;">
                                УДАЛИТЬ
                            </button>
                        </div>
                    </div>
                `;
            });

            html += `</div>`; // Закрываем скролл-контейнер
            
            html += `
                <div style="margin-top: 20px; border-top: 2px solid #000; padding-top: 15px;">
                    <div style="display: flex; justify-content: space-between; font-weight: bold;">
                        <span>ИТОГО:</span>
                        <span>${data.totalPrice.toLocaleString()} ₸</span>
                    </div>
                    <button class="add-btn" onclick="checkout()">ОФОРМИТЬ ЗАКАЗ</button>
                </div>
            `;
            content.innerHTML = html;
        }
    } catch (e) {
        console.error("Ошибка корзины:", e);
    }
}

// 2. Функция удаления
async function removeFromCart(productId) {
    // Окно подтверждения у нас уже есть

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', 'remove_cart'); // Строгое соответствие с PHP

    try {
        const response = await fetch('auth/action_handler.php', {
            method: 'POST',
            body: formData
        });
        
        const text = await response.text(); // Сначала получаем текст для отладки
        console.log("Сырой ответ сервера:", text);
        
        const result = JSON.parse(text);

        if (result.status === 'success') {
            loadCart(); // Перерисовываем корзину
        } else {
            alert("Ошибка: " + result.message);
        }
    } catch (e) {
        console.error("Критическая ошибка JS:", e);
    }
}
async function loadFavorites() {
    const content = document.getElementById('sidebar-content');
    
    try {
        const response = await fetch('auth/get_favorites.php');
        const data = await response.json();

        if (data.status === 'success') {
            if (data.items.length === 0) {
                content.innerHTML = '<h3>Избранное</h3><p style="padding:20px;">Ваш список пуст</p>';
                return;
            }

            let html = '<h3>Избранное</h3><div class="cart-items" style="padding:20px;">';
            data.items.forEach(item => {
                html += `
                    <div class="cart-item" style="display:flex; gap:15px; margin-bottom:15px; align-items:center;">
                        <img src="src/${item.main_image}" style="width:60px; height:80px; object-fit:cover;">
                        <div>
                            <div style="font-size:12px; text-transform:uppercase;">${item.name}</div>
                            <div style="font-weight:bold;">${Number(item.price).toLocaleString()} ₸</div>
                            <a href="item.php?id=${item.id}" style="font-size:10px; color:gray;">Посмотреть</a>
                        </div>
                    </div>
                `;
            });
            content.innerHTML = html + '</div>';
        }
    } catch (e) {
        content.innerHTML = '<h3>Избранное</h3><p>Ошибка загрузки</p>';
    }
}

// Обновленная функция переключения
async function toggleWishlist(productId, btn) {
    if (!userIsLogged) { openSidebar('profile'); return; }
    
    const formData = new FormData();
    formData.append('product_id', productId);

    const response = await fetch('auth/add_to_wishlist.php', { method: 'POST', body: formData });
    const result = await response.json();

    if (result.status === 'success') {
        const img = btn.querySelector('img');
        if (result.action === 'added') {
            img.src = 'src/heart-filled.png'; // Замени на свое имя файла
            btn.classList.add('active');
        } else {
            img.src = 'src/heart.png'; // Пустое сердце
            btn.classList.remove('active');
        }
    }
}
function deleteProduct(productId) {
    if (confirm('Вы уверены, что хотите удалить этот товар?')) {
        fetch('auth/delete_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + productId
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                location.reload(); // Перезагружаем страницу, чтобы товар исчез
            } else {
                alert('Ошибка при удалении: ' + data);
            }
        });
    }
}
function checkout() {
    window.location.href = 'checkout.php';
}