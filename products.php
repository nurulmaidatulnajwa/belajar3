<?php
include 'db.php';
session_start();

// Ambil data merek
$merek_query = mysqli_query($conn, "SELECT * FROM merek ORDER BY nama_merek ASC");


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
            $conn->query("UPDATE $tableName SET id_produk = $newID WHERE id_produk = $oldID");
            $newID++;
        }
    }
}

// ---- Hapus Produk ----
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $gambar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM produk WHERE id_produk='$id'"))['gambar'];
    if ($gambar && file_exists("uploads/$gambar")) {
        unlink("uploads/$gambar");
    }
    mysqli_query($conn, "DELETE FROM produk WHERE id_produk='$id'");
    renumberProductIDs($conn);
    header("Location: products.php");
    exit();
}

// ---- Tambah Produk ----
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_produk'];
    $id_merek = $_POST['id_merek']; // Ambil id merek
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];
    $tanggal = date('Y-m-d');
    $gambar = '';

    if (!empty($_FILES['gambar']['name'])) {
        $targetDir = "uploads/";
        $gambar = basename($_FILES["gambar"]["name"]);
        $targetFilePath = $targetDir . $gambar;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileType, $allowTypes)) {
            move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath);
        }
    }

    $query = "INSERT INTO produk (id_merek, nama_produk, harga, stok, tanggal_update, gambar)
          VALUES ('$id_merek', '$nama', '$harga', '$stok', '$tanggal', '$gambar')";
    mysqli_query($conn, $query);
    renumberProductIDs($conn);
    header("Location: products.php");
    exit();
}

// ---- Searching Produk ----
$cari = "";
if (isset($_GET['cari'])) {
    $cari = $_GET['cari'];
    $result = mysqli_query($conn, "SELECT id_produk, nama_produk, merek, gambar, harga, stok, tanggal_update FROM produk 
        WHERE id_produk LIKE '%$cari%' 
        OR nama_produk LIKE '%$cari%' 
        OR merek LIKE '%$cari%'
        OR harga LIKE '%$cari%' 
        OR stok LIKE '%$cari%' 
        ORDER BY id_produk ASC");

} else {
    $result = mysqli_query($conn, "
  SELECT p.id_produk, p.nama_produk, m.nama_merek, p.gambar, p.harga, p.stok, p.tanggal_update
  FROM produk p
  LEFT JOIN merek m ON p.id_merek = m.id_merek
  ORDER BY p.id_produk ASC
");

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products - Toko Elektronik</title>
<link rel="stylesheet" href="style.css">
<style>
/* === THEME STYLE (MiNa Techno Premium Dashboard) === */
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  display: flex;
  background: linear-gradient(135deg, #eaf1ff, #f8fbff);
  color: #333;
  overflow-x: hidden;
}

.main-content {
  margin-left: 260px;
  padding: 40px;
  width: calc(100% - 260px);
  animation: fadeIn 0.7s ease;
}



/* ==== TOPBAR ==== */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
  backdrop-filter: blur(10px);
  padding: 18px 25px;
  border-radius: 15px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  color: #fbfcfdff;
  margin-bottom: 30px;
  
}

/* ==== BUTTON TAMBAH PRODUK ==== */
.add-product-btn {
  background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff);
  color: white;
  border: none;
  padding: 12px 22px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  font-size: 15px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}

.add-product-btn:hover {
  transform: scale(1.05);
 background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff);
}

/* ==== FORM TAMBAH PRODUK ==== */
.form-tambah {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  padding: 25px;
  border-radius: 15px;
  margin-bottom: 25px;
  border-left: 6px solid #2f639b;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: all 0.4s ease;
  animation: fadeInForm 0.4s ease;
}

