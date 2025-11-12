<?php
session_start();
include 'db.php';

// Cek login
if (!isset($_SESSION['id_karyawan'])) {
  header("Location: login.php");
  exit();
}

// Pastikan metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: orders.php");
  exit();
}

$id_produk = intval($_POST['id_produk'] ?? 0);
if ($id_produk <= 0) {
  die("ID produk tidak valid.");
}

// Ambil data produk dari tabel produk
$stmt = mysqli_prepare($conn, "SELECT nama_produk, harga FROM produk WHERE id_produk = ?");
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$produk) {
  die("Produk tidak ditemukan.");
}

$nama_produk = $produk['nama_produk'];
$harga = $produk['harga'];
$jumlah = 1;
$subtotal = $harga * $jumlah;
$tanggal = date('Y-m-d');

// Simpan ke tabel orders
$stmt = mysqli_prepare($conn, "INSERT INTO orders (nama_produk, harga, jumlah, subtotal, tanggal) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sidds", $nama_produk, $harga, $jumlah, $subtotal, $tanggal);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
  echo "<script>alert('Order berhasil ditambahkan!'); window.location='orders.php';</script>";
} else {
  echo "<script>alert('Gagal menambahkan order.'); window.location='orders.php';</script>";
}
?>
