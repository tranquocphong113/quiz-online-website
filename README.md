Quiz Online Website
## 1.Giới thiệu
Quiz Online Website là một website làm trắc nghiệm trực tuyến được xây dựng cho môn Lập Trình Web.
Hệ thống cho phéo người dùng đăng ký, đăng nhập, tạo quiz, tạo phòng chơi, tham gia phòng bằng mã PIN,...
Dự án được xây dựng với giao diện đơn giản, dễ sử dụng và phù hợp để demo các chức năng cơ bản của 1 hệ thống quiz online.

## 2. Thành viên nhóm
- Trần Quốc Phong
- Hoàng Xuân Kỳ
- Phạm Hoàng Duy
- Lâm Văn Hậu

## 3. Chức năng chính
- Đăng ký
- Đăng nhập
- Tạo quiz
- Tạo phòng chơi
- Tham gia phòng bằng mã PIN
- Chơi quiz
- Lưu kết quả làm bài

## 4. Công nghệ sử dụng
- HTML5
- CSS3
- JavaScript ES6 Modules
- LocalStorage

## 5. Cấu trúc thư mục
quizweb
- css
  - auth.css
  - dashboard.css
  - quiz.css
  - style.css
- js
  - app.js
- model
  - api.js
  - state.js
- index.php
- quiz.html

## 6. Cấu hình backend MySQL

Để ứng dụng kết nối với MySQL, cần thêm các file sau:
- `db.php`: kết nối database
- `api.php`: API PHP xử lý đăng ký, đăng nhập, tạo phòng, tham gia phòng, lưu điểm và lấy lịch sử
- `sql_setup.sql`: tạo database và bảng cần thiết

### Bước cài đặt nhanh
1. Mở phpMyAdmin và tạo database `quizweb`.
2. Chạy nội dung trong file `sql_setup.sql` để tạo bảng `users`, `rooms`, `scores`.
3. Trong `db.php`, thay `YOUR_PASSWORD_HERE` bằng mật khẩu của user `myecm_user`.
4. Đảm bảo user `myecm_user@localhost` có quyền:
   - SELECT
   - INSERT
   - UPDATE
   - DELETE
   - CREATE

### Các endpoint sử dụng
- `api.php` nhận POST JSON với trường `action`
- Các action hiện tại:
  - `register`
  - `login`
  - `createRoom`
  - `joinRoom`
  - `saveScore`
  - `getScoresByRoom`
  - `getHistoryByUser`

### Lưu ý
Ứng dụng frontend trong `js/app.js` hiện đã chuyển sang gọi backend PHP thay vì lưu trên LocalStorage.
