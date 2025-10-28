<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Toko NuMi Techno Solution</title>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f4f6f9;
      scroll-behavior: smooth;
    }

    /* ===== NAVBAR ===== */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: linear-gradient(135deg, #007bff, #00bfff);
      padding: 15px 50px;
      color: white;
      position: sticky;
      top: 0;
      z-index: 10;
      box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }

    .navbar .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .navbar .logo img {
      width: 45px;
      height: 45px;
      border-radius: 10px;
    }

    .navbar h1 {
      font-size: 22px;
      margin: 0;
      font-weight: bold;
    }

    .navbar ul {
      list-style: none;
      display: flex;
      gap: 25px;
      margin: 0;
    }

    .navbar ul li a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: 0.3s;
    }

    .navbar ul li a:hover {
      color: #ffe600;
    }

    .login-btn {
      background: white;
      color: #007bff;
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }

    .login-btn:hover {
      background: #f1f1f1;
    }

    /* ===== HERO ===== */
    .hero {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 90vh;
      color: white;
      text-align: center;
      background: url('mn.jpg') no-repeat center center/cover;
      position: relative;
      animation: fadeIn 1.5s ease-in;
    }

    .hero::after {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.55);
      z-index: 0;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 700px;
      padding: 25px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      backdrop-filter: blur(5px);
      animation: slideUp 1s ease-out;
    }

    .hero-content h2 {
      font-size: 42px;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .hero-content p {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .hero-content a {
      background: #00bfff;
      color: white;
      padding: 12px 25px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
      box-shadow: 0 3px 8px rgba(0,0,0,0.3);
    }

    .hero-content a:hover {
      background: #007bff;
      transform: scale(1.05);
    }

    /* ===== PRODUK UNGGULAN ===== */
    .products {
      text-align: center;
      padding: 80px 10%;
    }

    .products h2 {
      color: #007bff;
      font-size: 30px;
      margin-bottom: 40px;
      position: relative;
    }

    .products h2::after {
      content: "";
      width: 80px;
      height: 4px;
      background: #00bfff;
      display: block;
      margin: 10px auto 0;
      border-radius: 5px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
    }

    .product-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 20px;
      transition: all 0.3s;
      animation: fadeIn 1s ease-in;
    }

    .product-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 10px;
    }

    .product-card h3 {
      color: #007bff;
      margin: 15px 0 8px;
    }

    .product-card p {
      color: #333;
      font-size: 14px;
    }

    /* ===== MENGAPA PILIH KAMI ===== */
    .why-us {
      background: #f8fbff;
      text-align: center;
      padding: 70px 10%;
    }

    .why-us h2 {
      color: #007bff;
      margin-bottom: 40px;
      font-size: 28px;
    }

    .why-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 25px;
    }

    .why-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 250px;
      padding: 25px;
      transition: all 0.3s;
    }

    .why-card:hover {
      transform: translateY(-5px);
    }

    .why-card img {
      width: 100px;
      height: 80px;
      margin-bottom: 10px;
    }

    .why-card h4 {
      color: #007bff;
      margin-bottom: 8px;
    }

    /* ===== ABOUT ===== */
    .about {
      background: white;
      padding: 70px 10%;
      text-align: center;
    }

    .about h2 {
      color: #007bff;
      font-size: 26px;
      margin-bottom: 15px;
    }

    .about p {
      max-width: 800px;
      margin: auto;
      line-height: 1.8;
      color: #333;
    }

    /* ===== FOOTER ===== */
    footer {
      background: #007bff;
      color: white;
      text-align: center;
      padding: 20px;
      font-size: 14px;
    }

    footer a {
      color: #ffe600;
      text-decoration: none;
      font-weight: bold;
    }

    footer a:hover {
      text-decoration: underline;
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeIn {
      from {opacity: 0;}
      to {opacity: 1;}
    }
    @keyframes slideUp {
      from {transform: translateY(40px); opacity: 0;}
      to {transform: translateY(0); opacity: 1;}
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="logo">
      <img src="nm.png" alt="Logo">
      <h1>MiNa Techno</h1>
    </div>
    <ul>
      <li><a href="#home">Home</a></li>
      <li><a href="#produk">Products</a></li>
      <li><a href="#tentang">About</a></li>
    </ul>
    <a href="login.php" class="login-btn">Login</a>
  </nav>

  <!-- HERO -->
  <section class="hero" id="home">
    <div class="hero-content">
      <h2>Solusi Elektronik Modern</h2>
      <p>Penuhi semua kebutuhan elektronik rumah tangga Anda dengan produk berkualitas dari NuMi Techno Solution.</p>
      <a href="#produk">Lihat Produk</a> 
    </div>
  </section>

  <!-- PRODUK UNGGULAN -->
  <section class="products" id="produk">
    <h2>Produk Unggulan</h2>
    <div class="product-grid">
      <div class="product-card">
        <img src="tv.webp" alt="TV LED 42 Inch">
        <h3>TV LED 42 Inch</h3>
        <p>Gambar jernih dan suara menggelegar.</p>
      </div>

      <div class="product-card">
        <img src="kulkas.webp" alt="Kulkas 2 Pintu">
        <h3>Kulkas 2 Pintu</h3>
        <p>Pendiginan cepat dan hemat energi.</p>
      </div>

      <div class="product-card">
        <img src="laptop.jpg" alt="Laptop">
        <h3>Laptop</h3>
        <p>Desain elegan dengan layar 14 inci Full HD.</p>
      </div>
    </div>
  </section>

  <!-- MENGAPA PILIH KAMI -->
  <section class="why-us">
    <h2>Mengapa Memilih Kami?</h2>
    <div class="why-grid">
      <div class="why-card">
        <img src="kualitas.jpeg" alt="Kualitas">
        <h4>Kualitas Terbaik</h4>
        <p>Produk bergaransi resmi dan berkualitas premium.</p>
      </div>
      <div class="why-card">
        <img src="harga.jpeg" alt="Harga">
        <h4>Harga Bersaing</h4>
        <p>Dapatkan harga terbaik untuk setiap produk.</p>
      </div>
      <div class="why-card">
        <img src="pelayanan.jpg" alt="Pelayanan">
        <h4>Pelayanan Cepat</h4>
        <p>Respon cepat dan dukungan pelanggan ramah.</p>
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="about" id="tentang">
    <h2>Tentang Kami</h2>
    <p>
      <b>MiNa Techno</b> adalah toko elektronik terpercaya yang menyediakan berbagai produk elektronik rumah tangga dan kantor. 
      Kami berkomitmen untuk memberikan pelayanan terbaik dengan produk berkualitas tinggi dan harga bersaing.
    </p>
  </section>

  <!-- FOOTER -->
  <footer>
    © 2025 <b>MiNa Techno</b> | Semua Hak Dilindungi |
    <a href="#">Kebijakan Privasi</a>
  </footer>

</body>
</html>
