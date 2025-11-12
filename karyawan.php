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

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$query = "SELECT * FROM karyawan WHERE nama_karyawan LIKE '%$cari%' OR username LIKE '%$cari%' ORDER BY id_karyawan DESC";
$result = mysqli_query($conn, $query);
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Karyawan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: linear-gradient(to bottom right, #f0f4ff, #ffffff);
    }

    /* ===== Sidebar ===== */
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
      padding: 0 10px;
    }

    .menu {
      list-style: none;
      padding: 0;
      margin: 0;
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

    /* ===== Main Content ===== */
    .main-content {
      margin-left: 260px;
      padding: 30px;
      transition: 0.3s;
    }

    .main-content h1 {
      margin-bottom: 20px;
      color: #2a5298;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      width: fit-content;
    }

    .search-box {
      margin-bottom: 20px;
    }

    .search-box input {
      padding: 8px;
      width: 220px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .search-box button {
      padding: 8px 14px;
      background: #2a5298;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #2a5298;
      color: white;
    }

    .btn-edit {
      background-color: #f1c40f;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
      margin-right: 4px;
      transition: 0.3s;
    }

    .btn-edit:hover {
      background-color: #d4ac0d;
    }

    .btn-hapus {
      background-color: #e74c3c;
      color: white;
      padding: 6px 12px;
      border-radius: 6px;
      text-decoration: none;
      transition: 0.3s;
    }

    .btn-hapus:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>
     <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Data Karyawan</h1>
    <div class="card">
      <form class="search-box" method="GET">
        <input type="text" name="cari" placeholder="Cari karyawan..." value="<?= htmlspecialchars($cari); ?>">
        <button type="submit">Cari</button>
      </form>

      <table>
        <tr>
          <th>No</th>
          <th>Nama Karyawan</th>
          <th>Username</th>
          <th>Jabatan</th>
          <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama_karyawan']}</td>
            <td>{$row['username']}</td>
            <td>{$row['jabatan']}</td>
            <td>
              <a href='edit_karyawan.php?id={$row['id_karyawan']}' class='btn-edit'>Edit</a>
              <a href='hapus_karyawan.php?id={$row['id_karyawan']}' class='btn-hapus' onclick=\"return confirm('Yakin ingin menghapus data ini?')\">Hapus</a>
            </td>
          </tr>";
          $no++;
        }
        ?>
      </table>
    </div>
  </div>
</body>
</html>
