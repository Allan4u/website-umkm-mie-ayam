<?php
require_once '../config/database.php';
require_admin_login();

$success_message = '';
$error_message = '';

$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori";
$result_kategori = mysqli_query($conn, $query_kategori);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_menu = clean_input($_POST['nama_menu']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $kategori_id = (int)$_POST['kategori_id'];
    $status = clean_input($_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    if (empty($nama_menu) || empty($deskripsi) || $harga <= 0 || $kategori_id <= 0) {
        $error_message = 'Mohon lengkapi semua field yang wajib diisi!';
    } else {
        $gambar_name = '';
        
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $file_size = $_FILES['gambar']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_types)) {
                $error_message = 'Format file tidak didukung! Gunakan JPG, JPEG, PNG, GIF, atau WebP.';
            } elseif ($file_size > $max_size) {
                $error_message = 'Ukuran file terlalu besar! Maksimal 5MB.';
            } else {
                $upload_dir = '../assets/images/menu/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $gambar_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = $upload_dir . $gambar_name;
                
                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    $error_message = 'Gagal mengupload gambar! Silakan coba lagi.';
                    $gambar_name = '';
                }
            }
        }
        
        if (empty($error_message)) {
            $query_insert = "INSERT INTO menu (nama_menu, deskripsi, harga, kategori_id, gambar, status, is_featured) 
                            VALUES ('$nama_menu', '$deskripsi', $harga, $kategori_id, '$gambar_name', '$status', $is_featured)";
            
            if (mysqli_query($conn, $query_insert)) {
                $success_message = 'Menu berhasil ditambahkan!';
                $_POST = array();
            } else {
                $error_message = 'Gagal menambahkan menu: ' . mysqli_error($conn);
                
                if (!empty($gambar_name) && file_exists($upload_dir . $gambar_name)) {
                    unlink($upload_dir . $gambar_name);
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="add_menu.php">
                            <i class="fas fa-plus me-2"></i>Tambah Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../" target="_blank">
                            <i class="fas fa-eye me-2"></i>Lihat Website
                        </a>
                    </li>
                </ul>
                
                <hr>
                
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Tambah Menu Baru</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Add Menu Form -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-utensils me-2"></i>Form Tambah Menu
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data" id="addMenuForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nama_menu" 
                                               name="nama_menu" 
                                               value="<?php echo isset($_POST['nama_menu']) ? htmlspecialchars($_POST['nama_menu']) : ''; ?>"
                                               required 
                                               maxlength="100"
                                               placeholder="Contoh: Mie Ayam Special">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                                                <option value="<?php echo $kategori['id']; ?>"
                                                        <?php echo (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $kategori['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="deskripsi" class="form-label">Deskripsi Menu <span class="text-danger">*</span></label>
                                        <textarea class="form-control" 
                                                  id="deskripsi" 
                                                  name="deskripsi" 
                                                  rows="3" 
                                                  required 
                                                  maxlength="500"
                                                  placeholder="Deskripsikan menu dengan detail..."><?php echo isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : ''; ?></textarea>
                                        <div class="form-text">Maksimal 500 karakter</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="harga" 
                                               name="harga" 
                                               value="<?php echo isset($_POST['harga']) ? $_POST['harga'] : ''; ?>"
                                               required 
                                               min="1000" 
                                               max="1000000" 
                                               step="500"
                                               placeholder="15000">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="aktif" <?php echo (isset($_POST['status']) && $_POST['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="nonaktif" <?php echo (isset($_POST['status']) && $_POST['status'] == 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="gambar" class="form-label">Foto Menu (Opsional)</label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="gambar" 
                                               name="gambar" 
                                               accept="image/*">
                                        <div class="form-text">
                                            Format yang didukung: JPG, JPEG, PNG, GIF, WebP. Maksimal 5MB.
                                        </div>
                                    </div>
                                    
                                    <!-- Image Preview -->
                                    <div class="col-12" id="imagePreview" style="display: none;">
                                        <label class="form-label">Preview Foto:</label>
                                        <div class="border rounded p-3 text-center">
                                            <img id="previewImg" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   value="1" 
                                                   id="is_featured" 
                                                   name="is_featured"
                                                   <?php echo (isset($_POST['is_featured'])) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_featured">
                                                <strong>Menu Featured</strong>
                                                <br><small class="text-muted">Menu ini akan ditampilkan di section "Menu Andalan"</small>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Simpan Menu
                                            </button>
                                            <button type="reset" class="btn btn-secondary" onclick="resetForm()">
                                                <i class="fas fa-undo me-2"></i>Reset Form
                                            </button>
                                            <a href="index.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Batal
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Guidelines -->
            <div class="row justify-content-center mt-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Panduan Upload Foto</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <h6 class="text-success"><i class="fas fa-check me-2"></i>Yang Dianjurkan:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Resolusi minimal 800x600 pixel</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Format JPG atau PNG</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Ukuran file 1-3 MB</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Foto yang terang dan jelas</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Fokus pada makanan</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-danger"><i class="fas fa-times me-2"></i>Yang Harus Dihindari:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-times text-danger me-2"></i>Foto blur atau gelap</li>
                                        <li><i class="fas fa-times text-danger me-2"></i>Ukuran file lebih dari 5MB</li>
                                        <li><i class="fas fa-times text-danger me-2"></i>Watermark atau logo</li>
                                        <li><i class="fas fa-times text-danger me-2"></i>Format selain yang diizinkan</li>
                                        <li><i class="fas fa-times text-danger me-2"></i>Foto dengan background berantakan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<style>
.sidebar {
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}
.sidebar .nav-link {
    color: #333;
}
.sidebar .nav-link.active {
    color: #007bff;
    background-color: rgba(0, 123, 255, .1);
}
.sidebar .nav-link:hover {
    color: #007bff;
}
#imagePreview img {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<script>
// Image preview functionality
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak didukung! Gunakan JPG, JPEG, PNG, GIF, atau WebP.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Character counter for description
document.getElementById('deskripsi').addEventListener('input', function() {
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

// Reset form function
function resetForm() {
    document.getElementById('addMenuForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
    
    // Reset character counter
    const helpText = document.querySelector('#deskripsi').parentNode.querySelector('.form-text');
    helpText.textContent = 'Maksimal 500 karakter';
    helpText.style.color = '#6c757d';
}

// Form validation
document.getElementById('addMenuForm').addEventListener('submit', function(e) {
    const nama = document.getElementById('nama_menu').value.trim();
    const deskripsi = document.getElementById('deskripsi').value.trim();
    const harga = document.getElementById('harga').value;
    const kategori = document.getElementById('kategori_id').value;
    
    if (!nama || !deskripsi || !harga || !kategori) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi!');
        return false;
    }
    
    if (parseFloat(harga) < 1000) {
        e.preventDefault();
        alert('Harga minimal Rp 1.000!');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
});

// Auto focus on nama_menu field
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nama_menu').focus();
});
</script>

<?php include 'includes/footer.php'; ?>