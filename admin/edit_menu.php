<?php
require_once '../config/database.php';
require_admin_login();

$success_message = '';
$error_message = '';
$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($menu_id <= 0) {
    header("Location: index.php");
    exit();
}

$query_menu = "SELECT m.*, k.nama_kategori FROM menu m 
               LEFT JOIN kategori k ON m.kategori_id = k.id 
               WHERE m.id = $menu_id";
$result_menu = mysqli_query($conn, $query_menu);

if (mysqli_num_rows($result_menu) == 0) {
    header("Location: index.php");
    exit();
}

$menu_data = mysqli_fetch_assoc($result_menu);

$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori";
$result_kategori = mysqli_query($conn, $query_kategori);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_menu = clean_input($_POST['nama_menu']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $kategori_id = (int)$_POST['kategori_id'];
    $status = clean_input($_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $hapus_gambar = isset($_POST['hapus_gambar']) ? 1 : 0;
    
    if (empty($nama_menu) || empty($deskripsi) || $harga <= 0 || $kategori_id <= 0) {
        $error_message = 'Mohon lengkapi semua field yang wajib diisi!';
    } else {
        $gambar_name = $menu_data['gambar']; 
        $upload_dir = '../assets/images/menu/';
        
        if ($hapus_gambar && !empty($menu_data['gambar'])) {
            $old_image_path = $upload_dir . $menu_data['gambar'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
            $gambar_name = '';
        }
        
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $max_size = 5 * 1024 * 1024; 
            
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $file_size = $_FILES['gambar']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_types)) {
                $error_message = 'Format file tidak didukung! Gunakan JPG, JPEG, PNG, GIF, atau WebP.';
            } elseif ($file_size > $max_size) {
                $error_message = 'Ukuran file terlalu besar! Maksimal 5MB.';
            } else {
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                if (!empty($menu_data['gambar'])) {
                    $old_image_path = $upload_dir . $menu_data['gambar'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                
                $gambar_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = $upload_dir . $gambar_name;
                
                if (!move_uploaded_file($file_tmp, $upload_path)) {
                    $error_message = 'Gagal mengupload gambar! Silakan coba lagi.';
                    $gambar_name = $menu_data['gambar']; 
                }
            }
        }
        
        if (empty($error_message)) {
            $query_update = "UPDATE menu SET 
                            nama_menu = '$nama_menu',
                            deskripsi = '$deskripsi',
                            harga = $harga,
                            kategori_id = $kategori_id,
                            gambar = '$gambar_name',
                            status = '$status',
                            is_featured = $is_featured,
                            updated_at = NOW()
                            WHERE id = $menu_id";
            
            if (mysqli_query($conn, $query_update)) {
                $success_message = 'Menu berhasil diupdate!';
                
                $result_menu = mysqli_query($conn, $query_menu);
                $menu_data = mysqli_fetch_assoc($result_menu);
            } else {
                $error_message = 'Gagal mengupdate menu: ' . mysqli_error($conn);
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
                        <a class="nav-link" href="add_menu.php">
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
                <h1 class="h2">Edit Menu: <?php echo htmlspecialchars($menu_data['nama_menu']); ?></h1>
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

            <!-- Edit Menu Form -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>Form Edit Menu
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data" id="editMenuForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nama_menu" class="form-label">Nama Menu <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="nama_menu" 
                                               name="nama_menu" 
                                               value="<?php echo htmlspecialchars($menu_data['nama_menu']); ?>"
                                               required 
                                               maxlength="100">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                                                <option value="<?php echo $kategori['id']; ?>"
                                                        <?php echo ($menu_data['kategori_id'] == $kategori['id']) ? 'selected' : ''; ?>>
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
                                                  maxlength="500"><?php echo htmlspecialchars($menu_data['deskripsi']); ?></textarea>
                                        <div class="form-text">Maksimal 500 karakter</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="harga" 
                                               name="harga" 
                                               value="<?php echo $menu_data['harga']; ?>"
                                               required 
                                               min="1000" 
                                               max="1000000" 
                                               step="500">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="aktif" <?php echo ($menu_data['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="nonaktif" <?php echo ($menu_data['status'] == 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Current Image Display -->
                                    <?php if (!empty($menu_data['gambar'])): ?>
                                    <div class="col-12">
                                        <label class="form-label">Foto Menu Saat Ini:</label>
                                        <div class="current-image mb-3">
                                            <img src="../assets/images/menu/<?php echo htmlspecialchars($menu_data['gambar']); ?>" 
                                                 alt="<?php echo htmlspecialchars($menu_data['nama_menu']); ?>"
                                                 class="img-fluid rounded shadow"
                                                 style="max-height: 200px;">
                                            <div class="mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="1" id="hapus_gambar" name="hapus_gambar">
                                                    <label class="form-check-label text-danger" for="hapus_gambar">
                                                        <i class="fas fa-trash me-1"></i>Hapus foto ini
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="col-12">
                                        <label for="gambar" class="form-label">
                                            <?php echo !empty($menu_data['gambar']) ? 'Ganti Foto Menu' : 'Upload Foto Menu'; ?>
                                        </label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="gambar" 
                                               name="gambar" 
                                               accept="image/*">
                                        <div class="form-text">
                                            Format yang didukung: JPG, JPEG, PNG, GIF, WebP. Maksimal 5MB.
                                            <?php if (!empty($menu_data['gambar'])): ?>
                                                <br>Kosongkan jika tidak ingin mengganti foto.
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Image Preview -->
                                    <div class="col-12" id="imagePreview" style="display: none;">
                                        <label class="form-label">Preview Foto Baru:</label>
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
                                                   <?php echo $menu_data['is_featured'] ? 'checked' : ''; ?>>
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
                                                <i class="fas fa-save me-2"></i>Update Menu
                                            </button>
                                            <a href="index.php" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Batal
                                            </a>
                                            <a href="delete_menu.php?id=<?php echo $menu_id; ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                                                <i class="fas fa-trash me-2"></i>Hapus Menu
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Menu Info Sidebar -->
                <div class="col-lg-4">
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Menu</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID Menu:</strong></td>
                                    <td>#<?php echo $menu_data['id']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Kategori:</strong></td>
                                    <td><?php echo htmlspecialchars($menu_data['nama_kategori']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php if ($menu_data['status'] == 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Featured:</strong></td>
                                    <td>
                                        <?php if ($menu_data['is_featured']): ?>
                                            <span class="badge bg-warning">Ya</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat:</strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($menu_data['created_at'])); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate:</strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($menu_data['updated_at'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card shadow mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="../#menu" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-eye me-2"></i>Lihat di Website
                                </a>
                                <a href="add_menu.php" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus me-2"></i>Tambah Menu Baru
                                </a>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="window.location.reload()">
                                    <i class="fas fa-refresh me-2"></i>Refresh Halaman
                                </button>
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
.current-image img {
    border: 2px solid #e9ecef;
}
</style>

<script>
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
        
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 5MB.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
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

document.getElementById('editMenuForm').addEventListener('submit', function(e) {
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
    
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengupdate...';
    submitBtn.disabled = true;
});

document.getElementById('hapus_gambar')?.addEventListener('change', function() {
    const currentImage = document.querySelector('.current-image img');
    if (this.checked) {
        currentImage.style.opacity = '0.3';
        currentImage.style.filter = 'grayscale(100%)';
    } else {
        currentImage.style.opacity = '1';
        currentImage.style.filter = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?>