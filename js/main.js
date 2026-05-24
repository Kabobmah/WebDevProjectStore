// Я лох

// 1. СЛОВАРЬ ПЕРЕВОДОВ
const translations = {
    ru: {
        clothes: "Одежда",
        accs: "Аксессуары",
        emptyCart: "Ваша корзина пуста",
        emptyFav: "Ваш список пуст",
        loading: "Войдите или зарегистрируйтесь для просмотра ",
        account: "Аккаунт",
        admin: "АДМИН-ПАНЕЛЬ",
        profileData: "ДАННЫЕ АККАУНТА",
        logout: "ВЫЙТИ",
        auth: "Авторизация",
        reg: "Регистрация",
        loginBtn: "ВОЙТИ",
        regBtn: "ЗАРЕГИСТРИРОВАТЬСЯ",
        search: "Поиск",
        placeholder: "Начните вводить...",
        notFound: "Ничего не найдено",
        total: "ИТОГО",
        checkout: "ОФОРМИТЬ ЗАКАЗ",
        delete: "УДАЛИТЬ",
        view: "View",
        addedToCart: "Товар добавлен в корзину!",
        confirmDelete: "Вы уверены, что хотите удалить этот товар?",
        loginRequired: "Войдите в аккаунт, чтобы добавить товар в корзину",
        email: "Email",
        password: "Пароль",
        fio: "ФИО",
        cartTitle: "КОРЗИНА",
        favTitle: "FAVORITES"
    },
    en: {
        clothes: "Clothes",
        accs: "Accessories",
        emptyCart: "Your cart is empty",
        emptyFav: "Your list is empty",
        loading: "Login or register to view",
        account: "Account",
        admin: "ADMIN PANEL",
        profileData: "ACCOUNT DATA",
        logout: "LOGOUT",
        auth: "Login",
        reg: "Registration",
        loginBtn: "LOGIN",
        regBtn: "REGISTER",
        search: "Search",
        placeholder: "Start typing...",
        notFound: "Nothing found",
        total: "TOTAL",
        checkout: "CHECKOUT",
        delete: "DELETE",
        view: "View",
        addedToCart: "Added to cart!",
        confirmDelete: "Are you sure you want to delete this item?",
        loginRequired: "Please login to add items to cart",
        email: "Email",
        password: "Password",
        fio: "Full Name",
        cartTitle: "CART",
        favTitle: "FAVORITES"
    }
};

const curLang = (document.documentElement.lang === 'en' || window.location.search.includes('lang=en')) ? 'en' : 'ru';
const t = translations[curLang];

function getProdName(item) {
    if (curLang === 'en' && item.name_en) {
        return item.name_en; 
    }
    return item.name; 
}

const menuData = {
    "clothes": {
        links: [
            { text: curLang === 'en' ? "Dresses" : "Платья", url: "category.php?id=2" },
            { text: curLang === 'en' ? "Pants" : "Брюки", url: "category.php?id=3" },
            { text: curLang === 'en' ? "Sweaters" : "Свитеры и толстовки", url: "category.php?id=4" },
            { text: curLang === 'en' ? "Jackets" : "Жакеты и жилеты", url: "category.php?id=5" },
            { text: curLang === 'en' ? "Skirts" : "Юбки", url: "category.php?id=6" },
            { text: curLang === 'en' ? "Denim" : "Деним", url: "category.php?id=7" },
            { text: curLang === 'en' ? "Outerwear" : "Верхняя одежда", url: "category.php?id=8" }
        ],
        images: [
            { src: "src/4foto.jpg", title: curLang === 'en' ? "GHOSTLY BEAUTY" : "ПРИЗРАЧНАЯ КРАСОТА" },
            { src: "src/5foto.jpg", title: curLang === 'en' ? "SILENT MUSE" : "ТИХАЯ МУЗА" },
            { src: "src/6foto.jpg", title: "NEW COLLECTION" }
        ]
    },
    "accs": {
        links: [
            { text: curLang === 'en' ? "Bags" : "Сумки", url: "category.php?id=9" },
            { text: curLang === 'en' ? "Belts" : "Ремни", url: "category.php?id=10" },
            { text: curLang === 'en' ? "Jewelry" : "Украшения", url: "category.php?id=11" },
            { text: curLang === 'en' ? "Glasses" : "Очки", url: "category.php?id=12" }
        ],
        images: [
            { src: "src/a1foto.jpg", title: "ACC 1" },
            { src: "src/a2foto.jpg", title: "ACC 2" },
            { src: "src/a3foto.jpg", title: "ACC 3" }
        ]
    }
};

