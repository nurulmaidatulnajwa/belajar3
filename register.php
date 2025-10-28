<?php
include 'db.php';
session_start();

$message = "";

if (isset($_POST['register'])) {
  $nama = $_POST['nama'];
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // password terenkripsi

  // Simpan data ke tabel karyawan
  $query = "INSERT INTO karyawan (nama_karyawan, username, password, jabatan, tanggal_masuk) 
            VALUES ('$nama', '$username', '$password', 'Kasir', CURDATE())";

  if (mysqli_query($conn, $query)) {
    $_SESSION['username'] = $username;
    header("Location: dashboard.php");
    exit();
  } else {
    $message = "Pendaftaran gagal: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - NuMi Techno Solution</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      background: linear-gradient(135deg, #007bff, #00c6ff);
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
    }

    .register-container {
      background: white;
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.2);
      width: 350px;
      text-align: center;
    }

    h2 {
      color: #007bff;
      margin-bottom: 20px;
    }

    input {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    button {
      width: 95%;
      padding: 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #0056b3;
    }

    .link {
      margin-top: 15px;
      font-size: 14px;
    }

    .link a {
      color: #007bff;
      text-decoration: none;
      font-weight: bold;
    }

    .error {
      color: red;
      font-size: 13px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <h2>Daftar Akun Kasir</h2>
    <form method="POST">
      <input type="text" name="nama" placeholder="Nama Lengkap" required>
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="register">Daftar</button>
    </form>
    <div class="link">
      <a href="login.php">Sudah punya akun? Login</a>
    </div>

    <?php if (!empty($message)): ?>
      <p class="error"><?= $message; ?></p>
    <?php endif; ?>
  </div>
</body>
</html>
