<?php

// Ngăn PHP tự gửi cookie
ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);

// Nếu client gửi session_id qua header hoặc param
if (!empty($_SERVER['HTTP_X_SESSION_ID'])) {
    session_id($_SERVER['HTTP_X_SESSION_ID']);
}

// Start the session
session_start();

require_once 'models/UserModel.php';
$userModel = new UserModel();

// Luôn đảm bảo có CSRF token trong session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 header("Content-Type: application/json; charset=UTF-8");
    $users = [
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ];
    $user = NULL;
    if ($user = $userModel->auth($users['username'], $users['password'])) {
        //Login successful
        $_SESSION['id'] = $user[0]['id'];

        // Sinh lại CSRF token mới sau login
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        $_SESSION['message'] = 'Login successful';

         // Trả về session_id để client lưu vào localStorage
        echo json_encode([
            'status' => 'ok',
            'session_id' => session_id(),
            'csrf_token' => $_SESSION['csrf_token'],
            'message' => $_SESSION['message']
        ]);


        
    }else {
        //Login failed
        $_SESSION['message'] = 'Login failed';
         echo json_encode([
            'status' => 'fail',
            'message' => $_SESSION['message']
        ]);
    }
    exit;

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

                <form id="formLogin" class="form-horizontal" role="form">
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
 <script>
    document.getElementById("formLogin").addEventListener("submit", function(e) {
    e.preventDefault(); // không reload trang
    let username = document.getElementById("login-username").value;
    let password = document.getElementById("login-password").value;

    // Gọi login
fetch("login.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password)
})
.then(res => res.json())
.then(data => {
    if (data.status === "ok") {

        // Lưu session_id + csrf_token
        localStorage.setItem("session_id", data.session_id);
        localStorage.setItem("csrf_token", data.csrf_token);
        alert("Đăng nhập thành công!");
        window.location.href = "http://192.168.33.10:8080/list_users.php";
        
    } else {
        alert("Sai tài khoản hoặc mật khẩu");
    }
    })
    .catch(err => console.error(err));
});
 </script>
</body>
</html>