const megaMenu = document.getElementById('mega-menu');
const linksContainer = document.getElementById('menu-links');
const imagesContainer = document.getElementById('menu-previews');
const menuItems = document.querySelectorAll('.menu-item');

menuItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
        const type = this.getAttribute('data-type');
        if (menuData[type]) {
            linksContainer.innerHTML = menuData[type].links.map(l => `<a href="${l.url}">${l.text}</a>`).join('');
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
    if (megaMenu && !megaMenu.contains(e.relatedTarget)) megaMenu.classList.remove('show');
});

if (megaMenu) {
    megaMenu.addEventListener('mouseleave', () => megaMenu.classList.remove('show'));
}

function openSidebar(type) {
    const content = document.getElementById('sidebar-content');
    const sidebar = document.getElementById('sidebar-container');
    const overlay = document.getElementById('overlay');

    if (type === 'favorites' || type === 'heart') {
        content.innerHTML = `<p>${t.loading}</p>`;
        loadFavorites(); 
    }
    else if (type === 'profile') {
        if (typeof userIsLogged !== 'undefined' && userIsLogged) {
            const adminLink = (typeof userRole !== 'undefined' && userRole === 'admin') 
                ? `<li><a href="admin.php" style="display:block; padding:15px 0; border-bottom:1px solid #eee; text-decoration:none; color:red; font-weight:bold;">${t.admin}</a></li>` 
                : '';
            content.innerHTML = `
                <div class="sidebar-header-simple" style="margin-bottom: 20px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase;">${t.account}</div>
                <div class="profile-menu"><ul style="list-style:none; padding:0; margin:0;">
                    ${adminLink} 
                    <li><a href="profile.php" style="display:flex; justify-content: space-between; align-items: center; padding: 18px 0; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: #000; font-size: 13px;">${t.profileData} <span>→</span></a></li>
                    <li><a href="auth/logout.php" style="display:block; padding: 25px 0; text-decoration: none; color: #ff3b30; font-size: 12px; font-weight: bold;">${t.logout}</a></li>
                </ul></div>`;
        } else {
            content.innerHTML = `
                <div class="sidebar-auth-choice">
                    <button class="auth-tab active" onclick="switchAuth('login')">${t.auth}</button>
                    <button class="auth-tab" onclick="switchAuth('reg')">${t.reg}</button>
                </div>
                <form id="login-form" action="auth/login.php" method="POST" class="auth-form">
                    <div class="input-group"><label>${t.email}</label><input type="email" name="email" required></div>
                    <div class="input-group"><label>${t.password}</label><input type="password" name="password" required></div>
                    <button type="submit" class="auth-submit-btn">${t.loginBtn}</button>
                </form>
                <form id="reg-form" action="auth/register.php" method="POST" class="auth-form" style="display:none;">
                    <div class="input-group"><label>${t.fio}</label><input type="text" name="full_name" required></div>
                    <div class="input-group"><label>${t.email}</label><input type="email" name="email" required></div>
                    <div class="input-group"><label>${t.password}</label><input type="password" name="password" required></div>
                    <button type="submit" class="auth-submit-btn">${t.regBtn}</button>
                </form>`;
        }
    }
    else if (type === 'basket') {
        content.innerHTML = `<p>${t.loading}</p>`;
        loadCart();
    }
    else if (type === 'search') {
        content.innerHTML = `
            <div class="sidebar-header-simple" style="font-weight: bold; margin-bottom: 10px;">${t.search}</div>
            <div style="padding: 10px 0;">
                <input type="text" id="main-search" class="search-input" placeholder="${t.placeholder}" style="width:100%; padding:10px; border:none; border-bottom:1px solid #000; outline:none;">
            </div>
            <div id="search-results" style="margin-top: 20px;"></div>`;
        const searchInput = document.getElementById('main-search');
        setTimeout(() => searchInput.focus(), 100);
        searchInput.addEventListener('input', async (e) => {
            const query = e.target.value.trim();
            const resultsContainer = document.getElementById('search-results');
            if (query.length < 2) { resultsContainer.innerHTML = ''; return; }
            try {
                const response = await fetch(`auth/search.php?q=${encodeURIComponent(query)}`);
                const data = await response.json();
                if (data.status === 'success') {
                    if (data.items.length === 0) { resultsContainer.innerHTML = `<p style="color: gray; font-size: 13px;">${t.notFound}</p>`; }
                    else {
                        resultsContainer.innerHTML = data.items.map(item => `
                            <a href="item.php?id=${item.id}" style="display: flex; gap: 15px; margin-bottom: 15px; text-decoration: none; color: #000; align-items: center;">
                                <img src="src/${item.main_image}" style="width: 50px; height: 60px; object-fit: cover;">
                                
                                <div><div style="font-size: 12px; text-transform: uppercase;">${item.name}</div><div style="font-weight: bold; font-size: 13px;">${Number(item.price).toLocaleString()} ₸</div></div>
                            
                            </a>`).join('');
                    }
                }
            } catch (err) { console.error(err); }
        });
    }
    sidebar.classList.add('active');
    overlay.classList.add('active');
}

