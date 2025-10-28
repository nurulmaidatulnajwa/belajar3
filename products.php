<?php
include 'db.php';
session_start();
// Cek login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}

// Fungsi merapikan ID produk
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
    // hapus gambar dari folder
    $gambar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM produk WHERE id_produk='$id'"))['gambar'];
    if ($gambar && file_exists("uploads/$gambar")) {
        unlink("uploads/$gambar");
    }
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
    $gambar = '';

    // Proses upload gambar
    if (!empty($_FILES['gambar']['name'])) {
        $targetDir = "uploads/";
        $gambar = basename($_FILES["gambar"]["name"]);
        $targetFilePath = $targetDir . $gambar;

        // Validasi tipe file
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($fileType, $allowTypes)) {
            move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath);
        } else {
            echo "<script>alert('Hanya file JPG, JPEG, PNG, atau GIF yang diperbolehkan!');</script>";
        }
    }

    $query = "INSERT INTO produk (nama_produk, harga, stok, tanggal_update, gambar)
              VALUES ('$nama', '$harga', '$stok', '$tanggal', '$gambar')";
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
            border-radius: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
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

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #007bff;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #b02a37;
        }

        .form-tambah {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 920px;
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

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
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

        img.produk-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
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
    <div class="topbar">
        <h1>Daftar Produk</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- Form Tambah Produk -->
    <div class="form-tambah">
        <form method="POST" action="" enctype="multipart/form-data">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" required>

            <label>Harga</label>
            <input type="number" name="harga" required>

            <label>Stok</label>
            <input type="number" name="stok" required>

            <label>Foto Produk</label>
            <input type="file" name="gambar" accept="image/*">

            <button type="submit" name="tambah">Tambah Produk</button>
        </form>
    </div>

    <!-- Tabel Produk -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Gambar</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Tanggal Update</th>
            <th>Aksi</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['id_produk'] ?></td>
                <td><?= $row['nama_produk'] ?></td>
                <td>
                    <?php if (!empty($row['gambar'])) { ?>
                        <img src="uploads/<?= $row['gambar'] ?>" class="produk-img">
                    <?php } else { ?>
                        <span style="color:#aaa;">(tidak ada gambar)</span>
                    <?php } ?>
                </td>
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
