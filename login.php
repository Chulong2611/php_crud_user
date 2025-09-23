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

        $_SESSION['message'] = 'Login successful';


         // Trả về session_id để client lưu vào localStorage
        echo json_encode([
            'status' => 'ok',
            'session_id' => session_id(),
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
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title">Login</div>
                    <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
                </div>

                <div style="padding-top:30px" class="panel-body" >
                    <form method="post" id="loginForm" class="form-horizontal" role="form">

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
                                    <a href="form_user.php">
                                        Sign Up Here
                                    </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    let username = document.getElementById("login-username").value;
    let password = document.getElementById("login-password").value;

    fetch("http://192.168.33.10:8080/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password)
    })
    /*
    .then(res => res.json())
    .then(data => {
        if (data.status === "ok") {
            localStorage.setItem("session_id", data.session_id);
            alert("Đăng nhập thành công!");
window.location.href = "http://192.168.33.10:8080/list_users.php";
        } else {
            alert("Sai tài khoản hoặc mật khẩu");
        }
    })
    .catch(err => console.error(err));
*/
 .then(res => res.text())  // 👈 đổi sang text để debug
      .then(text => {
          console.log("Raw response:", text); // debug
          try {
              let data = JSON.parse(text);
              if (data.status === "ok") {
                  localStorage.setItem("session_id", data.session_id);
                  window.location.href = "list_users.php";
              } else {
                  alert("Sai tài khoản hoặc mật khẩu");
              }
          } catch (err) {
              console.error("JSON parse error:", err);
          }
      })
      .catch(err => console.error("Fetch error:", err));
});

    </script>
</body>
</html>