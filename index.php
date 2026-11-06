<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    session_start();
    $is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Турбаза "Лесная поляна" - Аренда домиков</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
    <header>
        <nav>
            <nav>
                <div class="logo">🌲 Лесная поляна</div>
                
             
                <button class="burger" id="burger-btn">☰</button>
                
                
                <ul id="nav-menu">
                    <li><a href="#home">Главная</a></li>
                    <li><a href="#cabins">Домики</a></li>
                    <li><a href="#home">Главная</a></li>
                    <li><a href="#cabins">Домики</a></li>
                    <li><a href="#about">О нас</a></li>
                    <li><a href="#contact">Контакты</a></li>
                    <?php if ($is_logged_in): ?>
                        <li><a href="cabinet.php">Личный кабинет</a></li>
                        <li><a href="php/logout.php">Выйти</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Войти</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <div class="logo">🌲 Лесная поляна</div>
            <ul>
                <li><a href="#home">Главная</a></li>
                <li><a href="#cabins">Домики</a></li>
                <li><a href="#about">О нас</a></li>
                <li><a href="#contact">Контакты</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="cabinet.php">Личный кабинет</a></li>
                    <li><a href="php/logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="login.php">Войти</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Отдых в сердце природы</h1>
            <p>Уютные домики в лесу для вашего идеального отпуска</p>
            <a href="#cabins" class="btn">Забронировать</a>
        </div>
    </section>

    <section id="cabins">
        <h2>Наши домики</h2>
        <div class="cabins-grid">
            <div class="cabin-card">
                <img src="https://images.unsplash.com/photo-1587061949409-02df41d5e562?w=600" alt="Домик 1">
                <div class="cabin-info">
                    <h3>Лесной домик</h3>
                    <p>Уютный домик для двоих с видом на лес</p>
                    <div class="cabin-price">3 500 ₽/ночь</div>
                    <a href="<?= $is_logged_in ? 'booking.php?cabin_id=1' : 'login.php' ?>" class="btn">Забронировать</a>
                </div>
            </div>
            <div class="cabin-card">
                <img src="https://images.unsplash.com/photo-1518732714860-b62714ce0c59?w=600" alt="Домик 2">
                <div class="cabin-info">
                    <h3>Семейный коттедж</h3>
                    <p>Просторный коттедж для семьи до 6 человек</p>
                    <div class="cabin-price">6 000 ₽/ночь</div>
                    <a href="<?= $is_logged_in ? 'booking.php?cabin_id=1' : 'login.php' ?>" class="btn">Забронировать</a>
                </div>
            </div>
            <div class="cabin-card">
                <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600" alt="Домик 3">
                <div class="cabin-info">
                    <h3>VIP-шале</h3>
                    <p>Премиальный шале с сауной и джакузи</p>
                    <div class="cabin-price">12 000 ₽/ночь</div>
                    <a href="<?= $is_logged_in ? 'booking.php?cabin_id=1' : 'login.php' ?>" class="btn">Забронировать</a>
                </div>
            </div>
        </div>
    </section>

    <section id="about">
        <h2>О нашей турбазе</h2>
        <p style="text-align: center; font-size: 1.2rem; max-width: 800px; margin: 0 auto;">
            "Лесная поляна" — это место, где вы можете отдохнуть от городской суеты. 
            Мы предлагаем уютные домики в окружении вековых сосен, чистый воздух, 
            рыбалку на озере и множество активностей на свежем воздухе.
        </p>
    </section>

    <footer id="contact">
        <p>📞 +7 (999) 123-45-67 | ✉️ info@lespolyana.ru</p>
        <p>📍 Московская область, д. Лесная, 15</p>
        <p>&copy; 2026 Турбаза "Лесная поляна"</p>
    </footer>
    <script src="js/script.js"></script>
</body>
</html>