<?php
require_once __DIR__.'/../../config/app.php';require_login();header('Content-Type: application/json');http_response_code(501);
echo json_encode(['error'=>'Payment gateway is not configured. Add Razorpay keys and server-side order creation before accepting payments.']);
