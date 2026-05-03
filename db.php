<?php
// db.php
// Cấu hình kết nối MySQL cho ứng dụng Quiz Online.
// Thay giá trị USER, PASS bằng thông tin user đã cấp quyền trong phpMyAdmin.

$DB_HOST = 'localhost';
$DB_NAME = 'quizweb';
$DB_USER = 'myecm_user';
$DB_PASS = '12345';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Kết nối database thất bại: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
