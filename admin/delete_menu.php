<?php
require_once '../config/database.php';
require_admin_login();

$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($menu_id <= 0) {
    header("Location: index.php?error=invalid_id");
    exit();
}

$query_menu = "SELECT * FROM menu WHERE id = $menu_id";
$result_menu = mysqli_query($conn, $query_menu);

if (mysqli_num_rows($result_menu) == 0) {
    header("Location: index.php?error=menu_not_found");
    exit();
}

$menu_data = mysqli_fetch_assoc($result_menu);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    if (!empty($menu_data['gambar'])) {
        $image_path = '../assets/images/menu/' . $menu_data['gambar'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $query_delete = "DELETE FROM menu WHERE id = $menu_id";
    
    if (mysqli_query($conn, $query_delete)) {
        header("Location: index.php?success=menu_deleted");
        exit();
    } else {
        $error_message = 'Gagal menghapus menu: ' . mysqli_error($conn);
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
                <h1 class="h2 text-danger">
                    <i class="fas fa-trash me-2"></i>Hapus Menu
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Confirmation Card -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Konfirmasi Penghapusan Menu
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-warning me-2"></i>
                                <strong>Perhatian!</strong> Tindakan ini tidak dapat dibatalkan. Menu yang dihapus akan hilang permanen dari sistem.
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <!-- Menu Image -->
                                    <div class="menu-preview text-center">
                                        <?php if (!empty($menu_data['gambar']) && file_exists('../assets/images/menu/' . $menu_data['gambar'])): ?>
                                            <img src="../assets/images/menu/<?php echo htmlspecialchars($menu_data['gambar']); ?>" 
                                                 alt="<?php echo htmlspecialchars($menu_data['nama_menu']); ?>"
                                                 class="img-fluid rounded shadow"
                                                 style="max-height: 200px;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                 style="height: 200px;">
                                                <i class="fas fa-bowl-food fa-4x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <!-- Menu Details -->
                                    <h4 class="text-danger mb-3"><?php echo htmlspecialchars($menu_data['nama_menu']); ?></h4>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID Menu:</strong></td>
                                            <td>#<?php echo $menu_data['id']; ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Deskripsi:</strong></td>
                                            <td><?php echo htmlspecialchars($menu_data['deskripsi']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Harga:</strong></td>
                                            <td class="text-primary fw-bold">Rp <?php echo number_format($menu_data['harga'], 0, ',', '.'); ?></td>
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
                                            <td><strong>Foto:</strong></td>
                                            <td>
                                                <?php if (!empty($menu_data['gambar'])): ?>
                                                    <span class="text-success">Ada foto</span>
                                                    <small class="text-muted d-block"><?php echo $menu_data['gambar']; ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada foto</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <hr>

                            <div class="text-center">
                                <p class="text-muted mb-4">
                                    Apakah Anda yakin ingin menghapus menu <strong>"<?php echo htmlspecialchars($menu_data['nama_menu']); ?>"</strong>?
                                    <?php if (!empty($menu_data['gambar'])): ?>
                                        <br><small>Foto menu juga akan dihapus dari server.</small>
                                    <?php endif; ?>
                                </p>

                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="confirm_delete" value="1">
                                    <button type="submit" class="btn btn-danger me-3" id="deleteBtn">
                                        <i class="fas fa-trash me-2"></i>Ya, Hapus Menu
                                    </button>
                                </form>
                                
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                                
                                <a href="edit_menu.php?id=<?php echo $menu_id; ?>" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-edit me-2"></i>Edit Menu Ini
                                </a>
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
.menu-preview img {
    border: 2px solid #dc3545;
}
</style>

<script>
document.getElementById('deleteBtn').addEventListener('click', function(e) {
    e.preventDefault();
    
    const menuName = "<?php echo addslashes($menu_data['nama_menu']); ?>";
    
    if (confirm(`Apakah Anda BENAR-BENAR yakin ingin menghapus menu "${menuName}"?\n\nTindakan ini tidak dapat dibatalkan!`)) {
        if (confirm('Konfirmasi sekali lagi: Hapus menu ini secara permanen?')) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menghapus...';
            this.disabled = true;
            
            this.closest('form').submit();
        }
    }
});

window.addEventListener('beforeunload', function(e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>

<?php include 'includes/footer.php'; ?>