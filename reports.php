<?php
session_start();
include 'db.php';
include 'sidebar.php';

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
      background: linear-gradient(135deg, #eaf1ff, #f8fbff); /* Consistent background */
      margin: 0;
      display: flex;
      color: #333; /* Consistent text color */
    }

    .main-content {
      margin-left: 260px; /* Adjusted to match products page */
      padding: 40px;
      width: calc(100% - 260px); /* Adjusted to match products page */
      animation: fadeIn 0.7s ease; /* Added fade-in animation */
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .header-box {
      background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
      backdrop-filter: blur(10px);
      border-radius: 15px; /* Consistent rounded corners */
      padding: 20px 25px;
      margin-bottom: 25px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Consistent shadow */
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #f8f9faff; /* Consistent text color */
    }

    .header-box h1 {
      color: #f8fafcff; /* Consistent header color */
      margin: 0;
      font-size: 26px;
      font-weight: 700; /* Make the font bolder */
    }

    .filter-form {
      background: rgba(255,255,255,0.8); /* Adjusted background with blur */
      backdrop-filter: blur(12px);
      border-radius: 15px; /* Consistent rounded corners */
      padding: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Consistent shadow */
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      border-left: 6px solid #2f639b; /* Added border */
    }

    .filter-form input {
      padding: 10px; /* Increased padding for better appearance */
      border-radius: 10px; /* Consistent rounded corners */
      border: 1px solid #ccc;
      transition: border-color 0.3s ease; /* Added transition for focus effect */
    }

    .filter-form input:focus {
      border-color: #2f639b; /* Change border color on focus */
      outline: none; /* Remove default focus outline */
      box-shadow: 0 0 5px rgba(47, 99, 155, 0.3); /* Add a subtle shadow on focus */
    }

    .filter-form button {
     background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff);
      color: white;
      border: none;
      padding: 10px 20px; /* Increased padding to match products page */
      border-radius: 10px; /* Consistent rounded corners */
      cursor: pointer;
      font-weight: 600; /* Make the font bolder */
      transition: background-color 0.3s ease; /* Smooth transition for hover effect */
    }

    .filter-form button:hover {
      background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff);
      transform: scale(1.05); /* Added scale effect for better feedback */
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
      backdrop-filter: blur(12px);
      border-radius: 15px; /* Consistent rounded corners */
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Consistent shadow */
      border-left: 6px solid #2f639b; /* Added border */
    }

    th, td {
      padding: 15px; /* Increased padding for better readability */
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff); /* Consistent gradient */
      color: white;
      font-weight: 600; /* Make the font bolder */
    }

    tr:nth-child(even) {
      background: #f3f7ff; /* Consistent background color for even rows */
    }

    tbody tr:hover {
      background: #e2edff;
      transition: all 0.3s ease;
    }

    .total {
      text-align: right;
      margin-top: 15px;
      font-size: 18px;
      font-weight: bold;
      color: #2f639b; /* Consistent color */
    }

    .print-btn {
      background: linear-gradient(90deg, #28a745, #3cb371); /* Green gradient for print button */
      color: white;
      padding: 10px 20px; /* Adjusted padding to match products page */
      border-radius: 10px; /* Consistent rounded corners */
      border: none;
      cursor: pointer;
      font-weight: 600; /* Make the font bolder */
      transition: background-color 0.3s ease; /* Smooth transition for hover effect */
    }

    .print-btn:hover {
      background: linear-gradient(90deg, #3cb371, #28a745); /* Adjusted gradient on hover */
      transform: scale(1.05); /* Added scale effect for better feedback */
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
        <th>No</th>
        <th>Tanggal</th>
        <th>Total Harga</th>
        <th>Kasir</th>
      </tr>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php $no = 1; // INISIALISASI COUNTER ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
                        <td><?= $no++; ?></td>
            <td><?= date("d-m-Y", strtotime($row['tanggal'])); ?></td>
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