function closeSidebar() {
    document.getElementById('sidebar-container').classList.remove('active');
    document.getElementById('overlay').classList.remove('active');
}

function switchAuth(mode) {
    const loginForm = document.getElementById('login-form'), regForm = document.getElementById('reg-form'), tabs = document.querySelectorAll('.auth-tab');
    if (mode === 'login') {
        loginForm.style.display = 'block'; regForm.style.display = 'none';
        tabs[0].classList.add('active'); tabs[1].classList.remove('active');
    } else {
        loginForm.style.display = 'none'; regForm.style.display = 'block';
        tabs[0].classList.remove('active'); tabs[1].classList.add('active');
    }
}

// 4. КОРЗИНА И ИЗБРАННОЕ
async function loadCart() {
    const content = document.getElementById('sidebar-content');
    try {
        const response = await fetch('auth/get_cart.php');
        const data = await response.json();
        if (data.status === 'success') {
            if (!data.items || data.items.length === 0) {
                content.innerHTML = `<div class="sidebar-header-simple" style="font-weight:bold; margin-bottom:20px;">${t.cartTitle}</div><p style="padding:20px;">${t.emptyCart}</p>`;
                return;
            }
            let html = `<div class="sidebar-header-simple" style="font-weight:bold; margin-bottom:20px;">${t.cartTitle}</div><div id="cart-scroll-container" class="cart-items" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">`;
            data.items.forEach(item => {
                html += `
                    <div class="cart-item" style="display:flex; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                        <img src="src/${item.main_image}" style="width: 60px; height: 80px; object-fit: cover;">
                        <div style="flex: 1;">
                            <div style="font-size: 12px; font-weight: bold; text-transform: uppercase;">${getProdName(item)}</div>
                            <div style="font-size: 14px;">${Number(item.price).toLocaleString()} ₸</div>
                            <button onclick="removeFromCart(${item.id})" style="background:none; border:none; color:red; cursor:pointer; font-size:10px; padding:0; text-decoration:underline;">${t.delete}</button>
                        </div>
                    </div>`;
            });
            html += `</div><div style="margin-top: 20px; border-top: 2px solid #000; padding-top: 15px;">
                <div style="display: flex; justify-content: space-between; font-weight: bold;"><span>${t.total}:</span><span>${data.totalPrice.toLocaleString()} ₸</span></div>
                <button class="add-btn" onclick="checkout()">${t.checkout}</button>
            </div>`;
            content.innerHTML = html;
        }
    } catch (e) { console.error(e); }
}

