<?php
session_start();
include 'db.php';
include 'sidebar.php';

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location: login.php");
  exit();
}

$nama_karyawan = $_SESSION['nama_karyawan'];

// Ambil data produk & supplier untuk dropdown
$produk_query = mysqli_query($conn, "
  SELECT 
    p.id_produk, 
    p.nama_produk, 
    m.nama_merek AS merek, 
    p.harga, 
    p.stok
  FROM produk p
  LEFT JOIN merek m ON p.id_merek = m.id_merek
");

$supplier_query = mysqli_query($conn, "SELECT id_supplier, nama_supplier FROM supplier");

// Proses form restok
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_produk = $_POST['id_produk'];
  $id_supplier = $_POST['id_supplier'];
  $jumlah = (int) $_POST['jumlah'];
  $harga_per_item = (float) $_POST['harga_per_item'];
  $total_harga = (float) $_POST['total_harga'];
  $tanggal = $_POST['tanggal'];
  $tanggal_db = date("Y-m-d", strtotime($tanggal)); // Format ke YYYY-MM-DD untuk database

  // Simpan ke tabel orders
  $insert = "INSERT INTO orders (id_produk, id_supplier, jumlah, harga_per_item, total_harga, tanggal)
             VALUES ('$id_produk', '$id_supplier', '$jumlah', '$harga_per_item', '$total_harga', '$tanggal_db')";
  mysqli_query($conn, $insert);

  // Tambah stok produk
  $update_stok = "UPDATE produk SET stok = stok + $jumlah WHERE id_produk = '$id_produk'";
  mysqli_query($conn, $update_stok);

  echo "<script>alert('Restok berhasil disimpan dan stok produk diperbarui!'); window.location='orders.php';</script>";
  exit();
}

// Ambil semua data orders (QUERY INI SUDAH DIPERBAIKI)
$result = mysqli_query($conn, "
  SELECT
    o.id_order,
    p.nama_produk,
    m.nama_merek AS merek,       -- *PERBAIKAN 2A: Mengambil nama merek dari tabel merek*
    o.jumlah,
    o.harga_per_item,
    o.total_harga,
    o.tanggal,
    s.nama_supplier  
  FROM
    orders o
  JOIN
    produk p ON o.id_produk = p.id_produk
  LEFT JOIN                       -- *PERBAIKAN 2B: Tambah JOIN ke tabel merek*
    merek m ON p.id_merek = m.id_merek
  JOIN
    supplier s ON o.id_supplier = s.id_supplier
  ORDER BY
    o.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Restok Barang - MiNa Techno Solution</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #eaf1ff, #f8fbff);
      margin: 0;
      display: flex;
      color: #333;
    }

    .main-content {
      margin-left: 260px;
      padding: 40px;
      width: calc(100% - 260px);
    }

    .form-box {
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      border-left: 6px solid #2f639b;
    }

    h2 {
      color: #2f639b;
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin: 10px 0 5px;
      font-weight: 600;
    }

    select, input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    button {
      background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    button:hover {
      transform: scale(1.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    thead th {
      background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
      color: white;
    }

    tr:nth-child(even) {
      background: #f3f7ff;
    }

    tr:hover {
      background: #e2edff;
    }
  </style>
</head>
<body>

  <?php include 'sidebar.php'; ?>

  <div class="main-content">
    <div class="form-box">
      <h2>Form Restok Barang</h2>
      <form method="POST">
        <label>Tanggal:</label>
        <input type="date" name="tanggal" required>

        <label>Nama Produk:</label>
        <select name="id_produk" id="produk" required>
          <option value="">-- Pilih Produk --</option>
          <?php while ($p = mysqli_fetch_assoc($produk_query)) { ?>
            <option 
              value="<?= $p['id_produk']; ?>" 
              data-harga="<?= $p['harga']; ?>" 
              data-merek="<?= htmlspecialchars($p['merek']); ?>">
              <?= htmlspecialchars($p['nama_produk']); ?> (Stok: <?= $p['stok']; ?>)
            </option>
          <?php } ?>
        </select>

        <label>Merek:</label>
        <input type="text" id="merek" name="merek" readonly>

        <label>Supplier:</label>
        <select name="id_supplier" required>
          <option value="">-- Pilih Supplier --</option>
          <?php while ($s = mysqli_fetch_assoc($supplier_query)) { ?>
            <option value="<?= $s['id_supplier']; ?>"><?= htmlspecialchars($s['nama_supplier']); ?></option>
          <?php } ?>
        </select>

        <label>Jumlah:</label>
        <input type="number" name="jumlah" id="jumlah" min="1" required>

        <label>Harga per Item:</label>
        <input type="number" name="harga_per_item" id="harga_per_item" readonly>

        <label>Total Harga:</label>
        <input type="number" name="total_harga" id="total_harga" readonly>

        <button type="submit">Simpan Restok</button>
      </form>
    </div>

    <h2>Riwayat Restok Barang</h2>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>supplier</th>
          <th>Nama Produk</th>
          <th>Merek</th>
          <th>Jumlah</th>
          <th>Harga per Item</th>
          <th>Total Harga</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        // *PERBAIKAN 1A: Inisialisasi counter nomor urut*
        $no = 1; 
        while ($row = mysqli_fetch_assoc($result)) { 
        ?>
          <tr>
                        <td><?= $no++; ?></td> 
            <td><?= htmlspecialchars($row['nama_supplier']) ?></td> 
            <td><?= htmlspecialchars($row['nama_produk']); ?></td>
            <td><?= htmlspecialchars($row['merek']); ?></td> 
            <td><?= $row['jumlah']; ?></td>
            <td><?= number_format($row['harga_per_item']); ?></td>
            <td><?= number_format($row['total_harga']); ?></td>
            <td><?= date("d-m-Y", strtotime($row['tanggal'])); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <script>
  const produkSelect = document.getElementById('produk');
  const merekInput = document.getElementById('merek');
  const jumlahInput = document.getElementById('jumlah');
  const hargaInput = document.getElementById('harga_per_item');
  const totalInput = document.getElementById('total_harga');

  function hitungTotal() {
    const selected = produkSelect.options[produkSelect.selectedIndex];

    if (!selected || selected.value === "") {
      merekInput.value = "";
      hargaInput.value = "";
      totalInput.value = "";
      return;
    }

    const harga = parseFloat(selected.getAttribute('data-harga')) || 0;
    const merek = selected.getAttribute('data-merek') || '';
    const jumlah = parseInt(jumlahInput.value || 0);

    merekInput.value = merek; // ✅ otomatis isi merek
    hargaInput.value = harga;
    totalInput.value = harga * jumlah;
  }

  produkSelect.addEventListener('change', hitungTotal);
  jumlahInput.addEventListener('input', hitungTotal);
</script>


</body>
</html>