<?php
include 'db.php';
session_start();

// Ambil data kasir (yang login)
$id_karyawan = $_SESSION['id_karyawan'];
$nama_karyawan = $_SESSION['nama_karyawan'];

// Ambil daftar produk untuk dropdown
$produk_query = mysqli_query($conn, "SELECT * FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Penjualan</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 30px; }
    h2 { text-align: center; }
    form { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background: #007bff; color: white; }
    input, select { width: 95%; padding: 5px; }
    button { background: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; margin-top: 10px; }
    button:hover { background: #218838; }
    .add-row { background: #007bff; margin-top: 10px; }
    .add-row:hover { background: #0056b3; }
    .total { font-weight: bold; text-align: right; margin-top: 10px; }
  </style>
</head>
<body>
  <h2>Tambah Penjualan</h2>
  <form action="proses_tambah_penjualan.php" method="POST">
    <label>Nama Customer:</label>
    <input type="text" name="nama_customer" required><br><br>

    <label>Nama Kasir:</label>
    <input type="text" value="<?= $nama_karyawan ?>" disabled>
    <input type="hidden" name="id_karyawan" value="<?= $id_karyawan ?>">

    <table id="produkTable">
      <thead>
        <tr>
          <th>Produk</th>
          <th>Harga</th>
          <th>Jumlah</th>
          <th>Total</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <select name="produk[]" class="produk-select" required>
              <option value="">-- Pilih Produk --</option>
              <?php while ($p = mysqli_fetch_assoc($produk_query)) { ?>
                <option value="<?= $p['id_produk'] ?>" data-harga="<?= $p['harga'] ?>">
                  <?= $p['nama_produk'] ?> (<?= $p['merek'] ?>)
                </option>
              <?php } ?>
            </select>
          </td>
          <td><input type="number" name="harga[]" class="harga" readonly></td>
          <td><input type="number" name="jumlah[]" class="jumlah" min="1" value="1"></td>
          <td><input type="number" name="total[]" class="total-item" readonly></td>
          <td><button type="button" class="hapus-row">Hapus</button></td>
        </tr>
      </tbody>
    </table>

    <button type="button" class="add-row">+ Tambah Produk</button>

    <div class="total">
      Total Keseluruhan: Rp <span id="totalKeseluruhan">0</span>
      <input type="hidden" name="total_keseluruhan" id="totalInput">
    </div>

    <button type="submit">Simpan Penjualan</button>
  </form>

  <script>
    function hitungTotal() {
      let totalKeseluruhan = 0;
      document.querySelectorAll("#produkTable tbody tr").forEach(row => {
        const harga = parseFloat(row.querySelector(".harga").value) || 0;
        const jumlah = parseInt(row.querySelector(".jumlah").value) || 0;
        const total = harga * jumlah;
        row.querySelector(".total-item").value = total;
        totalKeseluruhan += total;
      });
      document.getElementById("totalKeseluruhan").textContent = totalKeseluruhan.toLocaleString();
      document.getElementById("totalInput").value = totalKeseluruhan;
    }

    document.addEventListener("input", hitungTotal);

    document.addEventListener("change", e => {
      if (e.target.classList.contains("produk-select")) {
        const harga = e.target.options[e.target.selectedIndex].dataset.harga;
        e.target.closest("tr").querySelector(".harga").value = harga || 0;
        hitungTotal();
      }
    });

    document.querySelector(".add-row").addEventListener("click", () => {
      const newRow = document.querySelector("#produkTable tbody tr").cloneNode(true);
      newRow.querySelectorAll("input").forEach(i => i.value = "");
      document.querySelector("#produkTable tbody").appendChild(newRow);
    });

    document.addEventListener("click", e => {
      if (e.target.classList.contains("hapus-row")) {
        e.target.closest("tr").remove();
        hitungTotal();
      }
    });
  </script>
</body>
</html>
