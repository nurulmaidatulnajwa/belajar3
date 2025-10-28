<?php
include 'db.php';
session_start();
// Cek login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}

// Fungsi untuk merapikan ID produk
function renumberProductIDs($conn, $tableName = 'produk') {
    $sql = "SELECT id_produk FROM $tableName ORDER BY id_produk ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['id_produk'];
        }

        $newID = 1;
        foreach ($ids as $oldID) {
            $updateSQL = "UPDATE $tableName SET id_produk = $newID WHERE id_produk = $oldID";
            $conn->query($updateSQL);
            $newID++;
        }
    }
}

// ---- Hapus Produk ----
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM produk WHERE id_produk = '$id'");
    renumberProductIDs($conn, 'produk');
    header("Location: products.php");
    exit();
}

// ---- Tambah Produk ----
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];
    $tanggal = date('Y-m-d');

    $query = "INSERT INTO produk (nama_produk, harga, stok, tanggal_update)
              VALUES ('$nama', '$harga', '$stok', '$tanggal')";
    mysqli_query($conn, $query);
    renumberProductIDs($conn, 'produk');
    header("Location: products.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products - Toko Elektronik</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            background-color: #f4f6f9;
        }

        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 220px;
            height: 100vh;
            background: #007bff;
            color: white;
            padding: 20px;
        }

        .logo-container {
            text-align: center;
            padding: 20px 0;
        }

        .logo {
            width: 120px;
            height: auto;
            border-radius: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar ul li a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background-color: #0056b3;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: 100%;
        }

        /* Topbar bagian atas */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            color: #007bff;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background-color: #b02a37;
        }

        /* Form tambah produk */
        .form-tambah {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 915px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-tambah input {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-tambah button {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-tambah button:hover {
            background: #218838;
        }

        .aksi a {
            margin-right: 8px;
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .edit { background-color: #ffc107; }
        .hapus { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-container">
        <img src="nm.png" class="logo">
    </div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="products.php" class="active">Products</a></li>
        <li><a href="orders.php">Orders</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="penjualan.php">Penjualan</a></li>
    </ul>
</div>

<div class="main-content">
    <!-- Tambahkan topbar -->
    <div class="topbar">
        <h1>Daftar Produk</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- Form Tambah Produk -->
    <div class="form-tambah">
        <form method="POST" action="">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" required>

            <label>Harga</label>
            <input type="number" name="harga" required>

            <label>Stok</label>
            <input type="number" name="stok" required>

            <button type="submit" name="tambah">Tambah Produk</button>
        </form>
    </div>

    <!-- Tabel Produk -->
    <table border="1">
        <tr>
            <th>ID Produk</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Tanggal Update</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['id_produk'] ?></td>
                <td><?= $row['nama_produk'] ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                <td><?= $row['stok'] ?></td>
                <td><?= $row['tanggal_update'] ?></td>
                <td class="aksi">
                    <a href="edit_produk.php?id=<?= $row['id_produk'] ?>" class="edit">Edit</a>
                    <a href="products.php?hapus=<?= $row['id_produk'] ?>" class="hapus"
                       onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
