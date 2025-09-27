<?php
// Không dùng cookie
ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
// Nhận session_id từ header
if (!empty($_SERVER['HTTP_X_SESSION_ID'])) {
    session_id($_SERVER['HTTP_X_SESSION_ID']);
}

session_start();
// Xóa toàn bộ session
$_SESSION = [];
session_destroy();

// Trả JSON về client
header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
    'status' => 'ok',
    'message' => 'Logged out'
]);
exit;

?>