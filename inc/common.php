<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__.'/db.php';
function esc($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function go(string $path): never { header('Location: '.$path); exit; }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf(): void { if (!hash_equals($_SESSION['csrf']??'', (string)($_POST['csrf']??''))) {http_response_code(403);exit('CSRF không hợp lệ');} }
function require_employee(): void { if (empty($_SESSION['employee_id'])) go('login.php'); if (empty($_SESSION['can_contract'])) {http_response_code(403); exit('Không có quyền lập hợp đồng');} }
function money($n): string {return number_format((float)$n,0,',','.').' đ';}
function page_start(string $title): void {echo '<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.esc($title).' — AUK</title><link rel="stylesheet" href="assets/style.css"></head><body><header><div class="container nav"><div class="logo">AUK <span>CAR RENTAL</span></div><nav>';if(!empty($_SESSION['employee_id']))echo '<a href="index.php">Yêu cầu thuê</a> <a href="contracts.php">Hợp đồng</a> <a href="logout.php">Đăng xuất</a>';echo '</nav></div></header><main class="container">';}
function page_end(): void { echo '</main><footer>Hệ thống AUK · Demo học phần · UC27 Lập hợp đồng thuê xe</footer></body></html>'; }
