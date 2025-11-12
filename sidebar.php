<?php
  $current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Sidebar - MiNa Techno Solution</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f1f5ff, #ffffff);
      display: flex;
    }

    /* === SIDEBAR === */
    .sidebar {
      width: 250px;
      background: linear-gradient(180deg, #2f639b, #1b3655);
      color: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      box-shadow: 4px 0 15px rgba(0,0,0,0.15);
      overflow-y: auto; /* aktifkan scroll vertikal */
      scrollbar-width: thin; /* agar scroll-nya kecil */
      scrollbar-color: #999 transparent; /* warna scroll bar */


    }

    /* === HEADER LOGO === */
    .sidebar-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 30px 0 20px;
    }

    .sidebar-logo {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
      background-color: #fff;
      box-shadow: 0 0 15px rgba(255,255,255,0.3);
      transition: transform 0.4s ease;
    }

    .sidebar-logo:hover {
      transform: rotate(10deg) scale(1.1);
    }

    .sidebar-header h2 {
      font-size: 18px;
      text-align: center;
      line-height: 1.4;
      font-weight: 600;
      color: #fff;
    }

    /* === GARIS PEMBATAS === */
    .divider {
      width: 80%;
      height: 1.5px;
      background: linear-gradient(to right, #ffffff50, #ffffff90, #ffffff50);
      margin: 20px 0;
      border-radius: 5px;
    }

    /* === MENU === */
    .sidebar-menu {
      list-style: none;
      padding: 0;
      margin: 0;
      width: 100%;
    }

    .sidebar-menu li {
      width: 100%;
    }

    .sidebar-menu a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 25px;
      color: #e4e9f2;
      text-decoration: none;
      font-size: 15px;
      font-weight: 500;
      border-left: 4px solid transparent;
      transition: all 0.3s ease;
      position: relative;
    }

    .sidebar-menu a i {
      width: 20px;
      text-align: center;
    }

    /* === HOVER & ANIMASI KLIK === */
    .sidebar-menu a:hover {
      background: rgba(255,255,255,0.15);
      color: #fff;
      border-left: 4px solid #fff;
      transform: translateX(5px);
    }

    .sidebar-menu a:active {
      transform: scale(0.95);
    }

    /* === MENU AKTIF === */
    .sidebar-menu a.active {
      background: linear-gradient(90deg, #1b3655, #2f639b);
      border-left: 4px solid #ffffff;
      color: #ffffff;
      box-shadow: inset 0 0 10px rgba(255,255,255,0.2);
    }


  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <div class="sidebar">
    <div class="sidebar-header">
      <img src="nm.png" alt="Logo" class="sidebar-logo">
      <h2>MiNa Techno<br>Solution</h2>
    </div>

    <div class="divider"></div>

    <ul class="sidebar-menu">
      <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Dashboard</a></li>
      <li><a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>"><i class="fa-solid fa-box"></i> Products</a></li>
      <li><a href="orders.php" class="<?= $current_page == 'orders.php' ? 'active' : '' ?>"><i class="fa-solid fa-cart-shopping"></i> Orders</a></li>
      <li><a href="reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Reports</a></li>
      <li><a href="penjualan.php" class="<?= $current_page == 'penjualan.php' ? 'active' : '' ?>"><i class="fa-solid fa-money-bill-wave"></i> Penjualan</a></li>
      <li><a href="supplier.php" class="<?= $current_page == 'supplier.php' ? 'active' : '' ?>"><i class="fa-solid fa-truck-field"></i> Supplier</a></li>
      <li><a href="merek.php" class="<?= $current_page == 'merek.php' ? 'active' : '' ?>"><i class="fa-solid fa-tags"></i> Merek</a></li>
      <li><a href="karyawan.php"><i class="fas fa-users"></i> <span>Data Karyawan</span></a></li>

    </ul>
  </div>


</body>
</html>
