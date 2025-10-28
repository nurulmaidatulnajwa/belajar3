<?php
// tambah_order.php (gantikan file yang lama dengan ini)

include 'db.php';
session_start();

// Pastikan user login
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

// Pastikan koneksi valid
if (!isset($conn) || !$conn) {
  // Jika koneksi bermasalah, tampilkan error dan hentikan
  die("Koneksi database bermasalah: " . mysqli_connect_error());
}

// Hanya proses POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: orders.php");
  exit();
}

// Ambil id_produk dari POST dan validasi
$id_produk = isset($_POST['id_produk']) ? intval($_POST['id_produk']) : 0;
if ($id_produk <= 0) {
  die("ID produk tidak valid.");
}

/*
  Pastikan tabel orders ada. Jika belum ada, buat struktur sederhana:
  id_order, nama_produk, harga, jumlah (default 1), subtotal, tanggal_order
*/
$create_sql = "
CREATE TABLE IF NOT EXISTS orders (
  id_order INT AUTO_INCREMENT PRIMARY KEY,
  nama_produk VARCHAR(255) NOT NULL,
  harga DECIMAL(15,2) NOT NULL,
  jumlah INT NOT NULL DEFAULT 1,
  subtotal DECIMAL(15,2) NOT NULL,
  tanggal_order DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if (!mysqli_query($conn, $create_sql)) {
  die("Gagal memastikan tabel orders: " . mysqli_error($conn));
}

// Ambil data produk dari tabel produk
$produk_sql = "SELECT id_produk, nama_produk, harga, gambar FROM produk WHERE id_produk = ?";
$stmt = mysqli_prepare($conn, $produk_sql);
if (!$stmt) {
  die("Prepare gagal (produk): " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $id_produk);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$produk = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$produk) {
  die("Produk dengan ID {$id_produk} tidak ditemukan.");
}

// Siapkan data untuk dimasukkan ke tabel orders
$nama_produk = $produk['nama_produk'];
$harga = (float)$produk['harga'];
$jumlah = 1; // default 1 — bisa diubah jika nanti ada input jumlah
$subtotal = $harga * $jumlah;
$tanggal = date('Y-m-d');

// Gunakan prepared statement untuk insert
$insert_sql = "INSERT INTO orders (nama_produk, harga, jumlah, subtotal, tanggal_order) VALUES (?, ?, ?, ?, ?)";
$ins_stmt = mysqli_prepare($conn, $insert_sql);
if (!$ins_stmt) {
  die("Prepare gagal (insert): " . mysqli_error($conn));
}

mysqli_stmt_bind_param($ins_stmt, "sidds", $nama_produk, $harga, $jumlah, $subtotal, $tanggal);
$ok = mysqli_stmt_execute($ins_stmt);

if ($ok) {
  // sukses -> redirect kembali ke orders.php
  mysqli_stmt_close($ins_stmt);
  header("Location: orders.php?msg=added");
  exit();
} else {
  // tampilkan error MySQL untuk debugging
  $err = mysqli_stmt_error($ins_stmt);
  mysqli_stmt_close($ins_stmt);
  die("Gagal menyimpan order: " . $err);
}
