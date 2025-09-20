// LOGIN
  document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('login.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'ok') {
        localStorage.setItem('session_id', data.session_id);
        resultBox.textContent = "✅ Login thành công\nSession ID: " + data.session_id;
      } else {
        resultBox.textContent = "❌ Login thất bại: " + data.message;
      }
    })
    .catch(err => {
      resultBox.textContent = "⚠️ Lỗi: " + err;
    });
  });