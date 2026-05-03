<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Online</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/quiz.css">
</head>
<body>

<div id="app">

    <!-- 1. Đăng nhập / Đăng ký -->
    <section id="screen-auth" class="screen active">
        <div class="auth-box">
            <h1>Quiz Online</h1>
            <p>Đăng nhập hoặc đăng ký để bắt đầu</p>

            <div class="auth-tabs">
                <button id="showLoginTab" class="tab-btn active">Đăng nhập</button>
                <button id="showRegisterTab" class="tab-btn">Đăng ký</button>
            </div>

            <form id="loginForm" class="auth-form">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="loginEmail" placeholder="Nhập email">
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" id="loginPassword" placeholder="Nhập mật khẩu">
                </div>

                <button type="submit" class="btn btn-dark">Đăng nhập</button>
            </form>

            <form id="registerForm" class="auth-form hidden">
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" id="registerName" placeholder="Nhập họ tên">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="registerEmail" placeholder="Nhập email">
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" id="registerPassword" placeholder="Nhập mật khẩu">
                </div>

                <button type="submit" class="btn btn-dark">Đăng ký</button>
            </form>
        </div>
    </section>

    <!-- 2. Trang chủ -->
    <section id="screen-dashboard" class="screen">
        <div class="page">
            <div class="topbar">
                <h2>Trang chủ</h2>

                <div class="user-actions">
                    <span id="userNameText"></span>
                    <button id="logoutBtn" class="btn btn-light">Đăng xuất</button>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>Tạo / chỉnh sửa quiz</h3>
                    <p>Tạo câu hỏi và đáp án cho bài quiz.</p>
                    <button class="btn btn-dark" data-screen="screen-editor">Tạo quiz</button>
                </div>

                <div class="card">
                    <h3>Tham gia phòng</h3>
                    <p>Nhập mã phòng để tham gia làm bài.</p>
                    <button class="btn btn-dark" data-screen="screen-join-room">Vào phòng</button>
                </div>

                <div class="card">
                    <h3>Lịch sử làm bài</h3>
                    <p>Xem lại các bài quiz bạn đã từng tham gia.</p>
                    <button class="btn btn-dark" id="openHistoryBtn">Xem lịch sử</button>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Tạo / chỉnh sửa quiz -->
    <section id="screen-editor" class="screen">
        <div class="page">
            <div class="topbar">
                <h2>Tạo / chỉnh sửa quiz</h2>
                <button class="btn btn-light" data-screen="screen-dashboard">Quay lại</button>
            </div>

            <div class="card">
                <div class="form-group">
                    <label>Tên quiz</label>
                    <input type="text" id="quizTitle" placeholder="Ví dụ: Quiz HTML cơ bản">
                </div>

                <div class="form-group">
                    <label>Thời gian làm bài (phút)</label>
                    <input type="number" id="quizTimeLimit" value="5" min="1">
                </div>

                <div class="form-group">
                    <label>Câu hỏi</label>
                    <input type="text" id="questionText" placeholder="Nhập câu hỏi">
                </div>

                <div class="form-group">
                    <label>Đáp án A</label>
                    <input type="text" id="optionA">
                </div>

                <div class="form-group">
                    <label>Đáp án B</label>
                    <input type="text" id="optionB">
                </div>

                <div class="form-group">
                    <label>Đáp án C</label>
                    <input type="text" id="optionC">
                </div>

                <div class="form-group">
                    <label>Đáp án D</label>
                    <input type="text" id="optionD">
                </div>

                <div class="form-group">
                    <label>Đáp án đúng</label>
                    <select id="correctAnswer">
                        <option value="0">A</option>
                        <option value="1">B</option>
                        <option value="2">C</option>
                        <option value="3">D</option>
                    </select>
                </div>

                <div class="button-row">
                    <button id="addQuestionBtn" class="btn btn-light">Thêm câu hỏi</button>
                    <button id="createRoomBtn" class="btn btn-dark">Tạo phòng</button>
                </div>

                <div id="questionList" class="question-list"></div>
            </div>
        </div>
    </section>

    <!-- 4. Phòng chờ -->
    <section id="screen-waiting-room" class="screen">
        <div class="page small-page">
            <div class="card text-center">
                <h2>Phòng chờ</h2>
                <p>Mã phòng:</p>
                <h1 id="roomCodeText">----</h1>

                <div id="playerList" class="player-list"></div>

                <div class="button-row center">
                    <button class="btn btn-light" data-screen="screen-dashboard">Về trang chủ</button>
                    <button id="startQuizBtn" class="btn btn-dark">Bắt đầu</button>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Tham gia phòng -->
    <section id="screen-join-room" class="screen">
        <div class="page small-page">
            <div class="card">
                <h2>Tham gia phòng</h2>

                <div class="form-group">
                    <label>Mã phòng</label>
                    <input type="text" id="joinRoomCode" placeholder="Nhập mã phòng">
                </div>

                <div class="button-row">
                    <button class="btn btn-light" data-screen="screen-dashboard">Quay lại</button>
                    <button id="joinRoomBtn" class="btn btn-dark">Tham gia</button>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Làm bài quiz -->
    <section id="screen-play-quiz" class="screen">
        <div class="page small-page">
            <div class="card">
                <div class="quiz-header">
                    <span id="questionNumberText">Câu 1/1</span>
                    <span id="timerText">Thời gian: 00:00</span>
                    <span id="scoreText">Điểm: 0</span>
                </div>

                <h2 id="playQuestionText">Câu hỏi</h2>

                <div id="optionBox" class="option-box"></div>

                <button id="nextQuestionBtn" class="btn btn-dark hidden">Câu tiếp theo</button>
            </div>
        </div>
    </section>

    <!-- 7. Lịch sử quiz -->
    <section id="screen-history" class="screen">
        <div class="page">
            <div class="topbar">
                <h2>Lịch sử quiz</h2>
                <button class="btn btn-light" data-screen="screen-dashboard">Quay lại</button>
            </div>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên quiz</th>
                            <th>Mã phòng</th>
                            <th>Điểm</th>
                            <th>Tỉ lệ đúng</th>
                            <th>Thời gian làm</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody"></tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- 8. Kết quả + bảng xếp hạng -->
    <section id="screen-result" class="screen">
        <div class="page result-page">
            <div class="card text-center">
                <h2>Kết quả</h2>

                <div id="resultText"></div>

                <div class="result-leaderboard-box">
                    <h3>Bảng xếp hạng</h3>

                    <table>
                        <thead>
                            <tr>
                                <th>Hạng</th>
                                <th>Tên</th>
                                <th>Điểm</th>
                                <th>Tỉ lệ đúng</th>
                            </tr>
                        </thead>
                        <tbody id="resultLeaderboardBody"></tbody>
                    </table>
                </div>

                <div class="button-row center">
                    <button class="btn btn-light" data-screen="screen-dashboard">Về trang chủ</button>
                </div>
            </div>
        </div>
    </section>

</div>

<script type="module" src="js/app.js"></script>
</body>
</html>