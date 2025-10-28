<?php
session_start();
include 'db.php';

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location: login.php");
  exit();
}

$id_karyawan = $_SESSION['id_karyawan'];
$nama_karyawan = $_SESSION['nama_karyawan'];

// Filter tanggal (jika ada)
$tanggal_awal = $_GET['tanggal_awal'] ?? '';
$tanggal_akhir = $_GET['tanggal_akhir'] ?? '';

$where = '';
if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
  $where = "WHERE p.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
}

// Ambil data penjualan
$query = "
  SELECT p.*, k.nama_karyawan 
  FROM penjualan p
  JOIN karyawan k ON p.id_karyawan = k.id_karyawan
  $where
  ORDER BY p.tanggal DESC
";
$result = mysqli_query($conn, $query);

// Hitung total pendapatan
$total_query = mysqli_query($conn, "
  SELECT SUM(total_harga) AS total 
  FROM penjualan p
  $where
");
$total = mysqli_fetch_assoc($total_query)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan - MiNa Techno Solution</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f6f9;
      margin: 0;
      display: flex;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
      width: calc(100% - 250px);
    }

    .header-box {
      background: white;
      border-radius: 12px;
      padding: 20px 25px;
      margin-bottom: 25px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-box h1 {
      color: #007bff;
      margin: 0;
      font-size: 26px;
    }

    .filter-form {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .filter-form input {
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .filter-form button {
      background: #007bff;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }

    .filter-form button:hover {
      background: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }

    .total {
      text-align: right;
      margin-top: 15px;
      font-size: 18px;
      font-weight: bold;
      color: #007bff;
    }

    .print-btn {
      background: #28a745;
      color: white;
      padding: 8px 14px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      font-weight: bold;
      margin-top: 10px;
    }

    .print-btn:hover {
      background: #218838;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="header-box">
      <h1>Laporan Penjualan</h1>
      <p>Kasir: <strong><?= htmlspecialchars($nama_karyawan); ?></strong></p>
    </div>

    <form class="filter-form" method="GET">
      <label>Dari:</label>
      <input type="date" name="tanggal_awal" value="<?= $tanggal_awal; ?>">
      <label>Sampai:</label>
      <input type="date" name="tanggal_akhir" value="<?= $tanggal_akhir; ?>">
      <button type="submit">Tampilkan</button>
      <button type="button" class="print-btn" onclick="window.print()">Cetak Laporan</button>
    </form>

    <table>
      <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Total Harga</th>
        <th>Kasir</th>
      </tr>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['tanggal']; ?></td>
            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
            <td><?= $row['nama_karyawan']; ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">Tidak ada data penjualan.</td></tr>
      <?php endif; ?>
    </table>

    <p class="total">Total Pendapatan: <strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong></p>
  </div>
</body>
</html>
