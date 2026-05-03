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

if ($action === "create") {
    $userId = $input["user_id"] ?? null;
    $title = trim($input["title"] ?? "");
    $timeLimit = intval($input["time_limit"] ?? 5);
    $questions = $input["questions"] ?? [];

    if (!$userId) {
        echo json_encode([
            "success" => false,
            "message" => "Thiếu user_id"
        ]);
        exit;
    }

    if ($title === "") {
        echo json_encode([
            "success" => false,
            "message" => "Vui lòng nhập tên quiz"
        ]);
        exit;
    }

    if ($timeLimit <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Thời gian làm bài phải lớn hơn 0"
        ]);
        exit;
    }

    if (!is_array($questions) || count($questions) === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Vui lòng thêm ít nhất 1 câu hỏi"
        ]);
        exit;
    }

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("
            INSERT INTO quizzes (user_id, title, time_limit)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([$userId, $title, $timeLimit]);

        $quizId = $conn->lastInsertId();

        $questionStmt = $conn->prepare("
            INSERT INTO questions (
                quiz_id,
                question_text,
                option_a,
                option_b,
                option_c,
                option_d,
                correct_answer
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($questions as $q) {
            $questionText = trim($q["question"] ?? "");
            $options = $q["options"] ?? [];
            $correctAnswer = intval($q["correctAnswer"] ?? 0);

            if (
                $questionText === "" ||
                count($options) < 4 ||
                trim($options[0]) === "" ||
                trim($options[1]) === "" ||
                trim($options[2]) === "" ||
                trim($options[3]) === ""
            ) {
                throw new Exception("Dữ liệu câu hỏi không hợp lệ");
            }

            $questionStmt->execute([
                $quizId,
                $questionText,
                $options[0],
                $options[1],
                $options[2],
                $options[3],
                $correctAnswer
            ]);
        }

        $conn->commit();

        echo json_encode([
            "success" => true,
            "message" => "Tạo quiz thành công",
            "quiz" => [
                "id" => $quizId,
                "title" => $title,
                "timeLimit" => $timeLimit,
                "questions" => $questions
            ]
        ]);
        exit;
    } catch (Exception $e) {
        $conn->rollBack();

        echo json_encode([
            "success" => false,
            "message" => "Lỗi khi tạo quiz",
            "error" => $e->getMessage()
        ]);
        exit;
    }
}

echo json_encode([
    "success" => false,
    "message" => "Action không hợp lệ"
]);
?>