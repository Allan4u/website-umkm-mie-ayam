<?php
require_once 'config/database.php';

$query_pengaturan = "SELECT * FROM pengaturan LIMIT 1";
$result_pengaturan = mysqli_query($conn, $query_pengaturan);
$pengaturan = mysqli_fetch_assoc($result_pengaturan);

$query_toko_rating = "SELECT * FROM toko_rating LIMIT 1";
$result_toko_rating = mysqli_query($conn, $query_toko_rating);
$toko_rating = mysqli_fetch_assoc($result_toko_rating);

$query_featured = "SELECT m.*, k.nama_kategori,
                   COALESCE(AVG(r.rating), 0) as avg_rating,
                   COUNT(r.id) as review_count
                   FROM menu m 
                   LEFT JOIN kategori k ON m.kategori_id = k.id 
                   LEFT JOIN reviews r ON m.id = r.menu_id AND r.status = 'approved'
                   WHERE m.is_featured = 1 AND m.status = 'aktif' 
                   GROUP BY m.id
                   ORDER BY m.id DESC LIMIT 6";
$result_featured = mysqli_query($conn, $query_featured);

$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori";
$result_kategori = mysqli_query($conn, $query_kategori);

$query_menu = "SELECT m.*, k.nama_kategori,
               COALESCE(AVG(r.rating), 0) as avg_rating,
               COUNT(r.id) as review_count
               FROM menu m 
               LEFT JOIN kategori k ON m.kategori_id = k.id 
               LEFT JOIN reviews r ON m.id = r.menu_id AND r.status = 'approved'
               WHERE m.status = 'aktif' 
               GROUP BY m.id
               ORDER BY k.nama_kategori, m.nama_menu";
$result_menu = mysqli_query($conn, $query_menu);

$menu_by_kategori = [];
while ($row = mysqli_fetch_assoc($result_menu)) {
    $menu_by_kategori[$row['nama_kategori']][] = $row;
}

$query_reviews = "SELECT r.*, m.nama_menu FROM reviews r 
                  LEFT JOIN menu m ON r.menu_id = m.id 
                  WHERE r.status = 'approved' 
                  ORDER BY r.created_at DESC LIMIT 6";
$result_reviews = mysqli_query($conn, $query_reviews);

