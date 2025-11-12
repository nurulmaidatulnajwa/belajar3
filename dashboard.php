<?php
include 'db.php';
session_start();

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

// Produk terbaru
$produk_terbaru = mysqli_query($conn, "SELECT * FROM produk ORDER BY tanggal_update DESC LIMIT 5");

// Data penjualan per bulan
$penjualan_data = array_fill(1, 12, 0);
$query_penjualan = mysqli_query($conn, "
  SELECT MONTH(tanggal) AS bulan, SUM(jumlah * harga_per_item) AS total 
  FROM orders 
  GROUP BY MONTH(tanggal)
");
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
      margin: 0;
      display: flex;
      color: #333;
      background: linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%);
      background-attachment: fixed;
      background-image:
        radial-gradient(circle at 20% 20%, rgba(255,255,255,0.3) 0, transparent 40%),
        radial-gradient(circle at 80% 80%, rgba(255,255,255,0.25) 0, transparent 40%);
    }

    .main-content {
      margin-left: 260px;
      padding: 40px;
      width: calc(100% - 260px);
      animation: fadeIn 0.7s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* === HEADER === */
    .header-box {
      background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }

    .header-left h1 {
      margin: 0;
      font-size: 34px;
      font-weight: 700;
      line-height: 1.2;
    }

    .header-left p {
      margin-top: 8px;
      font-size: 14px;
      font-weight: 400;
      color: #f0f0f0;
    }

    /* === USER PROFILE DROPDOWN === */
    .user-profile {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    .user-icon {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #fff;
      padding: 5px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      transition: transform 0.2s ease;
    }

    .user-icon:hover { transform: scale(1.05); }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: calc(100% + 12px);
      background: #fff;
      min-width: 180px;
      border-radius: 10px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
      padding: 12px;
      z-index: 9999;
      text-align: center;
    }

    .dropdown-content p {
      margin: 6px 0;
      color: #222;
    }

    .dropdown-content .logout-btn {
      display: inline-block;
      background-color: #ff416c;
      color: white;
      padding: 7px 14px;
      text-decoration: none;
      border-radius: 8px;
      margin-top: 10px;
      font-weight: 500;
      font-size: 0.9em;
    }

    .dropdown-content .logout-btn:hover {
      background-color: #d93b56;
    }

    .dropdown-show {
      display: block !important;
    }

    /* === CARDS === */
    .cards {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }

    .card {
      flex: 1;
      min-width: 200px;
      background: linear-gradient(145deg, #ffffff, #e9eff8);
      border-radius: 15px;
      padding: 25px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      border-top: 4px solid #5b86e5;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(91,134,229,0.3);
    }

    .card h3 {
      margin: 0;
      color: #2f4873;
      font-size: 18px;
    }

    .card p {
      font-size: 28px;
      font-weight: bold;
      color: #1d2d50;
      margin-top: 10px;
    }

    /* === CHART === */
    .chart-container {
      background: rgba(255,255,255,0.9);
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      border-left: 6px solid #5b86e5;
    }

    .chart-container h2 {
      margin-bottom: 15px;
      color: #2f4873;
      font-size: 22px;
    }

    /* === PRODUK TERBARU === */
    .produk-terbaru {
      background: rgba(255,255,255,0.9);
      border-radius: 15px;
      padding: 25px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      border-left: 6px solid #5b86e5;
    }

    .produk-terbaru h2 {
      margin-bottom: 15px;
      color: #2f4873;
      font-size: 22px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    thead th {
      background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
      color: #fff;
      font-weight: 600;
      padding: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    tbody td {
      padding: 14px;
      text-align: center;
      border-bottom: 1px solid #e5e9f2;
      color: #333;
    }

    tbody tr:nth-child(even) {
      background: #f5f8ff;
    }

    tbody tr:hover {
      background: #e3f2fd;
      transition: all 0.3s ease;
    }
  </style>
</head>

<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">

    <div class="header-box">
      <div class="header-left">
        <h1>Dashboard</h1>
        
      </div>

      <!-- User Profile Dropdown -->
      <div class="user-profile" id="userMenu">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" class="user-icon" onclick="toggleDropdown()">
        <div id="userDropdown" class="dropdown-content">
          <p><strong><?= htmlspecialchars($nama_karyawan); ?></strong></p>
          <p style="font-size: 0.9em; color: #888;"><?= htmlspecialchars($_SESSION['jabatan']); ?></p>
          <a href="logout.php" class="logout-btn">Logout</a>
        </div>
      </div>
    </div>

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

    <div class="chart-container">
      <h2>Grafik Penjualan Bulanan</h2>
      <canvas id="salesChart"></canvas>
    </div>

    <div class="produk-terbaru">
      <h2>Produk Terbaru</h2>
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Tanggal Update</th>
          </tr>
        </thead>
        <tbody>
          <?php 
$no = 1; // mulai nomor dari 1
while ($row = mysqli_fetch_assoc($produk_terbaru)) { 
?>
<tr>
  <td><?= $no++; ?></td> <!-- nomor urut otomatis -->
  <td><?= $row['nama_produk'] ?></td>
  <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
  <td><?= $row['stok'] ?></td>
  <td><?= date("d-m-Y", strtotime($row['tanggal_update'])); ?></td>
</tr>
<?php } ?>

        </tbody>
      </table>
    </div>
  </div>

  <script>
  // === Dropdown Function ===
  function toggleDropdown() {
    const dd = document.getElementById('userDropdown');
    dd.classList.toggle('dropdown-show');
  }

  // Tutup dropdown kalau klik di luar
  window.addEventListener('click', function(e) {
    const menu = document.getElementById('userMenu');
    const dd = document.getElementById('userDropdown');
    if (!menu.contains(e.target)) {
      dd.classList.remove('dropdown-show');
    }
  });

  // === Chart.js ===
  const ctx = document.getElementById('salesChart').getContext('2d');
  new Chart(ctx, {
    data: {
      labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
      datasets: [
        {
          type: 'bar',
          label: 'Penjualan (Rp)',
          data: [<?= implode(',', $penjualan_data) ?>],
          backgroundColor: 'rgba(91,134,229,0.8)',
          borderRadius: 6,
          yAxisID: 'y',
        },
        {
          type: 'line',
          label: 'Tren Penjualan',
          data: [<?= implode(',', $penjualan_data) ?>],
          borderColor: '#ff4b2b',
          backgroundColor: 'rgba(255,75,43,0.15)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#ff4b2b',
          pointRadius: 5,
          borderWidth: 3,
          yAxisID: 'y',
        }
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Total Penjualan (Rp)' }
        }
      },
      plugins: {
        legend: {
          display: true,
          labels: { font: { size: 13, family: 'Poppins' } }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Rp ' + context.formattedValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
          }
        }
      }
    }
  });
  </script>
</body>
</html>