@keyframes fadeInForm {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.form-tambah label {
  font-weight: 600;
  color: #335981ff;
}

.form-tambah input {
  width: 100%;
  padding: 10px;
  margin: 6px 0 16px;
  border: 1px solid #ccc;
  border-radius: 10px;
  transition: all 0.3s;
}

.form-tambah input:focus {
  border-color: #090e13ff;
  box-shadow: 0 0 8px rgba(47, 99, 155, 0.4);
  outline: none;
}

.form-tambah button {
  background: linear-gradient(90deg, #0f8e5fff, #0e6f20ff);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  box-shadow: 0 3px 8px rgba(0,0,0,0.2);
  transition: all 0.3s;
}

.form-tambah button:hover {
  transform: scale(1.07);
}

.form-tambah .form-group {
  display: block;
  margin-bottom: 15px;
}

.form-tambah label {
  display: block;
  font-weight: 600;
  color: #335981;
  margin-bottom: 5px;
}

.form-tambah select,
.form-tambah input[type="text"],
.form-tambah input[type="number"],
.form-tambah input[type="file"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-sizing: border-box;
}


/* ====== HEADER TOOLS (Posisi Kanan Atas) ====== */
.header-tools {
  display: flex;
  justify-content: flex-end; /* Pindah ke kanan */
  align-items: center;
  margin-bottom: 20px;
}

/* ==== SEARCH BOX di Kanan ==== */
.search-box {
  background: rgba(255, 255, 255, 0.9);
  padding: 10px 15px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 10px;
}

.search-box input {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 200px;
}

.search-btn, .reset-btn {
  background: linear-gradient(90deg, #0f8e5f, #0e6f20);
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s;
}

.search-btn:hover, .reset-btn:hover {
  transform: scale(1.05);
  background: linear-gradient(90deg, #0e6f20, #0f8e5f);
}


 /* ==== TABLE STYLE (Tema Biru Elegan) ==== */
table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  background: white;
}

/* 🌈 Header tabel dengan gradasi */
thead th {
  background: linear-gradient(90deg, #4e69a3ff, #5c85a6ff);
}

thead th {
  color: #fff;
  font-weight: 600;
  padding: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #dee3f0;
}

/* ==== BODY TABLE ==== */
th, td {
  padding: 15px;
  text-align: center;
  border-bottom: 1px solid #eee;
  color: #333;
}

tbody tr:nth-child(even) {
  background: #f3f7ff;
}

tbody tr:hover {
  background: #e1ebff;
  transition: all 0.3s ease;
}

/* ==== RESPONSIVE ==== */
@media (max-width: 768px) {
  table {
    font-size: 13px;
  }
  th, td {
    padding: 10px;
  }
}


/* ==== BUTTON AKSI ==== */
.aksi a {
  text-decoration: none;
  color: white;
  padding: 7px 12px;
  border-radius: 8px;
  font-weight: bold;
  font-size: 14px;
  margin-right: 6px;
  display: inline-block;
  transition: all 0.3s ease;
}

.edit {
  background: linear-gradient(90deg, #ffc107, #ffb300);
}

.hapus {
  background: linear-gradient(90deg, #dc3545, #b02a37);
}

.edit:hover, .hapus:hover {
  transform: scale(1.1);
  filter: brightness(1.1);
}

/* ==== GAMBAR ==== */
img.produk-img {
  width: 85px;
  height: 85px;
  object-fit: cover;
  border-radius: 12px;
  border: 3px solid #2f639b33;
  transition: all 0.3s;
}

img.produk-img:hover {
  transform: scale(1.12);
  border-color: #2f639b88;
  box-shadow: 0 3px 10px rgba(47, 99, 155, 0.3);
}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
  <div class="topbar">
    <h1>Daftar Produk</h1>
  </div>

  <!-- Tombol Tambah Produk -->
  <div style="margin-bottom: 20px;">
    <button id="tambahBtn" class="add-product-btn">+ Tambah Produk</button>
  </div>

  <!-- Form Tambah Produk (disembunyikan) -->
<div class="form-tambah" id="formTambah" style="display: none;">
  <form method="POST" action="" enctype="multipart/form-data">
    
    <div class="form-group">
      <label>Nama Produk</label>
      <input type="text" name="nama_produk" required>
    </div>

    <div class="form-group">
      <label>Merek</label>
      <select name="id_merek" required>
        <option value="">-- Pilih Merek --</option>
        <?php while ($m = mysqli_fetch_assoc($merek_query)) { ?>
          <option value="<?= $m['id_merek']; ?>"><?= htmlspecialchars($m['nama_merek']); ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="form-group">
      <label>Harga</label>
      <input type="number" name="harga" required>
    </div>

    <div class="form-group">
      <label>Stok</label>
      <input type="number" name="stok" required>
    </div>

    <div class="form-group">
      <label>Foto Produk</label>
      <input type="file" name="gambar" accept="image/*">
    </div>

    <button type="submit" name="tambah">Simpan Produk</button>
  </form>
</div>


  <!-- 🔍 Form Pencarian di Kanan Atas -->
<div class="header-tools">
  <div class="search-box">
    <form method="GET" action="">
      <input type="text" name="cari" placeholder="Cari produk..." value="<?= htmlspecialchars($cari) ?>">
      <button type="submit" class="search-btn">Cari</button>
      <?php if ($cari != "") { ?>
        <a href="products.php" class="reset-btn">Reset</a>
      <?php } ?>
    </form>
  </div>
</div>


  <!-- Tabel Produk -->
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Merek</th> <!-- Tambahkan kolom ini -->
            <th>Gambar</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Tanggal Update</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?= $row['id_produk'] ?></td>
            <td><?= $row['nama_produk'] ?></td>
            <td><?= htmlspecialchars($row['nama_merek'] ?? '(Tidak ada merek)') ?></td>
             <td>
                <?php if (!empty($row['gambar'])) { ?>
                    <img src="uploads/<?= $row['gambar'] ?>" class="produk-img">
                <?php } else { ?>
                    <span style="color:#aaa;">(tidak ada gambar)</span>
                <?php } ?>
            </td>
            <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td><?= $row['stok'] ?></td>
            <td><?= date("d-m-Y", strtotime($row['tanggal_update'])); ?></td>
            <td class="aksi">
                <a href="edit_produk.php?id=<?= $row['id_produk'] ?>" class="edit">Edit</a>
                <a href="products.php?hapus=<?= $row['id_produk'] ?>" class="hapus"
                   onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
            </td>
        </tr>
    <?php } } else { ?>
        <tr><td colspan="8" style="color:#999;">Tidak ada produk ditemukan</td></tr>
    <?php } ?>
    </tbody>
</table>
</div>

<script>
const tambahBtn = document.getElementById("tambahBtn");
const formTambah = document.getElementById("formTambah");

tambahBtn.addEventListener("click", () => {
  if (formTambah.style.display === "none") {
    formTambah.style.display = "block";
    tambahBtn.textContent = "Tutup Form";
  } else {
    formTambah.style.display = "none";
    tambahBtn.textContent = "+ Tambah Produk";
  }
});
</script>

</body>
</html>
