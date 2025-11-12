<?php
include 'db.php';
if (isset($_POST['simpan'])) {
  $nama = $_POST['nama_produk'];
  $harga = $_POST['harga'];
  $stok = $_POST['stok'];
  $tanggal = date('Y-m-d');

  mysqli_query($conn, "INSERT INTO produk (nama_produk, harga, stok, tanggal_update) 
                       VALUES ('$nama', '$harga', '$stok', '$tanggal')");

  header("Location: products.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Tambah Produk</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: #f4f6f9;
      font-family: Arial, sans-serif;
    }
    .form-container {
      width: 400px;
      margin: 80px auto;
      background: white;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      padding: 10px;
      border: none;
      background-color: #007bff;
      color: white;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
<div class="form-container">
  <h2>Tambah Produk</h2>
  <form method="POST">
    <input type="text" name="nama_produk" placeholder="Nama Produk" required>
    <input type="number" name="harga" placeholder="Harga" required>
    <input type="number" name="stok" placeholder="Stok" required>
    <button type="submit" name="simpan">Simpan</button>
  </form>
</div>
</body>
</html>

