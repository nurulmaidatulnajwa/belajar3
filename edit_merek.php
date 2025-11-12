<?php
include 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location: login.php");
  exit();
}

// Ambil data merek berdasarkan ID
$id = intval($_GET['id']);
$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM merek WHERE id_merek=$id"));

// Update data merek
if (isset($_POST['simpan'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_merek']);
  $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
  mysqli_query($conn, "UPDATE merek SET nama_merek='$nama', deskripsi='$deskripsi' WHERE id_merek=$id");
  header("Location: merek.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Merek</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f5f6fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 400px;
      padding: 30px 40px;
      text-align: center;
      transition: all 0.3s ease;
    }

    .container:hover {
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }

    h1 {
      margin-bottom: 25px;
      color: #333;
      font-size: 22px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      text-align: left;
      font-weight: 500;
      color: #444;
      margin-bottom: 5px;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      outline: none;
      transition: 0.2s;
    }

    input[type="text"]:focus,
    textarea:focus {
      border-color: #3498db;
      box-shadow: 0 0 5px rgba(52, 152, 219, 0.4);
    }

    textarea {
      resize: vertical;
      min-height: 80px;
    }

    button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 10px 0;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 15px;
      transition: all 0.2s ease;
    }

    button:hover {
      background-color: #2980b9;
      transform: translateY(-2px);
    }

    .back-link {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: #555;
      font-size: 14px;
      transition: color 0.2s;
    }

    .back-link:hover {
      color: #3498db;
    }

    .back-link i {
      margin-right: 5px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1><i class="fa-solid fa-pen-to-square"></i> Edit Merek</h1>
    <form method="POST">
      <div class="form-group">
        <label>Nama Merek</label>
        <input type="text" name="nama_merek" value="<?= htmlspecialchars($data['nama_merek']); ?>" required>
      </div>

      <div class="form-group">
        <label>Deskripsi</label>
        <textarea name="deskripsi"><?= htmlspecialchars($data['deskripsi']); ?></textarea>
      </div>

      <button type="submit" name="simpan">
        <i class="fa-solid fa-save"></i> Simpan
      </button>
    </form>

    <a href="merek.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Data Merek</a>
  </div>

</body>
</html>
