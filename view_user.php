<?php
// Không dùng cookie
ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);

// Ưu tiên lấy session_id từ header, KHÔNG nên lấy từ URL để tránh session fixation
if (!empty($_SERVER['HTTP_X_SESSION_ID'])) {
    session_id($_SERVER['HTTP_X_SESSION_ID']);
}

session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();
$user = null;

// Nếu đã login thì lấy user từ session
/*if (!empty($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $user = $userModel->findUserById($id);
}*/
// 🔒 Kiểm tra bảo mật session
if (
    empty($_SESSION['id']) ||
    empty($_SESSION['ip']) || $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
    empty($_SESSION['ua']) || $_SESSION['ua'] !== $_SERVER['HTTP_USER_AGENT'] ||
    empty($_SESSION['fingerprint']) ||
    $_SESSION['fingerprint'] !== ($_SERVER['HTTP_X_FINGERPRINT'] ?? '')
) {
    // Nếu sai thông tin => hủy session & báo chưa login
    $_SESSION = [];
    session_destroy();
    $user = null;
} else {
    // Nếu hợp lệ thì lấy thông tin user
    $id = $_SESSION['id'];
    $user = $userModel->findUserById($id);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User profile</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php' ?>
<div class="container">

    <?php if ($user) { ?>
        <div class="alert alert-warning" role="alert">
            User profile
        </div>
        <form>
            <div class="form-group">
                <label for="name">Name</label>
                <span><?php echo htmlspecialchars($user[0]['name'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="fullname">Fullname</label>
                <span><?php echo htmlspecialchars($user[0]['fullname'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <span><?php echo htmlspecialchars($user[0]['email'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </form>
    <?php } else { ?>
        <div class="alert alert-danger" role="alert">
            Bạn chưa đăng nhập
        </div>
    <?php } ?>
</div>
</body>
</html>
