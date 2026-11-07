<?php
require_once __DIR__.'/../../config/app.php';require_login();header('Content-Type: application/json');
echo json_encode(['message'=>'AI generation endpoint is ready. Configure a provider in classes/AIService.php before enabling generated plans.']);
