<?php
include 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}

// Ambil data produk berdasarkan ID
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id'");
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='products.php';</script>";
    exit();
}

// ---- Update Produk ----
if (isset($_POST['update'])) {
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];
    $tanggal = date('Y-m-d');
    $gambar_lama = $produk['gambar'];
    $gambar_baru = $gambar_lama;

    // Jika upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        $targetDir = "uploads/";
        $gambar_baru = basename($_FILES["gambar"]["name"]);
        $targetFilePath = $targetDir . $gambar_baru;

        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($fileType, $allowTypes)) {
            // Hapus gambar lama jika ada
            if (!empty($gambar_lama) && file_exists("uploads/$gambar_lama")) {
                unlink("uploads/$gambar_lama");
            }
            move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFilePath);
        } else {
            echo "<script>alert('Hanya file JPG, JPEG, PNG, atau GIF yang diperbolehkan!');</script>";
        }
    }

    // Update ke database
    $query = "UPDATE produk SET 
                nama_produk='$nama', 
                harga='$harga', 
                stok='$stok', 
                tanggal_update='$tanggal',
                gambar='$gambar_baru'
              WHERE id_produk='$id'";

    mysqli_query($conn, $query);
    echo "<script>alert('Produk berhasil diperbarui!'); window.location='products.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            display: flex;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
            width: calc(100% - 250px);
        }

        .form-edit {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            max-width: 500px;
        }

        .form-edit h2 {
            margin-bottom: 20px;
            color: #2f639bff;
            text-align: center;
        }

        .form-edit label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        .form-edit input {
            width: 100%;
            padding: 8px;
            margin: 6px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .form-edit img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-top: 8px;
        }

        .form-edit button {
            background-color: #2f639bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 15px;
        }

        .form-edit button:hover {
            background-color: #234d79;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: white;
            background: #6c757d;
            padding: 8px 14px;
            border-radius: 6px;
        }

        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="form-edit">
        <h2>Edit Produk</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" value="<?= $produk['nama_produk'] ?>" required>

            <label>Harga</label>
            <input type="number" name="harga" value="<?= $produk['harga'] ?>" required>

            <label>Stok</label>
            <input type="number" name="stok" value="<?= $produk['stok'] ?>" required>

            <label>Gambar Produk</label><br>
            <?php if ($produk['gambar']) { ?>
                <img src="uploads/<?= $produk['gambar'] ?>" alt="Gambar Produk">
            <?php } else { ?>
                <p style="color: #999;">(Belum ada gambar)</p>
            <?php } ?>
            <input type="file" name="gambar" accept="image/*">

            <button type="submit" name="update">Simpan Perubahan</button>
        </form>

        <a href="products.php" class="back-btn">← Kembali</a>
    </div>
</div>

</body>
</html>
