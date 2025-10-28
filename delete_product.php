<?php
include 'db.php';
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM products WHERE id=$id");
header("Location: products.php");
exit();
?>
