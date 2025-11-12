<?php
include 'db.php';
session_start();

$nama_customer = $_POST['nama_customer'];
$id_karyawan   = $_POST['id_karyawan'];
$total_keseluruhan = $_POST['total_keseluruhan'];
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

// Simpan ke tabel penjualan
mysqli_query($conn, "INSERT INTO penjualan (nama_customer, id_karyawan, tanggal, jam, total_harga)
                     VALUES ('$nama_customer', '$id_karyawan', '$tanggal', '$jam', '$total_keseluruhan')");

// Ambil id penjualan terakhir
$id_penjualan = mysqli_insert_id($conn);

// Simpan ke tabel penjualan_detail
$produk = $_POST['produk'];
$harga = $_POST['harga'];
$jumlah = $_POST['jumlah'];
$total = $_POST['total'];

for ($i = 0; $i < count($produk); $i++) {
  $id_produk = $produk[$i];
  $harga_item = $harga[$i];
  $jumlah_item = $jumlah[$i];
  $total_item = $total[$i];

  mysqli_query($conn, "INSERT INTO penjualan_detail (id_penjualan, id_produk, jumlah_beli, total_per_item)
                       VALUES ('$id_penjualan', '$id_produk', '$jumlah_item', '$total_item')");
}

echo "<script>alert('Penjualan berhasil disimpan!'); window.location='penjualan.php';</script>";
?>
