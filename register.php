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
    /* Warna yang Senada dengan Dashboard */
    :root {
      --primary-color: #2f639b; /* Biru tua, sama seperti di dashboard */
      --secondary-color: #f8fbff; /* Latar belakang lembut */
      --text-color: #333;       /* Warna teks utama */
      --bg-blur: rgba(255,255,255,0.8); /* Efek blur */
    }

    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      background: linear-gradient(135deg, #eaf1ff, var(--secondary-color)); /* Gradien latar belakang */
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif; /* Font yang sama dengan dashboard */
    }

    .register-container {
      background: var(--bg-blur); /* Latar belakang dengan blur */
      backdrop-filter: blur(12px);
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.2);
      width: 350px;
      text-align: center;
    }

    h2 {
      color: var(--primary-color); /* Warna header yang sama */
      margin-bottom: 20px;
    }

    input {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ced4da; /* Border yang lebih lembut */
      font-size: 14px;
      color: var(--text-color); /* Warna teks input */
    }

    button {
      width: 95%;
      padding: 10px;
      background: linear-gradient(90deg, var(--primary-color), #3e7cc1); /* Gradient yang sama */
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2); /* Shadow yang sama */
      margin-bottom: 10px; /* Memberi jarak dengan tombol login */
    }

    button:hover {
      background: linear-gradient(90deg, #3e7cc1, #5d94d4); /* Gradient hover yang disesuaikan */
    }

    .link {
      margin-top: 15px;
      font-size: 14px;
    }

    .link a {
      color: var(--primary-color); /* Warna link yang sama */
      text-decoration: none;
      font-weight: bold;
    }

    .error {
      color: red;
      font-size: 13px;
      margin-top: 10px;
    }

    /* Style untuk tombol kembali ke login */
    .back-to-login {
      display: inline-block;
      padding: 8px 16px;
      background-color: #f0f0f0; /* Warna latar belakang tombol */
      color: var(--text-color); /* Warna teks tombol */
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease; /* Animasi yang lebih halus */
      box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Shadow yang lebih kecil */
    }

    .back-to-login:hover {
      background-color: #e0e0e0; /* Warna latar belakang saat dihover */
      transform: scale(1.05); /* Efek scale saat dihover */
      box-shadow: 0 3px 6px rgba(0,0,0,0.2); /* Efek shadow saat dihover */
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