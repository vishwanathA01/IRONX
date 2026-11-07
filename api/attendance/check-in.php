<?php
require_once __DIR__.'/../../config/app.php';require_login();header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit;}
$data=json_decode(file_get_contents('php://input'),true)??[];if(!hash_equals(csrf_token(),$data['csrf_token']??'')){http_response_code(419);echo json_encode(['error'=>'Invalid token']);exit;}
$s=$pdo->prepare("SELECT id FROM subscriptions WHERE user_id=? AND status='active' AND end_date>=CURDATE() LIMIT 1");$s->execute([current_user()['id']]);if(!$s->fetch()){http_response_code(403);echo json_encode(['error'=>'An active membership is required']);exit;}
$pdo->prepare('INSERT INTO attendance(user_id) VALUES(?)')->execute([current_user()['id']]);echo json_encode(['success'=>true]);
