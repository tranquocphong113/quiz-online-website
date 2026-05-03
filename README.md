Quiz Online Website

## 1.Giới thiệu
Quiz Online Website là một website làm trắc nghiệm trực tuyến được xây dựng cho môn Lập Trình Web.
Hệ thống cho phéo người dùng đăng ký, đăng nhập, tạo quiz, tạo phòng chơi, tham gia phòng bằng mã PIN,...

Dự án được xây dựng với giao diện đơn giản, dễ sử dụng và phù hợp để demo các chức năng cơ bản của 1 hệ thống quiz online.

---

## 2. Thành viên nhóm

- Trần Quốc Phong
- Hoàng Xuân Kỳ
- Phạm Hoàng Duy
- Lâm Văn Hậu
## 3. Chức năng chính

👑 Dành cho Host
🗂 Quản lý Quiz (CRUD)

Tạo mới bộ câu hỏi với tiêu đề, mô tả và danh mục
Thêm / chỉnh sửa / xóa từng câu hỏi trắc nghiệm (4 lựa chọn, 1 đáp án đúng)
Thiết lập thời gian giới hạn riêng cho mỗi câu (mặc định 20 giây)
Cho phép hoặc ẩn đáp án đúng sau mỗi câu

🚪 Tạo & Quản lý Phòng Chơi

Tạo phòng mới từ bộ quiz đã có — hệ thống tự động sinh mã PIN 6 chữ số ngẫu nhiên, đảm bảo không trùng với các phòng đang hoạt động
Xem danh sách Participant đang chờ trong Lobby theo thời gian thực
Kick người chơi không mong muốn ra khỏi phòng trước hoặc trong khi chơi
Đóng phòng thủ công hoặc tự động sau khi kết thúc trận

🎮 Điều Phối Trận Đấu

Nhấn Start để bắt đầu — toàn bộ thiết bị Participant đồng bộ hiển thị câu hỏi ngay lập tức
Chủ động chuyển câu tiếp theo hoặc chờ hết giờ tự động chuyển
Theo dõi số lượng người đã trả lời theo thời gian thực trong khi chờ
Tạm dừng trận đấu khi cần thiết

📊 Xem Kết Quả & Báo Cáo

Bảng xếp hạng Leaderboard cập nhật trực tiếp sau mỗi câu hỏi
Xem thống kê tổng kết sau trận: điểm cao nhất, trung bình, tỷ lệ trả lời đúng theo từng câu
Xuất kết quả trận đấu để lưu trữ


🙋 Dành cho Participant
🔑 Tham Gia Phòng

Truy cập trang join, nhập mã PIN và tên hiển thị — không cần đăng ký hay đăng nhập
Vào Lobby chờ Host bắt đầu, thấy danh sách người chơi khác đang tham gia
Hệ thống báo lỗi rõ ràng nếu mã PIN không tồn tại hoặc phòng đã bắt đầu

📝 Làm Bài Quiz

Nhận câu hỏi đồng bộ ngay khi Host phát — không bị lệch pha với các người chơi khác
Đồng hồ đếm ngược hiển thị thời gian còn lại của từng câu, đổi màu cảnh báo khi sắp hết giờ
Chọn đáp án bằng một lần nhấn — giao diện phản hồi trực quan (highlight đáp án đã chọn)
Nếu hết giờ mà chưa chọn, hệ thống tự ghi nhận bỏ qua (0 điểm câu đó)

🏆 Theo Dõi Điểm Số

Sau mỗi câu: hiển thị kết quả đúng/sai, điểm vừa nhận và thứ hạng hiện tại
Xem Leaderboard top người chơi sau mỗi câu hỏi
Màn hình kết quả cuối trận: điểm tổng, thứ hạng chung và thống kê cá nhân

## 4.Công nghệ sử dụng
Backend
├── PHP           — REST API, xử lý nghiệp vụ, kết nối DB
├── Node.js       — WebSocket server (chạy song song với PHP/Apache)
└── Socket.io     — Đồng bộ real-time giữa Host và Participant

Database
└── MySQL         — Lưu trữ users, quiz, questions, rooms, scores

Frontend
├── HTML5         — Cấu trúc trang
├── CSS3          — Responsive design (Flexbox, Media Query)
└── JavaScript    — Kết nối Socket.io, cập nhật UI động

Môi trường
├── XAMPP         — Apache + MySQL local
├── Virtual Hosts — Tên miền cục bộ cho project
└── Git           — Quản lý phiên bản
## 5. Cấu trúc thư mục
quiz-online/
│
├── css/                            # Stylesheet toàn dự án
│   ├── auth.css                    # Style trang đăng nhập / đăng ký
│   ├── dashboard.css               # Style trang dashboard Host
│   ├── quiz.css                    # Style màn hình làm bài quiz
│   └── style.css                   # Style dùng chung (reset, layout, components)
│
├── js/
│   └── app.js                      # Logic frontend chính (kết nối Socket.io, xử lý UI)
│
├── model/
│   ├── api.js                      # Các hàm gọi REST API (fetch wrapper)
│   └── state.js                    # Quản lý state phía client (phòng, điểm, câu hỏi)
│
├── README.md                       # Tài liệu dự án
├── index.php                       # Trang chủ — điều hướng Host / Participant
└── quiz.html                       # Giao diện làm bài quiz (Participant)
