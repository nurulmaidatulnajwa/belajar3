<?php
include 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}

// Ambil data produk dari database
$result = mysqli_query($conn, "SELECT * FROM produk");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orders - Toko Elektronik</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      display: flex;
    }

    /* MAIN CONTENT */
    .main-content {
      margin-left: 250px;
      padding: 40px;
      width: calc(100% - 250px);
    }

    /* --- HEADER BOX (judul + logout) --- */
    .header-box {
      background: white;
      border-radius: 12px;
      padding: 20px 30px;
      margin-bottom: 25px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-box h1 {
      margin: 0;
      font-size: 28px;
      color: #007bff;
      font-weight: bold;
    }

    .logout-btn {
      background-color: #dc3545;
      color: white;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .logout-btn:hover {
      background-color: #c82333;
    }

    /* --- KOTAK TABEL PRODUK --- */
    .card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      width: 100%;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
      font-size: 15px;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .btn-order {
      background-color: #28a745;
      color: white;
      padding: 6px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn-order:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Konten Utama -->
  <div class="main-content">

    <!-- Header (judul + logout dalam satu kotak) -->
    <div class="header-box">
      <h1>Daftar Barang yang Dijual</h1>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- Kotak daftar barang -->
    <div class="card">
      <table>
        <tr>
          <th>ID Produk</th>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Tanggal Update</th>
          <th>Aksi</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $row['id_produk'] ?></td>
            <td><?= $row['nama_produk'] ?></td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td><?= $row['stok'] ?></td>
            <td><?= $row['tanggal_update'] ?></td>
            <td>
              <form method="POST" action="tambah_order.php">
                <input type="hidden" name="id_produk" value="<?= $row['id_produk'] ?>">
                <input type="submit" class="btn-order" value="Tambah ke Order">
              </form>
            </td>
          </tr>
        <?php } ?>
      </table>
    </div>
  </div>

</body>
</html>
