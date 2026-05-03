<?php
require_once "db.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode([
        "success" => false,
        "message" => "Không nhận được dữ liệu JSON"
    ]);
    exit;
}

$action = $input["action"] ?? "";

if ($action === "save") {
    $roomId = intval($input["room_id"] ?? 0);
    $playerName = trim($input["player_name"] ?? "");
    $playerEmail = trim($input["player_email"] ?? "");
    $quizTitle = trim($input["quiz_title"] ?? "");
    $score = intval($input["score"] ?? 0);
    $total = intval($input["total"] ?? 0);

    if ($roomId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu room_id"
        ]);
        exit;
    }

    if ($playerName === "") {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu tên người chơi"
        ]);
        exit;
    }

    if ($quizTitle === "") {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu tên quiz"
        ]);
        exit;
    }

    if ($total <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Tổng số câu hỏi không hợp lệ"
        ]);
        exit;
    }

    $percent = round(($score / $total) * 100);

    $stmt = $conn->prepare("
        INSERT INTO scores (
            room_id,
            player_name,
            player_email,
            quiz_title,
            score,
            total,
            percent
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $roomId,
        $playerName,
        $playerEmail,
        $quizTitle,
        $score,
        $total,
        $percent
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Lưu điểm thành công"
    ]);
    exit;
}

if ($action === "leaderboard") {
    $roomId = intval($input["room_id"] ?? 0);

    if ($roomId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu room_id"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT *
        FROM scores
        WHERE room_id = ?
        ORDER BY percent DESC, score DESC, created_at ASC
    ");
    $stmt->execute([$roomId]);

    $scores = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $scores[] = [
            "id" => intval($row["id"]),
            "name" => $row["player_name"],
            "email" => $row["player_email"],
            "quizTitle" => $row["quiz_title"],
            "score" => intval($row["score"]),
            "total" => intval($row["total"]),
            "percent" => intval($row["percent"]),
            "time" => $row["created_at"]
        ];
    }

    echo json_encode([
        "success" => true,
        "scores" => $scores
    ]);
    exit;
}

if ($action === "history") {
    $email = trim($input["email"] ?? "");

    if ($email === "") {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu email"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT 
            scores.*,
            rooms.room_code
        FROM scores
        JOIN rooms ON scores.room_id = rooms.id
        WHERE scores.player_email = ?
        ORDER BY scores.created_at DESC
    ");
    $stmt->execute([$email]);

    $history = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $history[] = [
            "id" => intval($row["id"]),
            "name" => $row["player_name"],
            "email" => $row["player_email"],
            "roomCode" => $row["room_code"],
            "quizTitle" => $row["quiz_title"],
            "score" => intval($row["score"]),
            "total" => intval($row["total"]),
            "percent" => intval($row["percent"]),
            "time" => $row["created_at"]
        ];
    }

    echo json_encode([
        "success" => true,
        "history" => $history
    ]);
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Action không hợp lệ"
]);
?>