<?php
// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();

// Tạo mã thông báo CSRF nếu chưa có
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo token ngẫu nhiên
}

// Xử lý biểu mẫu đăng nhập
if (!empty($_POST['submit'])) {
    // Kiểm tra mã thông báo CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = 'CSRF token validation failed';
    } else {
        $users = [
            'username' => $_POST['username'],
            'password' => $_POST['password']
        ];
        $user = NULL;
        if ($user = $userModel->auth($users['username'], $users['password'])) {
            // Đăng nhập thành công
            $_SESSION['id'] = $user[0]['id'];
            $_SESSION['message'] = 'Login successful';
            // Tạo lại CSRF token mới sau khi đăng nhập thành công
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('location: list_users.php');
            exit;
        } else {
            // Đăng nhập thất bại
            $_SESSION['message'] = 'Login failed';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User form</title>
    <?php include 'views/meta.php' ?>
</head>
<body>
<?php include 'views/header.php'?>

<div class="container">
    <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Login</div>
                <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
            </div>

            <div style="padding-top:30px" class="panel-body">
                <!-- Hiển thị thông báo nếu có -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="form-horizontal" role="form">
                    <!-- Thêm trường ẩn cho CSRF token -->
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username or email">
                    </div>

                    <div class="margin-bottom-25 input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                    </div>

                    <div class="margin-bottom-25">
                        <input type="checkbox" tabindex="3" class="" name="remember" id="remember">
                        <label for="remember"> Remember Me</label>
                    </div>

                    <div class="margin-bottom-25 input-group">
                        <!-- Button -->
                        <div class="col-sm-12 controls">
                            <button type="submit" name="submit" value="submit" class="btn btn-primary">Submit</button>
                            <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 control">
                            Don't have an account!
                            <a href="form_user.php">Sign Up Here</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>