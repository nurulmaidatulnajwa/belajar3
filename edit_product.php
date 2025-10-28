<<?php
include 'db.php';
$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id'");
$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $tanggal = date('Y-m-d');

    // --- proses upload gambar ---
    $gambarLama = $row['gambar']; // ambil gambar lama
    $gambarBaru = $_FILES['gambar']['name'];
    $targetDir = "uploads/";
    $targetFilePath = $targetDir . basename($gambarBaru);

    if (!empty($gambarBaru)) {
        // hapus gambar lama jika ada
        if (file_exists($targetDir . $gambarLama) && $gambarLama != "") {
            unlink($targetDir . $gambarLama);
        }
        // upload gambar baru
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath);
        $gambarFinal = $gambarBaru;
    } else {
        // jika tidak ada upload baru, pakai gambar lama
        $gambarFinal = $gambarLama;
    }

    // update data ke database
    mysqli_query($conn, "UPDATE produk SET 
        nama_produk='$nama',
        harga='$harga',
        stok='$stok',
        gambar='$gambarFinal',
        tanggal_update='$tanggal'
        WHERE id_produk='$id'
    ");

    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Produk</title>
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
      background-color: #28a745;
      color: white;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
<div class="form-container">
  <h2>Edit Produk</h2>
  <form method="POST">
    <input type="text" name="nama_produk" value="<?= $row['nama_produk'] ?>" required>
    <input type="number" name="harga" value="<?= $row['harga'] ?>" required>
    <input type="number" name="stok" value="<?= $row['stok'] ?>" required>
    <button type="submit" name="update">Update</button>
  </form>
</div>
</body>
</html>
