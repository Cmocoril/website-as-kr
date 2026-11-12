<?php
// ВАЖНО: Этот файл нужно удалить после использования!
require_once 'php/db_connect.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Настройка БД</title>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .err{color:red;} pre{background:#f5f5f5;padding:10px;}</style></head><body>";
echo "<h1>🔧 Настройка базы данных turbaza_db</h1>";

$success = 0;
$errors = 0;

// Функция для выполнения SQL
function runSQL($pdo, $sql, $description) {
    global $success, $errors;
    echo "<p><strong>$description</strong></p>";
    try {
        $pdo->exec($sql);
        echo "<p class='ok'>✅ Выполнено успешно</p>";
        $success++;
    } catch (PDOException $e) {
        // Если поле уже существует — это не ошибка, просто пропускаем
        if (strpos($e->getMessage(), 'Duplicate column') !== false || 
            strpos($e->getMessage(), 'already exists') !== false) {
            echo "<p class='ok'>⚠️ Уже существует (пропущено)</p>";
            $success++;
        } else {
            echo "<p class='err'>❌ Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
            $errors++;
        }
    }
    echo "<hr>";
}

// 1. Добавляем поле роли в таблицу пользователей
runSQL($pdo, 
    "ALTER TABLE users ADD COLUMN role ENUM('user', 'employee', 'admin') DEFAULT 'user'",
    "1. Добавление поля 'role' в таблицу users"
);

// 2. Делаем текущего пользователя сотрудником
runSQL($pdo,
    "UPDATE users SET role = 'employee' WHERE email = 'x77722855@gmail.com'",
    "2. Назначение роли 'employee' текущему пользователю"
);

// 3. Добавляем поле себестоимости в таблицу домиков
runSQL($pdo,
    "ALTER TABLE cabins ADD COLUMN cost_per_night DECIMAL(10,2) DEFAULT 0.00",
    "3. Добавление поля 'cost_per_night' в таблицу cabins"
);

// 4. Задаём тестовую себестоимость
runSQL($pdo,
    "UPDATE cabins SET cost_per_night = 1500.00 WHERE id = 1",
    "4. Установка себестоимости для 'Лесной домик'"
);
runSQL($pdo,
    "UPDATE cabins SET cost_per_night = 2500.00 WHERE id = 2",
    "5. Установка себестоимости для 'Семейный коттедж'"
);
runSQL($pdo,
    "UPDATE cabins SET cost_per_night = 5000.00 WHERE id = 3",
    "6. Установка себестоимости для 'VIP-шале'"
);

// 5. Проверка: выводим текущее состояние таблиц
echo "<h2>📊 Текущее состояние таблиц</h2>";

echo "<h3>Таблица users:</h3><pre>";
$stmt = $pdo->query("SELECT id, username, email, role FROM users");
while ($row = $stmt->fetch()) {
    echo htmlspecialchars(print_r($row, true));
}
echo "</pre>";

echo "<h3>Таблица cabins:</h3><pre>";
$stmt = $pdo->query("SELECT id, name, price_per_night, cost_per_night FROM cabins");
while ($row = $stmt->fetch()) {
    echo htmlspecialchars(print_r($row, true));
}
echo "</pre>";

// Итог
echo "<h2>📝 Итог</h2>";
echo "<p>Успешно: <strong class='ok'>$success</strong> | Ошибок: <strong class='err'>$errors</strong></p>";

if ($errors === 0) {
    echo "<p class='ok'><strong>✅ База данных успешно настроена!</strong></p>";
    echo "<p>Теперь можно:</p>";
    echo "<ul>";
    echo "<li>Открыть <a href='analytics.php'>аналитический модуль</a></li>";
    echo "<li>Войти под своим аккаунтом — у тебя уже роль 'employee'</li>";
    echo "</ul>";
    echo "<p style='background:#fff3cd;padding:15px;border-radius:5px;'>";
    echo "<strong>⚠️ ВАЖНО:</strong> Удали файл <code>setup.php</code> после использования! ";
    echo "Он даёт полный доступ к изменению БД.</p>";
} else {
    echo "<p class='err'>Есть ошибки. Проверь сообщения выше.</p>";
}

echo "</body></html>";
?>