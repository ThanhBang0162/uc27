<?php
require_once __DIR__.'/inc/common.php';require_employee();
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Method not allowed');}check_csrf();
$requestId=filter_var($_POST['request_id']??null,FILTER_VALIDATE_INT);$carId=filter_var($_POST['car_id']??null,FILTER_VALIDATE_INT);
$start=(string)($_POST['start_date']??'');$end=(string)($_POST['end_date']??'');
function parsed_date(string $s): ?DateTimeImmutable { $dt=DateTimeImmutable::createFromFormat('!Y-m-d',$s);return $dt && $dt->format('Y-m-d')===$s?$dt:null; }
$d1=parsed_date($start);$d2=parsed_date($end);
$error='';$savedId=null;
try {
 if(!$requestId || !$carId || !$d1 || !$d2 || $d1<new DateTimeImmutable('today') || $d2<=$d1)throw new DomainException('Ngày thuê hoặc mã dữ liệu không hợp lệ.');
 $days=(int)$d1->diff($d2)->days;if($days>365)throw new DomainException('Thời hạn thuê vượt quá 365 ngày (giới hạn demo).');
 $pdo=db();$pdo->beginTransaction();
 $q=$pdo->prepare('SELECT * FROM rental_requests WHERE id=? FOR UPDATE');$q->execute([$requestId]);$r=$q->fetch();if(!$r || $r['status']!=='CHO_XU_LY')throw new DomainException('Yêu cầu đã được xử lý hoặc không tồn tại.');
 $q=$pdo->prepare('SELECT * FROM customers WHERE id=? FOR UPDATE');$q->execute([$r['customer_id']]);$c=$q->fetch();
 if(!$c || !$c['profile_valid'] || !$c['rental_allowed'] || !$c['full_name'] || !$c['phone'] || !$c['cccd'] || !$c['license_number'] || $c['license_expiry']<$end || (float)$c['unpaid_fee']>0)throw new DomainException('Hồ sơ, GPLX, điều kiện thuê hoặc phí chưa thanh toán không đạt yêu cầu.');
 $q=$pdo->prepare('SELECT v.*,m.daily_rate FROM cars v JOIN car_models m ON m.id=v.model_id WHERE v.id=? FOR UPDATE');$q->execute([$carId]);$car=$q->fetch();
 if(!$car || $car['status']!=='SAN_SANG')throw new DomainException('Xe không còn sẵn sàng. Vui lòng chọn lại.');
 if((int)$car['model_id']!==(int)$r['model_id']){
  $q=$pdo->prepare("SELECT COUNT(*) FROM cars WHERE model_id=? AND status='SAN_SANG'");$q->execute([$r['model_id']]);
  if((int)$q->fetchColumn()>0)throw new DomainException('Mẫu xe yêu cầu vẫn còn xe sẵn sàng, vui lòng chọn đúng mẫu.');
  if(($_POST['alternative_agreed']??'')!=='1')throw new DomainException('Cần xác nhận khách đã đồng ý xe thay thế.');
 }
 $rate=(float)$car['daily_rate'];$total=$days*$rate;
 $code='HD'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(4)));
 $q=$pdo->prepare('INSERT INTO contracts(contract_code,request_id,customer_id,car_id,employee_id,start_date,end_date,rental_days,daily_rate,total_amount) VALUES(?,?,?,?,?,?,?,?,?,?)');$q->execute([$code,$requestId,$c['id'],$carId,$_SESSION['employee_id'],$start,$end,$days,$rate,$total]);$savedId=(int)$pdo->lastInsertId();
 $q=$pdo->prepare("INSERT INTO rentals(contract_id,customer_id,car_id,status) VALUES(?,?,?,'DANG_THUE')");$q->execute([$savedId,$c['id'],$carId]);
 $q=$pdo->prepare("UPDATE cars SET status='DA_THUE' WHERE id=? AND status='SAN_SANG'");$q->execute([$carId]);if($q->rowCount()!==1)throw new RuntimeException('Không thể cập nhật trạng thái xe.');
 $q=$pdo->prepare("UPDATE rental_requests SET status='DA_LAP_HOP_DONG' WHERE id=? AND status='CHO_XU_LY'");$q->execute([$requestId]);if($q->rowCount()!==1)throw new RuntimeException('Không thể cập nhật yêu cầu.');
 $pdo->commit();go('contract_view.php?id='.$savedId);
} catch(Throwable $e){if(isset($pdo) && $pdo->inTransaction())$pdo->rollBack();$error=$e instanceof DomainException?$e->getMessage():'Không thể lưu hợp đồng hoặc Rental. Dữ liệu đã được hoàn tác. Vui lòng thử lại.';}
page_start('Không thể lập hợp đồng');echo '<div class="card errorpage"><h1>Không thể lập hợp đồng</h1><div class="alert error">'.esc($error).'</div><a class="btn" href="index.php">Về danh sách yêu cầu</a></div>';page_end();
