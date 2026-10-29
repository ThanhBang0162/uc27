<?php
require_once __DIR__.'/inc/common.php';
// Chỉ cho tạo tài khoản thử nghiệm khi chưa có nhân viên nào.
$count=(int)db()->query('SELECT COUNT(*) FROM employees')->fetchColumn();
if ($count>0) {http_response_code(403);exit('Đã có tài khoản nhân viên. Hãy xóa hoặc vô hiệu hóa setup.php.');}
$hash=password_hash('Admin@2026!',PASSWORD_DEFAULT);
$stmt=db()->prepare('INSERT INTO employees(username,password_hash,full_name,can_contract) VALUES(?,?,?,1)');
$stmt->execute(['admin',$hash,'Nhân viên Demo']);
echo 'Tạo tài khoản thành công: admin / Admin@2026! — Hãy xóa setup.php trước khi sử dụng thực tế. <a href="login.php">Đăng nhập</a>';
