# UC27 – Lập hợp đồng thuê xe (đúng đặc tả báo cáo)
Dùng hồ sơ khách hàng đã lưu, không nhập lại trong hợp đồng. Yêu cầu thuê/mẫu xe đã chọn là điểm bắt đầu.
Cài mới: chạy setup.php theo README bản gốc. Nếu đang dùng DB cũ: đăng nhập rồi chạy upgrade.php một lần để bổ sung ghi nhận thanh toán và loại khách hàng.
Các chức năng:
- Danh sách yêu cầu; lập hợp đồng; kiểm tra xe thuộc mẫu; chọn xe thay thế có xác nhận khách đồng ý.
- Kiểm tra hồ sơ/GPLX, quyền thuê, phí chưa thanh toán; cập nhật hồ sơ ở customer_profile.php.
- Ghi nhận thanh toán phí còn nợ (có lịch sử và hỗ trợ thanh toán một phần) tại fee_payment.php.
- Lập hợp đồng + Rental + cập nhật xe sang DA_THUE trong một transaction có rollback.
- Không lập khi khách từ chối xe thay thế, không có xe, hồ sơ chưa hợp lệ, bị cấm thuê hoặc còn nợ.
Tài khoản demo xem setup.php; sử dụng XAMPP theo cấu hình inc/db.php.
Ghi chú: 'khách vãng lai' trong UC27 vẫn phải có tài khoản/hồ sơ đã lưu. Trang hồ sơ là tác vụ cập nhật riêng, không nhập lại hồ sơ tại hợp đồng.
Chưa kiểm thử tích hợp MySQL trên máy người dùng. Sao lưu DB trước khi nâng cấp. Xóa setup.php và upgrade.php sau khi dùng.