async function loadFavorites() {
    const content = document.getElementById('sidebar-content');
    try {
        const response = await fetch('auth/get_favorites.php');
        const data = await response.json();

        if (data.status === 'success') {
<<<<<<< HEAD
            if (data.items.length === 0) {
=======
            if (!data.items || data.items.length === 0) {
>>>>>>> 51cee659f40054ed2ec7d85135da49fb6d5fe41e
                content.innerHTML = `<div class="sidebar-header-simple" style="font-weight:bold; margin-bottom:20px;">${t.favTitle}</div><p style="padding:20px;">${t.emptyFav}</p>`;
                return;
            }
            let html = `<div class="sidebar-header-simple" style="font-weight:bold; margin-bottom:20px;">${t.favTitle}</div><div class="cart-items" style="padding:10px 0;">`;
            data.items.forEach(item => {
                html += `
                    <div class="cart-item" style="display:flex; gap:15px; margin-bottom:15px; align-items:center; border-bottom: 1px solid #eee; padding-bottom:10px;">
                        <img src="src/${item.main_image}" style="width:60px; height:80px; object-fit:cover;">
<<<<<<< HEAD
                        <div>
                            <div style="font-size:12px; text-transform:uppercase;">${item.name}</div>
=======
                        <div style="flex:1;">
                            <div style="font-size:12px; text-transform:uppercase;">${getProdName(item)}</div>
>>>>>>> 51cee659f40054ed2ec7d85135da49fb6d5fe41e
                            <div style="font-weight:bold;">${Number(item.price).toLocaleString()} ₸</div>
                            <div style="display:flex; gap:10px; margin-top:5px; align-items:center;">
                                <a href="item.php?id=${item.id}" class="view-link" style="font-size:10px; color:gray; text-decoration:underline;">${t.view}</a>
                                <button onclick="removeFromFavorites(${item.id})" style="background:none; border:none; color:red; cursor:pointer; font-size:10px; padding:0; text-decoration:underline;">${t.delete}</button>
                            </div>
                        </div>
                    </div>`;
            });
            content.innerHTML = html + '</div>';
        }
<<<<<<< HEAD
    } catch (e) { content.innerHTML = `<div class="sidebar-header-simple">${t.favTitle}</div><p>Error</p>`; }

=======
    } catch (e) { 
        content.innerHTML = `<div class="sidebar-header-simple">${t.favTitle}</div><p>Error</p>`; 
    }
}

async function removeFromFavorites(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', 'favorite'); 

    try {
        const response = await fetch('auth/action_handler.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.status === 'success') {
          
            loadFavorites();
            
            const heartBtn = document.querySelector(`[onclick*="toggleAction(${productId}, 'favorite'"]`);
            if (heartBtn) {
                const img = heartBtn.querySelector('img');
                if (img) {
                    img.src = 'src/heart.png';
                    img.style.filter = 'brightness(0)';
                }
            }
        }
    } catch (e) { console.error(e); }
>>>>>>> 51cee659f40054ed2ec7d85135da49fb6d5fe41e
}

async function removeFromCart(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('action', 'remove_cart');
    try {
        const response = await fetch('auth/action_handler.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.status === 'success') loadCart();
    } catch (e) { console.error(e); }
}



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
                
                if (result.action === 'added') {
                    img.src = 'src/heart-filled.png';
                    img.style.filter = 'none';
                } else if (result.action === 'removed') {
                    img.src = 'src/heart.png';
                    img.style.filter = 'brightness(0)';
                }
            }
            
            // ВЕРНО: либо вызываем функцию, либо проверяем тип
            if (actionType === 'cart') {
                alert(t.addedToCart);
            }
        }
    } catch (error) {
        console.error(t.error, error);
    }
}

async function addToCart(productId) {
    if (!userIsLogged) {
        alert(t.loginRequired);
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
            alert(t.addedToCart);
        } else {
            alert(t.error + result.message);
        }
    } catch (error) {
        console.error(t.error, error);
    }
}





function deleteProduct(productId) {
    if (confirm(t.confirmDelete)) {
        fetch('auth/delete_product.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'id=' + productId })
        .then(response => response.text())
        .then(data => { if (data.trim() === 'success') location.reload(); else alert('Error: ' + data); });
    }
}

function checkout() { window.location.href = 'checkout.php'; }

document.addEventListener('DOMContentLoaded', () => {
    const closeBtn = document.getElementById('close-sidebar'), overlay = document.getElementById('overlay');
    if (closeBtn) closeBtn.onclick = closeSidebar;
    if (overlay) overlay.onclick = closeSidebar;
    document.addEventListener('keydown', (e) => { if (e.key === "Escape") closeSidebar(); });
});