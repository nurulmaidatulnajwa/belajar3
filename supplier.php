<?php
session_start();
include 'db.php';
include 'sidebar.php';

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location: login.php");
  exit();
}

// Tambah supplier baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama_supplier'];
  $alamat = $_POST['alamat'];
  $telepon = $_POST['telepon'];
  $email = $_POST['email'];

  $insert = "INSERT INTO supplier (nama_supplier, alamat, telepon, email)
             VALUES ('$nama', '$alamat', '$telepon', '$email')";
  mysqli_query($conn, $insert);

  echo "<script>alert('Supplier berhasil ditambahkan!'); window.location='supplier.php';</script>";
  exit();
}

// Hapus supplier
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  mysqli_query($conn, "DELETE FROM supplier WHERE id_supplier = '$id'");
  echo "<script>alert('Supplier berhasil dihapus!'); window.location='supplier.php';</script>";
  exit();
}

// Ambil semua data supplier
$result = mysqli_query($conn, "SELECT * FROM supplier ORDER BY id_supplier DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Supplier - MiNa Techno Solution</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #eaf1ff, #f8fbff);
      margin: 0;
      display: flex;
      color: #333;
    }

    .main-content {
      margin-left: 260px;
      padding: 40px;
      width: calc(100% - 260px);
    }

    .form-box {
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      border-left: 6px solid #2f639b;
    }

    h2 {
      color: #2f639b;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin: 10px 0 5px;
      font-weight: 600;
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    button {
      background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    button:hover {
      transform: scale(1.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    thead th {
      background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
      color: white;
    }

    tr:nth-child(even) {
      background: #f3f7ff;
    }

    tr:hover {
      background: #e2edff;
    }

    .hapus-btn {
      background: #e74c3c;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
    }

    .hapus-btn:hover {
      background: #c0392b;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="form-box">
      <h2>Tambah Supplier Baru</h2>
      <form method="POST">
        <label>Nama Supplier:</label>
        <input type="text" name="nama_supplier" required>

        <label>Alamat:</label>
        <textarea name="alamat" rows="3" required></textarea>

        <label>No. Telepon:</label>
        <input type="text" name="telepon" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <button type="submit">Simpan Supplier</button>
      </form>
    </div>

    <h2>Daftar Supplier</h2>
    <table>
      <thead>
        <tr>
          <th>ID Supplier</th>
          <th>Nama Supplier</th>
          <th>Alamat</th>
          <th>No. Telepon</th>
          <th>Email</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?= $row['id_supplier']; ?></td>
          <td><?= htmlspecialchars($row['nama_supplier']); ?></td>
          <td><?= htmlspecialchars($row['alamat']); ?></td>
          <td><?= htmlspecialchars($row['no_telp']); ?></td>
          <td><?= htmlspecialchars($row['email']); ?></td>
          <td><a href="?hapus=<?= $row['id_supplier']; ?>" class="hapus-btn" onclick="return confirm('Yakin ingin menghapus supplier ini?')">Hapus</a></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

</body>
</html>
