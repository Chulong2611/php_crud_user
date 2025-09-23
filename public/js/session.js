// app.js

// Hàm login
function login(username, password) {
  const formData = new FormData();
  formData.append("username", username);
  formData.append("password", password);

  return fetch("login.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.status === "ok") {
        localStorage.setItem("session_id", data.session_id); // lưu session vào localStorage
        return { success: true, message: data.message, session_id: data.session_id };
      } else {
        return { success: false, message: data.message };
      }
    })
    .catch(err => {
      return { success: false, message: err.toString() };
    });
}

// Hàm lấy danh sách users
function getUsers() {
  const sid = localStorage.getItem("session_id") || "";
  if (!sid) {
    return Promise.resolve({ success: false, message: "Chưa login, không có session_id" });
  }

  return fetch("list_users.php", {
    headers: {
      "X-Session-Id": sid
    }
  })
    .then(res => res.json())
    .catch(err => ({ success: false, message: err.toString() }));
}

// Hàm logout
function logout() {
  const sid = localStorage.getItem("session_id") || "";
  if (!sid) {
    return Promise.resolve({ success: false, message: "Không có session_id để logout" });
  }

  return fetch("logout.php", {
    headers: {
      "X-Session-Id": sid
    }
  })
    .then(res => res.json())
    .then(data => {
      localStorage.removeItem("session_id"); // xóa session trong localStorage
      return data;
    })
    .catch(err => ({ success: false, message: err.toString() }));
}
