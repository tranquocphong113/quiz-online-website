<?php
header('Content-Type: application/json; charset=utf-8');
require 'db.php'; // Gọi file kết nối database (chứa biến PDO $conn hoặc $pdo)

try {
    // Đảm bảo dùng đúng tên biến kết nối. Dựa vào lỗi của bạn, biến này tên là $conn
    // 1. Lấy các bài quiz được đánh dấu hiển thị trang chủ (is_preset = 1)
    $queryQuiz = "SELECT * FROM quizzes WHERE is_preset = 1";
    $stmtQuiz = $conn->query($queryQuiz);
    
    $presetQuizzes = [];

    // PDO dùng fetch(PDO::FETCH_ASSOC) thay vì mysqli_fetch_assoc
    while ($quiz = $stmtQuiz->fetch(PDO::FETCH_ASSOC)) {
        $quizId = $quiz['id'];
        
        // 2. Lấy toàn bộ câu hỏi của bài quiz đó
        $queryQuestions = "SELECT * FROM questions WHERE quiz_id = :quiz_id";
        $stmtQuestions = $conn->prepare($queryQuestions);
        $stmtQuestions->execute(['quiz_id' => $quizId]);
        
        $questions = [];
        while ($q = $stmtQuestions->fetch(PDO::FETCH_ASSOC)) {
            // Gom 4 đáp án
            $options = [$q['option_a'], $q['option_b'], $q['option_c'], $q['option_d']];
            
            $questions[] = [
                "question" => $q['question_text'], 
                "options" => $options,
                "correctAnswer" => (int)$q['correct_answer']
            ];
        }

        // 3. Đóng gói câu hỏi vào quiz
        $quiz['questions'] = $questions;
        $quiz['questionCount'] = count($questions);
        $presetQuizzes[] = $quiz;
    }

    echo json_encode(["success" => true, "quizzes" => $presetQuizzes]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Lỗi truy vấn: " . $e->getMessage()]);
}
?>