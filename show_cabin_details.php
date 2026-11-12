<?php
require_once 'php/db_connect.php';

$cabin_id = $_POST['cabin_id'] ?? 0;

if ($cabin_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.id, c.name, c.description, c.price_per_night, c.capacity, c.image_url,
                COUNT(b.id) AS total_bookings,
                AVG(b.total_price) AS avg_booking_price
            FROM cabins c
            LEFT JOIN bookings b ON c.id = b.cabin_id AND b.status != 'cancelled'
            WHERE c.id = :cabin_id
            GROUP BY c.id
        ");
        $stmt->execute(['cabin_id' => $cabin_id]);
        $cabin = $stmt->fetch();

        if ($cabin) {
            // Если в БД относительный путь — оставляем как есть, если нет — подставляем локальный по id
            $img = $cabin['image_url'];
            if (empty($img) || strpos($img, 'http') === false && strpos($img, 'images/') === false) {
                $img = 'images/cabin' . $cabin['id'] . '.jpg';
            }
            ?>
            <div style="background:#fff; padding:2rem; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; align-items:start;">
                    <div>
                        <img src="<?= htmlspecialchars($img) ?>" 
                             alt="<?= htmlspecialchars($cabin['name']) ?>" 
                             style="width:100%; border-radius:10px; object-fit:cover; height:300px;">
                    </div>
                    <div>
                        <h3 style="margin-bottom:1rem; color:#2c3e50;"><?= htmlspecialchars($cabin['name']) ?></h3>
                        <div style="margin-bottom:1rem;">
                            <strong style="color:#3498db; font-size:1.5rem;"><?= number_format($cabin['price_per_night'], 0, ',', ' ') ?> ₽</strong>
                            <span style="color:#888;">/ночь</span>
                        </div>
                        <div style="margin-bottom:1rem;">
                            <p><strong>👥 Вместимость:</strong> <?= $cabin['capacity'] ?> чел.</p>
                            <p><strong>📊 Всего бронирований:</strong> <?= $cabin['total_bookings'] ?? 0 ?></p>
                            <?php if ($cabin['avg_booking_price']): ?>
                                <p><strong>💰 Средняя стоимость брони:</strong> <?= number_format($cabin['avg_booking_price'], 0, ',', ' ') ?> ₽</p>
                            <?php endif; ?>
                        </div>
                        <p style="color:#666; line-height:1.6; margin-bottom:1.5rem;"><?= htmlspecialchars($cabin['description']) ?></p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="booking.php?cabin_id=<?= $cabin['id'] ?>" class="btn" style="display:inline-block;">Забронировать</a>
                        <?php else: ?>
                            <a href="login.php" class="btn" style="display:inline-block;">Войти для бронирования</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            echo '<p style="text-align:center; color:#888;">Домик не найден</p>';
        }
    } catch (PDOException $e) {
        echo '<p style="text-align:center; color:red;">Ошибка базы данных</p>';
    }
} else {
    echo '<p style="text-align:center; color:#888;">Выберите домик из списка</p>';
}
?>