<?php
require_once __DIR__.'/inc/common.php';
if (!empty($_SESSION['employee_id'])) go('index.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf();
 $q=db()->prepare('SELECT * FROM employees WHERE username=?');$q->execute([trim((string)($_POST['username']??''))]);$e=$q->fetch();
 if($e && password_verify((string)($_POST['password']??''),$e['password_hash'])) {session_regenerate_id(true);$_SESSION['employee_id']=(int)$e['id'];$_SESSION['employee_name']=$e['full_name'];$_SESSION['can_contract']=(int)$e['can_contract'];go('index.php');}
 $error='Tên đăng nhập hoặc mật khẩu không đúng.';
}
page_start('Đăng nhập'); ?>
<div class="auth card"><span class="eyebrow">NHÂN VIÊN AUK</span><h1>Đăng nhập hệ thống</h1><p>Chức năng lập hợp đồng thuê xe.</p><?php if($error):?><div class="alert error"><?=esc($error)?></div><?php endif;?><form method="post"><input type="hidden" name="csrf" value="<?=esc(csrf())?>"><label>Tên đăng nhập<input name="username" required autofocus autocomplete="username"></label><label>Mật khẩu<input name="password" type="password" required autocomplete="current-password"></label><button class="btn">Đăng nhập</button></form></div>
<?php page_end(); ?>
