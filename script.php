<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$db   = 'feedback'; // �������� �� ��� ���� �� ������ SQL
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // ����� ����������� ����������
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => '������ ����������� � ���� ������']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$comment = trim($data['comment'] ?? '');

// ���������� ������� ��������
if (!$name  !$email  !$comment) {
    echo json_encode(['status' => 'error', 'message' => '����������, ��������� ��� ����']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => '������� ���������� email']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO reviews (name, email, comment) VALUES (?, ?, ?)');
    $success = $stmt->execute([$name, $email, $comment]);

    // �������: ������� ��������� ������� � ����� ������ (����� ����� ������)
    ob_start(); // ����� ������� ������� � JSON-����� ���������
    $stmt->debugDumpParams();
    $debugInfo = ob_get_clean();

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => '������� �� ��� �����!',
            'debug' => $debugInfo // ����� ������� �������, ����� ������ �����
        ]);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode([
            'status' => 'error',
            'message' => '������ ��� ����������: ' . $errorInfo[2],
            'debug' => $debugInfo
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => '������ ��� ���������� ������: ' . $e->getMessage()
    ]);
}