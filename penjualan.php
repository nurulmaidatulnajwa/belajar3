<?php
session_start();
include 'db.php';

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}

$nama_karyawan = $_SESSION['nama_karyawan'];

// Ambil daftar produk untuk dropdown
$produk_query = mysqli_query($conn, "SELECT id_produk, nama_produk, stok FROM produk");

// Simpan penjualan baru jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah']; // jumlah produk yang dijual
    $total_harga = $_POST['total_harga'];
    $id_karyawan = $_SESSION['id_karyawan'];

    // Cek stok produk
    $cek_stok = mysqli_query($conn, "SELECT stok FROM produk WHERE id_produk = '$id_produk'");
    $data_stok = mysqli_fetch_assoc($cek_stok);

    if ($data_stok && $data_stok['stok'] >= $jumlah) {
        // Kurangi stok produk
        $stok_baru = $data_stok['stok'] - $jumlah;
        mysqli_query($conn, "UPDATE produk SET stok = '$stok_baru' WHERE id_produk = '$id_produk'");

        // Simpan ke tabel penjualan
        $query = "INSERT INTO penjualan (tanggal, id_produk, total_harga, id_karyawan) 
                  VALUES ('$tanggal', '$id_produk', '$total_harga', '$id_karyawan')";
        mysqli_query($conn, $query);

        echo "<script>alert('Penjualan berhasil disimpan dan stok diperbarui!'); window.location='penjualan.php';</script>";
    } else {
        echo "<script>alert('Stok tidak mencukupi! Penjualan dibatalkan.');</script>";
    }
}

// Ambil semua data penjualan beserta nama produk dan nama kasirnya
$result = mysqli_query($conn, "
    SELECT p.id, p.tanggal, pr.nama_produk, p.total_harga, k.nama_karyawan 
    FROM penjualan p
    JOIN produk pr ON p.id_produk = pr.id_produk
    JOIN karyawan k ON p.id_karyawan = k.id_karyawan
    ORDER BY p.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Penjualan - MiNa Techno</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      display: flex;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
      width: calc(100% - 250px);
    }

    .container {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #007bff;
    }

    label {
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin: 6px 0 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    button {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    th {
      background: #007bff;
      color: white;
    }

    tr:nth-child(even) {
      background: #f9f9f9;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="container">
      <h2>Data Penjualan</h2>
      <p>Kasir: <strong><?= htmlspecialchars($nama_karyawan); ?></strong></p>

      <form method="POST">
        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" id="tanggal" required>

        <label for="id_produk">Nama Produk:</label>
        <select name="id_produk" id="id_produk" required>
          <option value="">-- Pilih Produk --</option>
          <?php while ($produk = mysqli_fetch_assoc($produk_query)) { ?>
            <option value="<?= $produk['id_produk']; ?>">
              <?= htmlspecialchars($produk['nama_produk']); ?> (Stok: <?= $produk['stok']; ?>)
            </option>
          <?php } ?>
        </select>

        <label for="jumlah">Jumlah Terjual:</label>
        <input type="number" name="jumlah" id="jumlah" min="1" required>

        <label for="total_harga">Total Harga:</label>
        <input type="number" name="total_harga" id="total_harga" required>

        <button type="submit" name="simpan">Simpan Penjualan</button>
      </form>

      <h3>Riwayat Penjualan</h3>
      <table>
        <tr>
          <th>ID</th>
          <th>Tanggal</th>
          <th>Nama Produk</th>
          <th>Total Harga</th>
          <th>Kasir</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?= $row['id']; ?></td>
          <td><?= $row['tanggal']; ?></td>
          <td><?= htmlspecialchars($row['nama_produk']); ?></td>
          <td>Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
          <td><?= htmlspecialchars($row['nama_karyawan']); ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>

</body>
</html>