function display_stars($rating, $show_number = false) {
    $output = '';
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;
    
    for ($i = 0; $i < $full_stars; $i++) {
        $output .= '<i class="fas fa-star text-warning"></i>';
    }
    
    if ($half_star) {
        $output .= '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    
    for ($i = 0; $i < $empty_stars; $i++) {
        $output .= '<i class="far fa-star text-muted"></i>';
    }
    
    if ($show_number) {
        $output .= ' <span class="ms-1">' . number_format($rating, 1) . '</span>';
    }
    
    return $output;
}

$review_success = false;
$review_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $nama = clean_input($_POST['nama']);
    $email = clean_input($_POST['email']);
    $rating = (int)$_POST['rating'];
    $komentar = clean_input($_POST['komentar']);
    $menu_id = !empty($_POST['menu_id']) ? (int)$_POST['menu_id'] : NULL;
    
    if (empty($nama) || empty($komentar) || $rating < 1 || $rating > 5) {
        $review_error = 'Mohon lengkapi semua field yang wajib diisi!';
    } else {
        $query_insert = "INSERT INTO reviews (nama, email, rating, komentar, menu_id, status) 
                         VALUES ('$nama', '$email', $rating, '$komentar', " . 
                         ($menu_id ? $menu_id : "NULL") . ", 'pending')";
        
        if (mysqli_query($conn, $query_insert)) {
            $review_success = true;
        } else {
            $review_error = 'Terjadi kesalahan saat menyimpan review. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pengaturan['nama_toko']; ?> - Mie Ayam Terenak di Bandar Lampung</title>
    <meta name="description" content="<?php echo $pengaturan['deskripsi_toko']; ?>">
    <meta name="keywords" content="mie ayam, bakso, kuliner lampung, makanan enak, delivery">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="icon" type="image/x-icon" href="assets/images/logo.png">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#home">
            <i class="fas fa-utensils me-2"></i><?php echo $pengaturan['nama_toko']; ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">Tentang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#menu">Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reviews">Reviews</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Kontak</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-success ms-2" href="https://wa.me/<?php echo $pengaturan['whatsapp']; ?>?text=Halo, saya ingin memesan mie ayam" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp me-1"></i>Pesan Sekarang
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content text-white">
                    <h1 class="display-4 fw-bold mb-4 animate-slide-up">
                        Mie Ayam Terenak di <span class="text-warning">Bandar Lampung</span>
                    </h1>
                    <p class="lead mb-4 animate-slide-up" style="animation-delay: 0.2s">
                        Nikmati kelezatan mie ayam dengan cita rasa otentik, bumbu meresap, dan harga terjangkau. 
                        Sudah melayani ribuan pelanggan dengan rating 
                        <span class="text-warning fw-bold">
                            <i class="fas fa-star"></i> <?php echo number_format($toko_rating['total_rating'], 1); ?>
                        </span>
                        dari <?php echo number_format($toko_rating['total_reviews']); ?> reviews!
                    </p>
                    <div class="hero-buttons animate-slide-up" style="animation-delay: 0.4s">
                        <a href="#menu" class="btn btn-warning btn-lg me-3">
                            <i class="fas fa-utensils me-2"></i>Lihat Menu
                        </a>
                        <a href="https://wa.me/<?php echo $pengaturan['whatsapp']; ?>?text=Halo, saya ingin memesan mie ayam" 
                           class="btn btn-success btn-lg" target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp me-2"></i>Pesan Via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section id="featured" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Menu Andalan Kami</h2>
                <p class="lead text-muted">Pilihan menu terpopuler yang paling disukai pelanggan</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (mysqli_num_rows($result_featured) > 0): ?>
                <?php while ($featured = mysqli_fetch_assoc($result_featured)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card menu-card h-100 shadow-sm">
                        <?php if (!empty($featured['gambar']) && file_exists('assets/images/menu/' . $featured['gambar'])): ?>
                            <img src="assets/images/menu/<?php echo htmlspecialchars($featured['gambar']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($featured['nama_menu']); ?>"
                                 style="height: 200px; object-fit: cover;"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-bowl-food fa-4x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title"><?php echo htmlspecialchars($featured['nama_menu']); ?></h5>
                                <span class="badge bg-warning text-dark">Populer</span>
                            </div>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($featured['deskripsi']); ?></p>
                            
                            <!-- Rating Display -->
                            <?php if ($featured['review_count'] > 0): ?>
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rating-stars me-2">
                                        <?php echo display_stars($featured['avg_rating']); ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo number_format($featured['avg_rating'], 1); ?> 
                                        (<?php echo $featured['review_count']; ?> review<?php echo $featured['review_count'] > 1 ? 's' : ''; ?>)
                                    </small>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 text-primary mb-0">Rp <?php echo number_format($featured['harga'], 0, ',', '.'); ?></span>
                                <button class="btn btn-primary btn-sm order-btn" 
                                        data-menu="<?php echo htmlspecialchars($featured['nama_menu']); ?>" 
                                        data-harga="<?php echo $featured['harga']; ?>">
                                    <i class="fas fa-shopping-cart me-1"></i>Pesan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada menu featured yang tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="assets/images/about-us.jpg" alt="Tentang Kami" class="img-fluid rounded-4 shadow">
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <h2 class="display-5 fw-bold mb-4">Tentang <?php echo htmlspecialchars($pengaturan['nama_toko']); ?></h2>
                    <p class="lead mb-4"><?php echo htmlspecialchars($pengaturan['deskripsi_toko']); ?></p>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="feature-box text-center">
                                <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-3">
                                    <i class="fas fa-award fa-2x"></i>
                                </div>
                                <h5>Kualitas Terbaik</h5>
                                <p class="text-muted">Menggunakan bahan-bahan fresh dan berkualitas tinggi</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="feature-box text-center">
                                <div class="feature-icon bg-success text-white rounded-circle mx-auto mb-3">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <h5>Pelayanan Cepat</h5>
                                <p class="text-muted">Pesanan diproses dengan cepat dan efisien</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="opening-hours bg-light p-4 rounded-3">
                        <h5 class="mb-3"><i class="fas fa-clock me-2 text-primary"></i>Jam Buka</h5>
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Senin - Minggu</strong></p>
                                <p class="text-muted"><?php echo date('H:i', strtotime($pengaturan['jam_buka'])); ?> - <?php echo date('H:i', strtotime($pengaturan['jam_tutup'])); ?></p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><strong>Rating Pelanggan</strong></p>
                                <div class="d-flex align-items-center">
                                    <?php echo display_stars($toko_rating['total_rating'], true); ?>
                                    <span class="ms-2 text-muted">(<?php echo $toko_rating['total_reviews']; ?> reviews)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Menu Section -->
<section id="menu" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Menu Lengkap</h2>
                <p class="lead text-muted">Pilihan menu lengkap dengan cita rasa yang tak terlupakan</p>
            </div>
        </div>
        
        <!-- Menu Categories -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="menu-filter text-center">
                    <button class="btn btn-outline-primary me-2 mb-2 filter-btn active" data-filter="all">
                        Semua Menu
                    </button>
                    <?php 
                    mysqli_data_seek($result_kategori, 0);
                    while ($kategori = mysqli_fetch_assoc($result_kategori)): 
                    ?>
                    <button class="btn btn-outline-primary me-2 mb-2 filter-btn" data-filter="<?php echo strtolower(str_replace(' ', '-', $kategori['nama_kategori'])); ?>">
                        <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                    </button>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        
        <!-- Menu Items -->
        <div class="row g-4" id="menu-container">
            <?php if (!empty($menu_by_kategori)): ?>
                <?php foreach ($menu_by_kategori as $kategori_nama => $menu_items): ?>
                    <?php foreach ($menu_items as $menu): ?>
                    <div class="col-lg-4 col-md-6 menu-item" data-category="<?php echo strtolower(str_replace(' ', '-', $kategori_nama)); ?>">
                        <div class="card menu-card h-100 shadow-sm">
                            <?php if (!empty($menu['gambar']) && file_exists('assets/images/menu/' . $menu['gambar'])): ?>
                                <img src="assets/images/menu/<?php echo htmlspecialchars($menu['gambar']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($menu['nama_menu']); ?>"
                                     style="height: 200px; object-fit: cover;"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="card-img-top bg-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-bowl-food fa-4x text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?php echo htmlspecialchars($menu['nama_menu']); ?></h5>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($kategori_nama); ?></span>
                                </div>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($menu['deskripsi']); ?></p>
                                
                                <!-- Rating Display -->
                                <?php if ($menu['review_count'] > 0): ?>
                                <div class="mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="rating-stars me-2">
                                            <?php echo display_stars($menu['avg_rating']); ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo number_format($menu['avg_rating'], 1); ?> 
                                            (<?php echo $menu['review_count']; ?> review<?php echo $menu['review_count'] > 1 ? 's' : ''; ?>)
                                        </small>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0">Rp <?php echo number_format($menu['harga'], 0, ',', '.'); ?></span>
                                    <button class="btn btn-primary btn-sm order-btn" 
                                            data-menu="<?php echo htmlspecialchars($menu['nama_menu']); ?>" 
                                            data-harga="<?php echo $menu['harga']; ?>">
                                        <i class="fas fa-shopping-cart me-1"></i>Pesan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada menu yang tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Customer Reviews Section -->
<section id="reviews" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Apa Kata Pelanggan</h2>
                <p class="lead text-muted">Testimoni dari pelanggan yang sudah merasakan kelezatan menu kami</p>
                <div class="rating-summary">
                    <div class="h3 text-warning mb-2">
                        <?php echo display_stars($toko_rating['total_rating'], true); ?>
                    </div>
                    <p class="text-muted">Berdasarkan <?php echo number_format($toko_rating['total_reviews']); ?> reviews dari pelanggan</p>
                </div>
            </div>
        </div>
        
        <!-- Reviews Grid -->
        <div class="row g-4 mb-5">
            <?php if (mysqli_num_rows($result_reviews) > 0): ?>
                <?php while ($review = mysqli_fetch_assoc($result_reviews)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card review-card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="review-avatar bg-primary text-white rounded-circle me-3">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($review['nama']); ?></h6>
                                    <div class="rating-stars">
                                        <?php echo display_stars($review['rating']); ?>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text">"<?php echo htmlspecialchars($review['komentar']); ?>"</p>
                            <?php if ($review['nama_menu']): ?>
                                <small class="text-muted">
                                    <i class="fas fa-utensils me-1"></i>
                                    Review untuk: <?php echo htmlspecialchars($review['nama_menu']); ?>
                                </small>
                            <?php endif; ?>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada review yang tersedia. Jadilah yang pertama memberikan review!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Add Review Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-pen me-2"></i>Berikan Review Anda</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($review_success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                Terima kasih! Review Anda telah dikirim dan akan ditampilkan setelah disetujui.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($review_error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo $review_error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="#reviews" id="reviewForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email (Opsional)</label>
                                    <input type="email" class="form-control" id="email" name="email" maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                                    <select class="form-select" id="rating" name="rating" required>
                                        <option value="">Pilih Rating</option>
                                        <option value="5">⭐⭐⭐⭐⭐ - Sangat Baik</option>
                                        <option value="4">⭐⭐⭐⭐ - Baik</option>
                                        <option value="3">⭐⭐⭐ - Cukup</option>
                                        <option value="2">⭐⭐ - Kurang</option>
                                        <option value="1">⭐ - Sangat Kurang</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="menu_id" class="form-label">Menu yang Direview (Opsional)</label>
                                    <select class="form-select" id="menu_id" name="menu_id">
                                        <option value="">Pilih Menu</option>
                                        <?php 
                                        // Reset pointer dan ambil menu untuk dropdown
                                        mysqli_data_seek($result_menu, 0);
                                        $query_menu_dropdown = "SELECT * FROM menu WHERE status = 'aktif' ORDER BY nama_menu";
                                        $result_menu_dropdown = mysqli_query($conn, $query_menu_dropdown);
                                        while ($menu_option = mysqli_fetch_assoc($result_menu_dropdown)): 
                                        ?>
                                            <option value="<?php echo $menu_option['id']; ?>">
                                                <?php echo htmlspecialchars($menu_option['nama_menu']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="komentar" class="form-label">Komentar/Review <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="komentar" name="komentar" rows="4" required 
                                              placeholder="Ceritakan pengalaman Anda..." maxlength="500"></textarea>
                                    <div class="form-text">Maksimal 500 karakter</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="submit_review" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>Kirim Review
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Hubungi Kami</h2>
                <p class="lead text-muted">Siap melayani pesanan Anda kapan saja</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="contact-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="contact-icon bg-primary text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                    <h5>Alamat</h5>
                    <p class="text-muted"><?php echo htmlspecialchars($pengaturan['alamat']); ?></p>
                    <a href="https://maps.google.com?q=<?php echo urlencode($pengaturan['alamat']); ?>" 
                       class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fas fa-directions me-1"></i>Lihat di Maps
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="contact-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="contact-icon bg-success text-white rounded-circle mx-auto mb-3">
                        <i class="fab fa-whatsapp fa-2x"></i>
                    </div>
                    <h5>WhatsApp</h5>
                    <p class="text-muted"><?php echo htmlspecialchars($pengaturan['whatsapp']); ?></p>
                    <a href="https://wa.me/<?php echo $pengaturan['whatsapp']; ?>?text=Halo, saya ingin memesan mie ayam" 
                       class="btn btn-success btn-sm" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp me-1"></i>Chat Sekarang
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="contact-card text-center p-4 h-100 bg-white rounded-3 shadow-sm">
                    <div class="contact-icon bg-warning text-white rounded-circle mx-auto mb-3">
                        <i class="fas fa-phone fa-2x"></i>
                    </div>
                    <h5>Telepon</h5>
                    <p class="text-muted"><?php echo htmlspecialchars($pengaturan['telepon']); ?></p>
                    <a href="tel:<?php echo $pengaturan['telepon']; ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-phone me-1"></i>Telepon
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h5 class="mb-3"><?php echo htmlspecialchars($pengaturan['nama_toko']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($pengaturan['deskripsi_toko']); ?></p>
                <div class="social-links">
                    <a href="https://wa.me/<?php echo $pengaturan['whatsapp']; ?>" class="text-white me-3" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp fa-lg"></i>
                    </a>
                    <a href="mailto:<?php echo $pengaturan['email']; ?>" class="text-white me-3">
                        <i class="fas fa-envelope fa-lg"></i>
                    </a>
                    <a href="tel:<?php echo $pengaturan['telepon']; ?>" class="text-white">
                        <i class="fas fa-phone fa-lg"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
                <h5 class="mb-3">Jam Buka</h5>
                <p class="text-muted">
                    <i class="fas fa-clock me-2"></i>
                    Senin - Minggu<br>
                    <?php echo date('H:i', strtotime($pengaturan['jam_buka'])); ?> - 
                    <?php echo date('H:i', strtotime($pengaturan['jam_tutup'])); ?>
                </p>
                <p class="text-muted">
                    <i class="fas fa-star me-2"></i>
                    Rating: <?php echo number_format($toko_rating['total_rating'], 1); ?>/5.0 
                    (<?php echo $toko_rating['total_reviews']; ?> reviews)
                </p>
            </div>
            <div class="col-lg-4">
                <h5 class="mb-3">Kontak</h5>
                <p class="text-muted">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <?php echo htmlspecialchars($pengaturan['alamat']); ?>
                </p>
                <p class="text-muted">
                    <i class="fas fa-phone me-2"></i>
                    <?php echo htmlspecialchars($pengaturan['telepon']); ?>
                </p>
                <p class="text-muted">
                    <i class="fas fa-envelope me-2"></i>
                    <?php echo htmlspecialchars($pengaturan['email']); ?>
                </p>
            </div>
        </div>
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-muted">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($pengaturan['nama_toko']); ?>. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="admin/" class="text-muted text-decoration-none">
                    <i class="fas fa-cog me-1"></i>Admin Dashboard
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Konfirmasi Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memesan:</p>
                <div class="order-details">
                    <h6 id="modal-menu-name"></h6>
                    <p class="text-primary fw-bold" id="modal-menu-price"></p>
                </div>
                <p class="text-muted">Anda akan diarahkan ke WhatsApp untuk melanjutkan pemesanan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirm-order" class="btn btn-success" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp me-1"></i>Ya, Pesan Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<button id="scrollToTop" class="scroll-to-top" style="display: none;">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>

<script>
window.whatsappNumber = '<?php echo $pengaturan['whatsapp']; ?>';

document.getElementById('komentar').addEventListener('input', function() {
    const maxLength = 500;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    let helpText = this.parentNode.querySelector('.form-text');
    helpText.textContent = `${remaining} karakter tersisa`;
    
    if (remaining < 50) {
        helpText.style.color = '#dc3545';
    } else {
        helpText.style.color = '#6c757d';
    }
});
</script>

</body>
</html>