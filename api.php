<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$request = json_decode(file_get_contents('php://input'), true);

if (!is_array($request)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], JSON_UNESCAPED_UNICODE);
    exit;
}

actionHandler($pdo, $request);

function actionHandler(PDO $pdo, array $request): void
{
    $action = trim($request['action'] ?? '');

    switch ($action) {
        case 'register':
            handleRegister($pdo, $request);
            break;
        case 'login':
            handleLogin($pdo, $request);
            break;
        case 'createRoom':
            handleCreateRoom($pdo, $request);
            break;
        case 'joinRoom':
            handleJoinRoom($pdo, $request);
            break;
        case 'saveScore':
            handleSaveScore($pdo, $request);
            break;
        case 'getScoresByRoom':
            handleGetScoresByRoom($pdo, $request);
            break;
        case 'getHistoryByUser':
            handleGetHistoryByUser($pdo, $request);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ'], JSON_UNESCAPED_UNICODE);
            break;
    }
}

function handleRegister(PDO $pdo, array $data): void
{
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ tên, email và mật khẩu'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email đã tồn tại'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$name, $email, $passwordHash]);

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
}

function handleLogin(PDO $pdo, array $data): void
{
    $email = trim($data['email'] ?? '');
    $password = trim($data['password'] ?? '');

    if ($email === '' || $password === '') {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập email và mật khẩu'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Sai email hoặc mật khẩu'], JSON_UNESCAPED_UNICODE);
        return;
    }

    echo json_encode(['success' => true, 'user' => ['name' => $user['name'], 'email' => $user['email']]], JSON_UNESCAPED_UNICODE);
}

function handleCreateRoom(PDO $pdo, array $data): void
{
    $quiz = $data['quiz'] ?? null;
    $hostName = trim($data['hostName'] ?? '');

    if (!is_array($quiz) || $hostName === '') {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu phòng không hợp lệ'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $title = trim($quiz['title'] ?? '');
    $timeLimit = intval($quiz['timeLimit'] ?? 0);
    $questions = $quiz['questions'] ?? [];

    if ($title === '' || $timeLimit <= 0 || !is_array($questions) || count($questions) === 0) {
        echo json_encode(['success' => false, 'message' => 'Quiz chưa đầy đủ thông tin'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $code = createUniqueRoomCode($pdo);
    $quizJson = json_encode($quiz, JSON_UNESCAPED_UNICODE);
    $playersJson = json_encode([$hostName], JSON_UNESCAPED_UNICODE);

    $stmt = $pdo->prepare('INSERT INTO rooms (code, title, time_limit, quiz_json, players_json, host_name, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$code, $title, $timeLimit, $quizJson, $playersJson, $hostName]);

    echo json_encode([
        'success' => true,
        'room' => [
            'code' => $code,
            'quiz' => $quiz,
            'players' => [$hostName],
        ],
    ], JSON_UNESCAPED_UNICODE);
}

function handleJoinRoom(PDO $pdo, array $data): void
{
    $code = trim($data['code'] ?? '');
    $playerName = trim($data['playerName'] ?? '');

    if ($code === '' || $playerName === '') {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu tham gia phòng không hợp lệ'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = $pdo->prepare('SELECT id, quiz_json, players_json FROM rooms WHERE code = ? LIMIT 1');
    $stmt->execute([$code]);
    $room = $stmt->fetch();

    if (!$room) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy phòng'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $players = json_decode($room['players_json'], true);
    if (!is_array($players)) {
        $players = [];
    }

    if (!in_array($playerName, $players, true)) {
        $players[] = $playerName;
        $playersJson = json_encode($players, JSON_UNESCAPED_UNICODE);
        $update = $pdo->prepare('UPDATE rooms SET players_json = ? WHERE id = ?');
        $update->execute([$playersJson, $room['id']]);
    }

    $quiz = json_decode($room['quiz_json'], true);

    echo json_encode([
        'success' => true,
        'room' => [
            'code' => $code,
            'quiz' => $quiz,
            'players' => $players,
        ],
    ], JSON_UNESCAPED_UNICODE);
}

function handleSaveScore(PDO $pdo, array $data): void
{
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $roomCode = trim($data['roomCode'] ?? '');
    $quizTitle = trim($data['quizTitle'] ?? '');
    $score = intval($data['score'] ?? 0);
    $total = intval($data['total'] ?? 0);
    $percent = intval($data['percent'] ?? ($total > 0 ? round($score * 100 / $total) : 0));

    if ($name === '' || $email === '' || $roomCode === '' || $quizTitle === '') {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu điểm chưa đầy đủ'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = $pdo->prepare('INSERT INTO scores (room_code, name, email, quiz_title, score, total, percent, completed_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$roomCode, $name, $email, $quizTitle, $score, $total, $percent]);

    echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
}

function handleGetScoresByRoom(PDO $pdo, array $data): void
{
    $roomCode = trim($data['roomCode'] ?? '');

    if ($roomCode === '') {
        echo json_encode(['success' => false, 'message' => 'Mã phòng không hợp lệ'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = $pdo->prepare('SELECT name, score, total, percent FROM scores WHERE room_code = ? ORDER BY percent DESC, score DESC, completed_at ASC');
    $stmt->execute([$roomCode]);
    $scores = $stmt->fetchAll();

    echo json_encode(['success' => true, 'scores' => $scores], JSON_UNESCAPED_UNICODE);
}

function handleGetHistoryByUser(PDO $pdo, array $data): void
{
    $email = trim($data['email'] ?? '');

    if ($email === '') {
        echo json_encode(['success' => false, 'message' => 'Email không hợp lệ'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $stmt = $pdo->prepare('SELECT id, room_code, quiz_title, score, total, percent, completed_at AS time FROM scores WHERE email = ? ORDER BY id DESC');
    $stmt->execute([$email]);
    $history = $stmt->fetchAll();

    echo json_encode(['success' => true, 'history' => $history], JSON_UNESCAPED_UNICODE);
}

function createUniqueRoomCode(PDO $pdo, int $length = 6): string
{
    do {
        $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, $length));
        $stmt = $pdo->prepare('SELECT id FROM rooms WHERE code = ? LIMIT 1');
        $stmt->execute([$code]);
    } while ($stmt->fetch());

    return $code;
}
