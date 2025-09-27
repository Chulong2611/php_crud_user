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
if (!empty($_SESSION['id'])) {
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
            Bạn chưa đăng nhập!
        </div>
    <?php } ?>
</div>
</body>
</html>
