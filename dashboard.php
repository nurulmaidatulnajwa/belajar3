<?php
include 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location:login.php");
  exit();
}

$id_karyawan = $_SESSION['id_karyawan'];
$nama_karyawan = $_SESSION['nama_karyawan'];

// Hitung total produk, order, dan karyawan
$total_produk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM produk"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'] ?? 0;
$total_karyawan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM karyawan"))['total'] ?? 0;

// Ambil produk terbaru
$produk_terbaru = mysqli_query($conn, "SELECT * FROM produk ORDER BY tanggal_update DESC LIMIT 5");

// Ambil data penjualan per bulan (grafik)
$penjualan_data = array_fill(1, 12, 0); // isi default 0 untuk 12 bulan
$query_penjualan = mysqli_query($conn, "SELECT MONTH(tanggal) AS bulan, SUM(total_harga) AS total FROM penjualan GROUP BY MONTH(tanggal)");
while ($row = mysqli_fetch_assoc($query_penjualan)) {
  $penjualan_data[(int)$row['bulan']] = (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - MiNa Techno Solution</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    /* HEADER */
    .header-box {
      background: white;
      border-radius: 12px;
      padding: 25px 30px;
      margin-bottom: 30px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-left h1 {
      margin: 0;
      color: #007bff;
      font-size: 34px;
      font-weight: 700;
      line-height: 1.2;
    }

    .header-left p {
      margin-top: 8px;
      color: #555;
      font-size: 14px;
      font-weight: 400;
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

    /* CARD */
    .cards {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 30px;
    }

    .card {
      background: white;
      border-radius: 12px;
      padding: 25px;
      flex: 1;
      text-align: center;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card h3 {
      margin: 0;
      color: #333;
      font-size: 18px;
    }

    .card p {
      font-size: 26px;
      font-weight: bold;
      color: #007bff;
      margin-top: 10px;
    }

    /* GRAFIK */
    .chart-container {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .chart-container h2 {
      margin-bottom: 15px;
      color: #007bff;
      font-size: 22px;
    }

    /* PRODUK TERBARU */
    .produk-terbaru {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }

    .produk-terbaru h2 {
      margin-bottom: 15px;
      color: #007bff;
      font-size: 22px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    th {
      background-color: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">

    <!-- HEADER DENGAN NAMA KARYAWAN -->
    <div class="header-box">
      <div class="header-left">
        <h1>Dashboard</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($nama_karyawan); ?></strong> 👋<br>
        di <strong>Toko MiNa Techno</strong></p>
      </div>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- KARTU RINGKASAN -->
    <div class="cards">
      <div class="card">
        <h3>Total Produk</h3>
        <p><?= $total_produk ?></p>
      </div>
      <div class="card">
        <h3>Total Orders</h3>
        <p><?= $total_orders ?></p>
      </div>
      <div class="card">
        <h3>Jumlah Karyawan</h3>
        <p><?= $total_karyawan ?></p>
      </div>
    </div>

    <!-- GRAFIK PENJUALAN -->
    <div class="chart-container">
      <h2>Grafik Penjualan Bulanan</h2>
      <canvas id="salesChart"></canvas>
    </div>

    <!-- PRODUK TERBARU -->
    <div class="produk-terbaru">
      <h2>Produk Terbaru</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>Nama Produk</th>
          <th>Harga</th>
          <th>Stok</th>
          <th>Tanggal Update</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($produk_terbaru)) { ?>
        <tr>
          <td><?= $row['id_produk'] ?></td>
          <td><?= $row['nama_produk'] ?></td>
          <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
          <td><?= $row['stok'] ?></td>
          <td><?= $row['tanggal_update'] ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>

  </div>

  <!-- GRAFIK PENJUALAN SCRIPT -->
  <script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
      datasets: [{
        label: 'Penjualan (Rp)',
        data: [<?= implode(',', $penjualan_data) ?>],
        backgroundColor: 'rgba(0, 102, 255, 0.9)',
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
  </script>

</body>
</html>
