<?php
require_once __DIR__.'/../../config/app.php';require_role('member');header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
$data=json_decode(file_get_contents('php://input'),true)??[];if(!hash_equals(csrf_token(),$data['csrf_token']??'')){http_response_code(419);echo json_encode(['error'=>'Invalid token']);exit;}
$date=$data['session_date']??'';if(!$date||strtotime($date)<time()){http_response_code(422);echo json_encode(['error'=>'Choose a future session']);exit;}
$pdo->prepare('INSERT INTO bookings(member_id,session_date,notes) VALUES(?,?,?)')->execute([current_user()['id'],date('Y-m-d H:i:s',strtotime($date)),substr(trim($data['notes']??''),0,500)]);echo json_encode(['success'=>true]);
