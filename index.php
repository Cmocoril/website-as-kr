<?php
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
    <!-- jQuery для AJAX (как в примере задания) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">🌲 Лесная поляна</div>
            <button class="burger" id="burger-btn">☰</button>
            <ul id="nav-menu">
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

    <!-- ===== AJAX-СЕКЦИЯ (Шаг 1 задания) ===== -->
    <section id="ajax-cabins" style="background:#f8f9fa; padding:4rem 2rem;">
        <div style="max-width:800px; margin:0 auto;">
            <h2 style="text-align:center; margin-bottom:2rem;">🔍 Быстрый просмотр домика</h2>

            <form id="cabinForm" style="background:#fff; padding:2rem; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                <label for="cabinSelect" style="display:block; margin-bottom:0.5rem; font-weight:bold;">
                    Выберите домик:
                </label>
                <select id="cabinSelect" style="width:100%; padding:0.8rem; font-size:1rem; border:1px solid #ddd; border-radius:5px;">
                    <option value="">-- Загрузка домиков... --</option>
                </select>
            </form>

            <!-- Сюда AJAX вставит детали (аналог <div id="content"> из задания) -->
            <div id="cabinDetails" style="margin-top:2rem;"></div>
        </div>
    </section>

    <!-- ===== Обычные карточки с ЛОКАЛЬНЫМИ картинками ===== -->
    <section id="cabins">
        <h2>Наши домики</h2>
        <div class="cabins-grid">
            <div class="cabin-card">
                <img src="images/cabin1.jpg" alt="Лесной домик">
                <div class="cabin-info">
                    <h3>Лесной домик</h3>
                    <p>Уютный домик для двоих с видом на лес</p>
                    <div class="cabin-price">3 500 ₽/ночь</div>
                    <a href="<?=$is_logged_in ? 'booking.php?cabin_id=1' : 'login.php'?>" class="btn">Забронировать</a>
                </div>
            </div>
            <div class="cabin-card">
                <img src="images/cabin2.jpg" alt="Семейный коттедж">
                <div class="cabin-info">
                    <h3>Семейный коттедж</h3>
                    <p>Просторный коттедж для семьи до 6 человек</p>
                    <div class="cabin-price">6 000 ₽/ночь</div>
                    <a href="<?=$is_logged_in ? 'booking.php?cabin_id=2' : 'login.php'?>" class="btn">Забронировать</a>
                </div>
            </div>
            <div class="cabin-card">
                <img src="images/cabin3.jpg" alt="VIP-шале">
                <div class="cabin-info">
                    <h3>VIP-шале</h3>
                    <p>Премиальный шале с сауной и джакузи</p>
                    <div class="cabin-price">12 000 ₽/ночь</div>
                    <a href="<?=$is_logged_in ? 'booking.php?cabin_id=3' : 'login.php'?>" class="btn">Забронировать</a>
                </div>
            </div>
        </div>
    </section>

    <section id="about">
        <h2>О нашей турбазе</h2>
        <p style="text-align:center; font-size:1.2rem; max-width:800px; margin:0 auto;">
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

    <!-- ===== AJAX-СКРИПТ (Шаг 1 задания) ===== -->
    <script>
    $(document).ready(function(){

        // 1) Загружаем список домиков в dropdown (GET-запрос)
        $.ajax({
            type: "GET",
            url: "get_cabins_list.php",
            dataType: "json",
            success: function(data){
                var select = $('#cabinSelect');
                select.empty().append('<option value="">-- Выберите домик --</option>');
                $.each(data, function(i, cabin){
                    select.append('<option value="' + cabin.id + '">' + cabin.name + ' (' + cabin.price_per_night + ' ₽/ночь)</option>');
                });
            },
            error: function(){
                $('#cabinSelect').html('<option value="">Ошибка загрузки домиков</option>');
            }
        });

        // 2) При выборе домика — AJAX-запрос на обработчик (аналог $('#idcat').change)
        $('#cabinSelect').change(function(){
            var cabinId = $(this).val();

            if (cabinId) {
                $('#cabinDetails').html('<p style="text-align:center; color:#888;">Загрузка информации...</p>');

                $.ajax({
                    type: "POST",
                    url: "show_cabin_details.php",
                    data: "cabin_id=" + cabinId,
                    success: function(html){
                        $('#cabinDetails').html(html);
                    },
                    error: function(){
                        $('#cabinDetails').html('<p style="color:red; text-align:center;">Ошибка при загрузке данных</p>');
                    }
                });
            } else {
                $('#cabinDetails').html('');
            }
            return false;
        });
    });
    </script>

    <script src="js/script.js"></script>
</body>
</html>