<?php 
session_start(); 
require_once 'includes/db.php';

// Проверка авторизации для JS
$userIsLogged = isset($_SESSION['user_id']) ? 'true' : 'false';
$userRole = $_SESSION['role'] ?? 'user';


//-----------------------------------------------------------------

$user_favs = [];
if (isset($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $fav_query = $conn->query("SELECT product_id FROM favorites WHERE user_id = $uid");
    while ($f = $fav_query->fetch_assoc()) {
        $user_favs[] = $f['product_id'];
    }
}





//-----------------------------------------------------------------



// checking lang
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'] == 'en' ? 'en' : 'ru';
    $_SESSION['lang'] = $lang;
}


// default russian
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ru';

?>








<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Aura Store</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        // phph status parse
        const userIsLogged = <?php echo $userIsLogged; ?>;
        const userRole = '<?php echo $userRole; ?>';
    </script>
    <style>
        .product-page-layout { display: flex; min-height: 100vh; }
        .product-visual { flex: 1.2; }
        .product-visual img { width: 100%; height: auto; display: block; }
        .product-sidebar { 
            flex: 0.8; padding: 100px 60px; 
            position: sticky; top: 0; height: 100vh; box-sizing: border-box; 
        }
        .breadcrumb { font-size: 10px; letter-spacing: 1px; color: #999; text-transform: uppercase; margin-bottom: 20px; }
        .breadcrumb a { color: #999; text-decoration: none; }
        .item-name { font-size: 24px; font-weight: 300; margin-bottom: 15px; }
        .item-price { font-size: 18px; margin-bottom: 30px; }
        .item-description { font-size: 13px; line-height: 1.7; color: #444; border-top: 1px solid #eee; padding-top: 20px; }
        .add-btn { 
            width: 100%; padding: 20px; background: #000; color: #fff; border: none; 
            cursor: pointer; letter-spacing: 2px; font-size: 11px; margin-top: auto;
        }
        .apple-container {
            height: 300vh !important;
            position: relative !important;
            background-color: #000 !important;
            display: block !important;
            width: 100% !important;
            box-sizing: border-box;
        }

        .apple-sticky {
            position: sticky !important;
            position: -webkit-sticky !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            overflow: hidden !important;
            background-color: #000 !important;
            box-sizing: border-box;
        }

        .apple-track {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            width: 300vw !important; 
            height: 100% !important;
            will-change: transform !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .apple-slide {
            width: 100vw !important;
            max-width: 100vw !important;
            min-width: 100vw !important;
            height: 100vh !important;
            flex-shrink: 0 !important;
            position: relative !important;
            overflow: hidden !important;
            box-sizing: border-box;
        }

        .hero-section {
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            background: #000 !important;
        }

        .hero-video, .hero-img {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            min-width: 100% !important;
            min-height: 100% !important;
            width: auto !important;
            height: auto !important;
            transform: translate(-50%, -50%) !important;
            object-fit: cover !important;
            z-index: 1 !important;
        }

        .apple-pagination {
            position: absolute !important;
            bottom: 40px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            display: flex !important;
            gap: 12px !important;
            z-index: 100 !important;
        }

        .apple-dot {
            width: 8px !important;
            height: 8px !important;
            border-radius: 50% !important;
            background: #fff !important;
            opacity: 0.3 !important;
            transition: opacity 0.3s ease !important;
        }

        .apple-dot.active {
            opacity: 1 !important;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

   
<!-- --------------------------------------HEADER----------------------------------->
<?php include 'includes/header.php'; ?>

<!-- --------------------------------------MAIN----------------------------------->
<div class="apple-container">
    <main class="apple-sticky">
        <div class="apple-track">
            
            <div class="apple-slide">
                <div class="hero-section">
                    <video autoplay muted loop playsinline class="hero-video">
                        <source src="src/6a045143c276a_720.mp4" type="video/mp4">
                    </video>
                </div>
            </div>

            <div class="apple-slide">
                <div class="hero-section">
                    <img src="src/f.jpg" alt="Эстетика" class="hero-img">
                </div>
            </div>

            <div class="apple-slide">
                <div class="hero-section">
                    <img src="src/7foto.jpg" alt="Коллекция" class="hero-img">
                </div>
            </div>

        </div>

        <div class="apple-pagination">
            <div class="apple-dot active"></div>
            <div class="apple-dot"></div>
            <div class="apple-dot"></div>
        </div>
    </main>
</div>
<!-- --------------------------------------FOOTER----------------------------------->
<?php include 'includes/footer.php'; ?>

<script src="js/main.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const container = document.querySelector('.apple-container');
    const track = document.querySelector('.apple-track');
    const dots = document.querySelectorAll('.apple-dot');

    if (!container || !track) return;

    let parent = container.parentElement;
    while (parent && parent !== document.documentElement) {
        const style = window.getComputedStyle(parent);
        if (style.overflow !== 'visible' || style.overflowY !== 'visible' || style.overflowX !== 'visible') {
            parent.style.setProperty('overflow', 'visible', 'important');
            parent.style.setProperty('overflow-x', 'visible', 'important');
            parent.style.setProperty('overflow-y', 'visible', 'important');
        }
        parent = parent.parentElement;
    }

    function updateSlider() {
        const rect = container.getBoundingClientRect();
        const viewHeight = window.innerHeight;
        
        const scrolled = -rect.top;
        const totalScrollable = rect.height - viewHeight;

        if (totalScrollable <= 0) return;

        let progress = scrolled / totalScrollable;

        if (progress < 0) progress = 0;
        if (progress > 1) progress = 1;

        const maxTranslate = -200; 
        const currentTranslate = progress * maxTranslate;
        track.style.transform = `translateX(${currentTranslate}vw)`;

        let activeIndex = 0;
        if (progress >= 0.33 && progress < 0.66) {
            activeIndex = 1;
        } else if (progress >= 0.66) {
            activeIndex = 2;
        }

        dots.forEach((dot, idx) => {
            if (idx === activeIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    window.addEventListener('scroll', () => {
        window.requestAnimationFrame(updateSlider);
    }, { passive: true });

    updateSlider();
});
</script>

</body>
</html>