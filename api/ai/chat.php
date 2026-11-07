<?php
require_once __DIR__ . '/../../config/app.php';
header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store');
function ai_json(int $status, array $body): never { http_response_code($status); echo json_encode($body, JSON_UNESCAPED_UNICODE); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') ai_json(405, ['error'=>'Method not allowed']);
$csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!$csrf || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) ai_json(419, ['error'=>'Session expired. Refresh the page and try again.']);
$now=time(); $_SESSION['ai_request_times']=array_values(array_filter($_SESSION['ai_request_times']??[],fn($t)=>is_int($t)&&$t>$now-60));
if(count($_SESSION['ai_request_times'])>=8) ai_json(429,['error'=>'You have reached the chat limit. Please wait a minute and try again.']);
$data=json_decode(file_get_contents('php://input'),true); if(!is_array($data)) ai_json(400,['error'=>'Invalid request body.']);
$message=trim((string)($data['message']??'')); if($message===''||mb_strlen($message)>2000) ai_json(422,['error'=>'Message must be between 1 and 2000 characters.']);
$history=$data['history']??[]; if(!is_array($history))$history=[]; $history=array_slice($history,-8);
require_once __DIR__.'/../../classes/AIService.php';
try {
 $ai=new AIService(); $reply=$ai->reply($message,$history); $_SESSION['ai_request_times'][]=$now;
 $user=current_user();
 if(!empty($user['id'])) { try { $stmt=$pdo->prepare('INSERT INTO ai_conversations (user_id,title) VALUES (?,?)'); $stmt->execute([(int)$user['id'],mb_substr($message,0,140)]); $cid=(int)$pdo->lastInsertId(); $save=$pdo->prepare("INSERT INTO ai_messages (conversation_id,sender,message) VALUES (?, 'user', ?),(?, 'assistant', ?)"); $save->execute([$cid,$message,$cid,$reply]); } catch(Throwable $storageError){error_log('IRONX chat history save failed: '.$storageError->getMessage());} }
 ai_json(200,['reply'=>$reply]);
} catch(Throwable $error) { error_log('IRONX Gemini chatbot error: '.$error->getMessage()); $publicMessage=str_contains($error->getMessage(),'not configured')?'The AI coach is not configured yet. Add your Gemini API key to .env and try again.':'The AI coach is temporarily unavailable. Please try again in a moment.'; ai_json(502,['error'=>$publicMessage]); }
