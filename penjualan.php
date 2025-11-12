<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); 
include 'db.php';
include 'sidebar.php';

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}

$nama_karyawan = $_SESSION['nama_karyawan'];

// Ambil daftar produk + merek
$produk_query = mysqli_query($conn, "
    SELECT p.id_produk, p.nama_produk, p.harga, p.stok, m.nama_merek AS merek 
    FROM produk p
    LEFT JOIN merek m ON p.id_merek = m.id_merek
");

// ========== PROSES SIMPAN PENJUALAN ==========
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']); 
    $id_karyawan = $_SESSION['id_karyawan'];
    $jam_sekarang = date("H:i:s"); 

    $id_produk_array = $_POST['id_produk'];
    $jumlah_array = $_POST['jumlah'];
    $harga_array = $_POST['harga_per_item'];
    $total_array = $_POST['total_harga_item'];

    $total_harga_keseluruhan = array_sum($total_array);

    // SIMPAN KE TABEL PENJUALAN (MASTER)
    $query_penjualan = "INSERT INTO penjualan (tanggal, jam, nama_customer, total_harga, id_karyawan)
                        VALUES ('$tanggal', '$jam_sekarang', '$nama_customer', '$total_harga_keseluruhan', '$id_karyawan')";
    mysqli_query($conn, $query_penjualan);
    $id_penjualan_baru = mysqli_insert_id($conn);

    // SIMPAN KE PENJUALAN_DETAIL + UPDATE STOK
    for ($i = 0; $i < count($id_produk_array); $i++) {
        $id_produk = $id_produk_array[$i];
        $jumlah = (int)$jumlah_array[$i];
        $harga = (float)$harga_array[$i];
        $total_item = (float)$total_array[$i];

        // Cek stok dulu
        $stok_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM produk WHERE id_produk = '$id_produk'"));
        if ($stok_data && $stok_data['stok'] >= $jumlah) {
            mysqli_query($conn, "
                INSERT INTO penjualan_detail (id_penjualan, id_produk, jumlah, total_per_item)
                VALUES ('$id_penjualan_baru', '$id_produk', '$jumlah', '$total_item')
            ");
            mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id_produk = '$id_produk'");
        } else {
            echo "<script>alert('Stok untuk salah satu produk tidak mencukupi! Transaksi dibatalkan.'); window.location='penjualan.php';</script>";
            exit();
        }
    }

    echo "<script>alert('Penjualan berhasil disimpan!'); window.location='penjualan.php';</script>";
    exit();
}

// ========== RIWAYAT PENJUALAN ==========
$result = mysqli_query($conn, "
    SELECT 
        p.id_penjualan,
        p.tanggal,
        TIME(p.jam) AS jam,
        p.nama_customer,
        p.total_harga AS total_keseluruhan,
        k.nama_karyawan,
        GROUP_CONCAT(pr.nama_produk SEPARATOR '<br>') AS produk_dibeli,
        GROUP_CONCAT(m.nama_merek SEPARATOR '<br>') AS merek_dibeli, 
        GROUP_CONCAT(pd.jumlah SEPARATOR '<br>') AS jumlah_beli,
        GROUP_CONCAT(CONCAT('Rp ', FORMAT(pd.total_per_item, 0)) SEPARATOR '<br>') AS total_per_item
    FROM penjualan p
    JOIN karyawan k ON p.id_karyawan = k.id_karyawan
    JOIN penjualan_detail pd ON p.id_penjualan = pd.id_penjualan
    JOIN produk pr ON pd.id_produk = pr.id_produk
    LEFT JOIN merek m ON pr.id_merek = m.id_merek
    GROUP BY p.id_penjualan
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
:root {
  --primary-color: #2f639b;
  --secondary-color: #f8fbff;
  --table-header-bg: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
}
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #eaf1ff, var(--secondary-color));
  display: flex;
  margin: 0;
}
.main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
.container {
  background: rgba(255,255,255,0.8); padding: 30px; border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.15); border-left: 6px solid #2f639b;
}
button {
  background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff); color: white;
  border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;
}
button:hover { transform: scale(1.03); }
.produk-item { display: flex; gap: 10px; margin-bottom: 10px; }
.produk-item select, .produk-item input { flex: 1; }
.hapus-produk { background: red; color: white; border: none; border-radius: 8px; padding: 5px 10px; cursor: pointer; }
#tambah-produk { background: #2f639b; margin-bottom: 15px; }
table { width: 100%; border-collapse: collapse; margin-top: 30px; background: white; border-radius: 10px; overflow: hidden; }
thead { background: var(--table-header-bg); color: white; }
th, td { padding: 12px; text-align: center; border-bottom: 1px solid #eee; }
</style>
</head>
<body>
<?php include 'sidebar.php';?>

<div class="main-content">
  <div class="container">
    <h2>Form Penjualan</h2>
    <p>Kasir: <strong><?= htmlspecialchars($nama_karyawan); ?></strong></p>

    <form method="POST" id="form-penjualan">
      <label>Tanggal:</label>
      <input type="date" name="tanggal" required>

      <label>Nama Customer (Opsional):</label>
      <input type="text" name="nama_customer" placeholder="Masukkan Nama Pelanggan">

      <label>Daftar Produk:</label>
      <div id="produk-list">
        <div class="produk-item">
          <select name="id_produk[]" class="id_produk" required>
            <option value="">-- Pilih Produk --</option>
            <?php 
            mysqli_data_seek($produk_query, 0);
            while ($produk = mysqli_fetch_assoc($produk_query)) { ?>
              <option value="<?= $produk['id_produk']; ?>"
                data-harga="<?= $produk['harga']; ?>"
                data-merek="<?= htmlspecialchars($produk['merek']); ?>" 
                data-stok="<?= $produk['stok']; ?>">
                <?= htmlspecialchars($produk['nama_produk']); ?> (Stok: <?= $produk['stok']; ?>)
              </option>
            <?php } ?>
          </select>
          <input type="text" class="merek" placeholder="Merek" readonly>
          <input type="number" name="jumlah[]" class="jumlah" min="1" placeholder="Jumlah" required>
          <input type="number" name="harga_per_item[]" class="harga_per_item" readonly placeholder="Harga">
          <input type="number" name="total_harga_item[]" class="total_harga_item" readonly placeholder="Total">
          <button type="button" class="hapus-produk">❌</button>
        </div>
      </div>

      <button type="button" id="tambah-produk">+ Tambah Produk</button>

      <label>Total Harga Keseluruhan:</label>
      <input type="number" name="total_harga" id="total_harga" readonly>

      <button type="submit" name="simpan">Simpan Penjualan</button>
    </form>

    <h3>Riwayat Penjualan</h3>
    <table>
      <thead>
        <tr>
          <th>No</th><th>Tanggal</th><th>Jam</th><th>Customer</th>
          <th>Produk</th><th>Merek</th><th>Jumlah</th><th>Total / Item</th><th>Total Keseluruhan</th><th>Kasir</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= date("d-m-Y", strtotime($row['tanggal'])); ?></td>
          <td><?= htmlspecialchars($row['jam']); ?></td>
          <td><?= htmlspecialchars($row['nama_customer']); ?></td>
          <td><?= $row['produk_dibeli']; ?></td>
          <td><?= $row['merek_dibeli']; ?></td>
          <td><?= $row['jumlah_beli']; ?></td>
          <td><?= $row['total_per_item']; ?></td>
          <td><b>Rp <?= number_format($row['total_keseluruhan'],0,',','.'); ?></b></td>
          <td><?= htmlspecialchars($row['nama_karyawan']); ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const produkList = document.getElementById('produk-list');
  const tambahBtn = document.getElementById('tambah-produk');

  function updateTotals() {
    let totalKeseluruhan = 0;
    produkList.querySelectorAll('.produk-item').forEach(item => {
      const select = item.querySelector('.id_produk');
      const jumlah = item.querySelector('.jumlah');
      const harga = item.querySelector('.harga_per_item');
      const total = item.querySelector('.total_harga_item');
      const merek = item.querySelector('.merek');

      const hargaValue = parseFloat(select.selectedOptions[0]?.dataset.harga || 0);
      const merekValue = select.selectedOptions[0]?.dataset.merek || '';
      const jumlahValue = parseInt(jumlah.value || 0);

      harga.value = hargaValue;
      merek.value = merekValue;
      total.value = hargaValue * jumlahValue;
      totalKeseluruhan += hargaValue * jumlahValue;
    });
    document.getElementById('total_harga').value = totalKeseluruhan;
  }

  produkList.addEventListener('input', updateTotals);
  produkList.addEventListener('change', updateTotals);

  tambahBtn.addEventListener('click', () => {
    const clone = produkList.querySelector('.produk-item').cloneNode(true);
    clone.querySelectorAll('input').forEach(i => i.value = '');
    produkList.appendChild(clone);
  });

  produkList.addEventListener('click', e => {
    if (e.target.classList.contains('hapus-produk')) {
      if (produkList.querySelectorAll('.produk-item').length > 1) {
        e.target.closest('.produk-item').remove();
        updateTotals();
      }
    }
  });
});
</script>
</body>
</html>
