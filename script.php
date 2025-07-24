<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$db   = 'feedback'; // поправил на имя базы из вашего SQL
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // режим выбрасывать исключения
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к базе данных']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$comment = trim($data['comment'] ?? '');

// Исправлено условие проверки
if (!$name  !$email  !$comment) {
    echo json_encode(['status' => 'error', 'message' => 'Пожалуйста, заполните все поля']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Введите корректный email']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO reviews (name, email, comment) VALUES (?, ?, ?)');
    $success = $stmt->execute([$name, $email, $comment]);

    // Отладка: выводим параметры запроса в поток вывода (можно потом убрать)
    ob_start(); // чтобы вывести отладку в JSON-ответ аккуратно
    $stmt->debugDumpParams();
    $debugInfo = ob_get_clean();

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Спасибо за ваш отзыв!',
            'debug' => $debugInfo // здесь покажем отладку, потом убрать можно
        ]);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode([
            'status' => 'error',
            'message' => 'Ошибка при сохранении: ' . $errorInfo[2],
            'debug' => $debugInfo
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ошибка при сохранении отзыва: ' . $e->getMessage()
    ]);
}