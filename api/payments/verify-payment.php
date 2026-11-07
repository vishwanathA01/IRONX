<?php
require_once __DIR__.'/../../config/app.php';require_login();header('Content-Type: application/json');http_response_code(501);
echo json_encode(['error'=>'Payment verification must be implemented with your Razorpay secret and verified server-side webhook.']);
