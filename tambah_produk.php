<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Upload gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $folder = "uploads/" . basename($gambar);
    move_uploaded_file($tmp_name, $folder);

    $query = "INSERT INTO produk (nama, kategori, harga, stok, gambar) 
              VALUES ('$nama', '$kategori', '$harga', '$stok', '$gambar')";
    mysqli_query($conn, $query);

    echo "<script>alert('Produk berhasil ditambahkan!'); window.location='products.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Produk</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Tambah Produk Baru</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Nama Produk:</label>
    <input type="text" name="nama" required>

    <label>Kategori:</label>
    <input type="text" name="kategori" required>

    <label>Harga:</label>
    <input type="number" name="harga" required>

    <label>Stok:</label>
    <input type="number" name="stok" required>

    <label>Gambar Produk:</label>
    <input type="file" name="gambar" accept="image/*" required>

    <button type="submit">Simpan</button>
  </form>
</body>
</html>
