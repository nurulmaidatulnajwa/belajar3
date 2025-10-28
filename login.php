<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM karyawan WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password == $row['password']) {
            // Simpan data karyawan di session
            $_SESSION['id_karyawan'] = $row['id_karyawan'];
            $_SESSION['nama_karyawan'] = $row['nama_karyawan'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Toko Elektronik</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
    }

    body {
      margin: 0;
      height: 100vh;
      background: url('gambar toko.jpg') no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.45);
      z-index: 0;
    }

    .login-container {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 18px;
      padding: 25px 30px;
      width: 320px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
      animation: slideUp 0.8s ease;
    }

    .login-container img {
      width: 65px;
      margin-bottom: 8px;
      border-radius: 50%;
      box-shadow: 0 0 12px rgba(255,255,255,0.4);
    }

    h2 {
      margin-bottom: 15px;
      color: #fff;
      font-size: 22px;
      font-weight: 600;
    }

    input {
      width: 90%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 8px;
      border: none;
      background: rgba(255, 255, 255, 0.9);
      font-size: 14px;
      outline: none;
      transition: 0.2s;
    }

    input:focus {
      box-shadow: 0 0 4px #007bff;
    }

    button {
      width: 95%;
      padding: 10px;
      background: linear-gradient(135deg, #007bff, #00b7ff);
      border: none;
      border-radius: 8px;
      color: white;
      cursor: pointer;
      font-weight: bold;
      letter-spacing: 1px;
      transition: all 0.3s;
      margin-top: 5px;
    }

    button:hover {
      background: linear-gradient(135deg, #0062cc, #008cff);
      transform: scale(1.03);
    }

    .error {
      color: #ff5555;
      margin-top: 10px;
      font-size: 13px;
      background: rgba(255, 0, 0, 0.1);
      padding: 5px 8px;
      border-radius: 6px;
      display: inline-block;
      animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-5px);}
      to {opacity: 1; transform: translateY(0);}
    }

    @keyframes slideUp {
      from {opacity: 0; transform: translateY(50px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .footer-text {
      margin-top: 10px;
      color: #ddd;
      font-size: 11px;
      opacity: 0.8;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <img src="nm.png" alt="Logo Toko Elektronik">
    <h2>Login</h2>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">LOGIN</button>
    </form>
    <?php if (!empty($message)): ?>
      <p class="error"><?= $message; ?></p>
    <?php endif; ?>
    <p class="footer-text">© Toko MiNa Techno</p>
    <p style="margin-top:10px;">
      Belum punya akun? <a href="register.php">Daftar di sini</a>
    </p>
  </div>
</body>
</html>