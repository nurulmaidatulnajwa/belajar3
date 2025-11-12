<?php
include 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location: login.php");
  exit();
}

// Cek role (hanya admin)
if ($_SESSION['jabatan'] !== 'Admin Toko') {
  echo "<script>alert('Akses ditolak! Hanya admin yang bisa mengedit data karyawan.'); window.location='dashboard.php';</script>";
  exit();
}

// Ambil data karyawan berdasarkan ID
$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM karyawan WHERE id_karyawan = $id"));

if (!$data) {
  echo "<script>alert('Data karyawan tidak ditemukan!'); window.location='karyawan.php';</script>";
  exit();
}

// Proses update data
if (isset($_POST['simpan'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_karyawan']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  if (!empty($password)) {
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE karyawan SET nama_karyawan='$nama', username='$username', jabatan='$jabatan', password='$password_hashed' WHERE id_karyawan=$id";
  } else {
    $query = "UPDATE karyawan SET nama_karyawan='$nama', username='$username', jabatan='$jabatan' WHERE id_karyawan=$id";
  }

  if (mysqli_query($conn, $query)) {
    echo "<script>alert('Data karyawan berhasil diperbarui!'); window.location='karyawan.php';</script>";
  } else {
    echo "<script>alert('Gagal memperbarui data.');</script>";
  }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Data Karyawan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: linear-gradient(to bottom right, #eef2ff, #ffffff);
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 240px;
      height: 100%;
      background: linear-gradient(180deg, #1e3c72, #2a5298);
      color: white;
      padding-top: 20px;
    }

    .sidebar h2 {
      text-align: center;
      font-size: 18px;
      margin-bottom: 30px;
    }

    .menu {
      list-style: none;
      padding: 0;
    }

    .menu li {
      margin-bottom: 10px;
    }

    .menu li a {
      display: flex;
      align-items: center;
      color: #fff;
      text-decoration: none;
      padding: 10px 20px;
      transition: background 0.3s;
      border-radius: 8px;
    }

    .menu li a:hover,
    .menu li.active a {
      background-color: #f1f1f1;
      color: #2a5298;
    }

    .menu li a i {
      margin-right: 10px;
    }

    /* Main content */
    .main-content {
      margin-left: 260px;
      padding: 40px;
    }

    .card {
      background: white;
      padding: 30px;
      border-radius: 14px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 500px;
    }

    .card h1 {
      margin-top: 0;
      color: #2a5298;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="text"], input[type="password"], select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }

    button {
      background-color: #2a5298;
      color: white;
      padding: 10px;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #1e3c72;
    }

    .back-btn {
      display: inline-block;
      background-color: #ccc;
      color: #333;
      text-decoration: none;
      padding: 8px 14px;
      border-radius: 6px;
      margin-top: 10px;
      transition: 0.3s;
      text-align: center;
    }

    .back-btn:hover {
      background-color: #bbb;
    }
  </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <div class="card">
      <h1>Edit Data Karyawan</h1>
      <form method="POST">
        <label>Nama Karyawan</label>
        <input type="text" name="nama_karyawan" value="<?= htmlspecialchars($data['nama_karyawan']); ?>" required>

        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($data['username']); ?>" required>

        <label>Jabatan</label>
        <select name="jabatan" required>
          <option value="Admin Toko" <?= ($data['jabatan'] == 'Admin Toko') ? 'selected' : ''; ?>>Admin Toko</option>
          <option value="Kasir" <?= ($data['jabatan'] == 'Kasir') ? 'selected' : ''; ?>>Kasir</option>
          <option value="Gudang" <?= ($data['jabatan'] == 'Gudang') ? 'selected' : ''; ?>>Gudang</option>
        </select>

        <label>Password Baru (kosongkan jika tidak ingin mengubah)</label>
        <input type="password" name="password" placeholder="••••••••">

        <button type="submit" name="simpan"><i class="fas fa-save"></i> Simpan Perubahan</button>
        <a href="karyawan.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
      </form>
    </div>
  </div>
</body>
</html>
