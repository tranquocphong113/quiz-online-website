<?php
require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);
$action = $input["action"] ?? "";

if ($action === "register") {
    $fullname = trim($input["fullname"] ?? "");
    $email = trim($input["email"] ?? "");
    $password = trim($input["password"] ?? "");

    if ($fullname === "" || $email === "" || $password === "") {
        echo json_encode([
            "success" => false,
            "message" => "Vui lòng nhập đầy đủ thông tin"
        ]);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email đã tồn tại"
        ]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (fullname, email, password)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([$fullname, $email, $hashedPassword]);

    echo json_encode([
        "success" => true,
        "message" => "Đăng ký thành công"
    ]);
    exit;
}

if ($action === "login") {
    $email = trim($input["email"] ?? "");
    $password = trim($input["password"] ?? "");

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        echo json_encode([
            "success" => false,
            "message" => "Sai email hoặc mật khẩu"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $user["id"],
            "name" => $user["fullname"],
            "email" => $user["email"]
        ]
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Action không hợp lệ"
]);
?>