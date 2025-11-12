<?php
include 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location: login.php");
  exit();
}

// Cek role
if ($_SESSION['jabatan'] !== 'Admin Toko') {
  echo "<script>alert('Akses ditolak! Halaman ini hanya untuk admin.'); window.location='dashboard.php';</script>";
  exit();
}

// Tambah merek
if (isset($_POST['tambah'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_merek']);
  $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
  mysqli_query($conn, "INSERT INTO merek (nama_merek, deskripsi) VALUES ('$nama', '$deskripsi')");
  header("Location: merek.php");
  exit();
}

// Hapus merek
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  mysqli_query($conn, "DELETE FROM merek WHERE id_merek=$id");
  header("Location: merek.php");
  exit();
}

// Edit merek
if (isset($_POST['edit'])) {
  $id = intval($_POST['id_merek']);
  $nama = mysqli_real_escape_string($conn, $_POST['nama_merek']);
  $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
  mysqli_query($conn, "UPDATE merek SET nama_merek='$nama', deskripsi='$deskripsi' WHERE id_merek=$id");
  header("Location: merek.php");
  exit();
}

// Ekspor ke Excel
if (isset($_GET['export'])) {
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=data_merek.xls");

  $result = mysqli_query($conn, "SELECT * FROM merek");
  echo "ID\tNama Merek\tDeskripsi\n";
  while ($row = mysqli_fetch_assoc($result)) {
    echo "{$row['id_merek']}\t{$row['nama_merek']}\t{$row['deskripsi']}\n";
  }
  exit();
}

// Ambil data merek
$merek = mysqli_query($conn, "SELECT * FROM merek ORDER BY id_merek DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Merek</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f6fb;
      margin: 0;
      display: flex;
      color: #333;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
      width: calc(100% - 250px);
      min-height: 100vh;
      background-color: #f8faff;
    }

    h1 {
      background: linear-gradient(to right, #3f51b5, #5a77ff);
      color: white;
      padding: 12px 20px;
      border-radius: 10px;
      width: fit-content;
      font-size: 22px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    form {
      background: #fff;
      padding: 20px;
      margin-top: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.08);
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    form input[type="text"] {
      padding: 10px 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      flex: 1;
      min-width: 200px;
    }

    form button {
      background-color: #4caf50;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 500;
      transition: 0.3s;
    }

    form button:hover {
      background-color: #388e3c;
    }

    .btn-export {
      display: inline-block;
      background-color: #1d6f42;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 8px;
      font-weight: 500;
      transition: 0.3s;
      margin-top: 15px;
    }

    .btn-export i {
      margin-right: 6px;
    }

    .btn-export:hover {
      background-color: #145a33;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      margin-top: 20px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    thead th {
      background-color: #3f51b5;
      color: white;
      text-transform: uppercase;
      padding: 12px 15px;
    }

    tbody td {
      padding: 12px 15px;
      border-bottom: 1px solid #e0e0e0;
    }

    tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tbody tr:hover {
      background-color: #eef3ff;
    }

    td a {
      text-decoration: none;
      font-weight: 500;
      padding: 6px 12px;
      border-radius: 6px;
      margin-right: 5px;
      transition: 0.3s;
    }

    td a[href*="edit"] {
      background-color: #ffc107;
      color: #333;
    }

    td a[href*="edit"]:hover {
      background-color: #e0a800;
    }

    td a[href*="hapus"] {
      background-color: #dc3545;
      color: white;
    }

    td a[href*="hapus"]:hover {
      background-color: #b02a37;
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <h1>Data Merek</h1>

    <form method="POST">
      <input type="text" name="nama_merek" placeholder="Nama Merek" required>
      <input type="text" name="deskripsi" placeholder="Deskripsi">
      <button type="submit" name="tambah">Tambah</button>
    </form>

    <a href="merek.php?export=1" class="btn-export"><i class="fa-solid fa-file-excel"></i> Export Excel</a>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Merek</th>
          <th>Deskripsi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; while ($row = mysqli_fetch_assoc($merek)): ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['nama_merek']); ?></td>
          <td><?= htmlspecialchars($row['deskripsi']); ?></td>
          <td>
            <a href="edit_merek.php?id=<?= $row['id_merek']; ?>">Edit</a>
            <a href="merek.php?hapus=<?= $row['id_merek']; ?>" onclick="return confirm('Hapus merek ini?')">Hapus</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
