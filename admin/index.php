<?php
require_once '../config/database.php';
require_admin_login();

$query_total_menu = "SELECT COUNT(*) as total FROM menu WHERE status = 'aktif'";
$result_total_menu = mysqli_query($conn, $query_total_menu);
$total_menu = mysqli_fetch_assoc($result_total_menu)['total'];

$query_total_kategori = "SELECT COUNT(*) as total FROM kategori";
$result_total_kategori = mysqli_query($conn, $query_total_kategori);
$total_kategori = mysqli_fetch_assoc($result_total_kategori)['total'];

$query_featured_menu = "SELECT COUNT(*) as total FROM menu WHERE is_featured = 1 AND status = 'aktif'";
$result_featured_menu = mysqli_query($conn, $query_featured_menu);
$featured_menu = mysqli_fetch_assoc($result_featured_menu)['total'];

$query_recent_menu = "SELECT m.*, k.nama_kategori FROM menu m 
                      LEFT JOIN kategori k ON m.kategori_id = k.id 
                      ORDER BY m.created_at DESC LIMIT 5";
$result_recent_menu = mysqli_query($conn, $query_recent_menu);

$query_all_menu = "SELECT m.*, k.nama_kategori FROM menu m 
                   LEFT JOIN kategori k ON m.kategori_id = k.id 
                   ORDER BY m.created_at DESC";
$result_all_menu = mysqli_query($conn, $query_all_menu);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
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
                <h1 class="h2">Dashboard Admin</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="add_menu.php" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-plus me-1"></i>Tambah Menu
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_GET['success'])): ?>
                <?php if ($_GET['success'] == 'menu_deleted'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Menu berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <?php if ($_GET['error'] == 'invalid_id'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ID menu tidak valid!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php elseif ($_GET['error'] == 'menu_not_found'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Menu tidak ditemukan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Menu Aktif</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_menu; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-utensils fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Kategori</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_kategori; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Menu Featured</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $featured_menu; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-star fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Hari Ini</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo date('d M Y'); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Management Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Manajemen Menu</h6>
                    <a href="add_menu.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Tambah Menu Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Menu</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($menu = mysqli_fetch_assoc($result_all_menu)): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <strong><?php echo $menu['nama_menu']; ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo substr($menu['deskripsi'], 0, 50); ?>...</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $menu['nama_kategori']; ?></span>
                                    </td>
                                    <td><?php echo format_rupiah($menu['harga']); ?></td>
                                    <td>
                                        <?php if ($menu['status'] == 'aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($menu['is_featured']): ?>
                                            <span class="badge bg-warning">Featured</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark">Regular</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($menu['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="edit_menu.php?id=<?php echo $menu['id']; ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete_menu.php?id=<?php echo $menu['id']; ?>" 
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Menu Added -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Menu Terbaru Ditambahkan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php while ($recent = mysqli_fetch_assoc($result_recent_menu)): ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo $recent['nama_menu']; ?></h6>
                                    <p class="card-text">
                                        <small class="text-muted"><?php echo $recent['nama_kategori']; ?></small><br>
                                        <strong class="text-primary"><?php echo format_rupiah($recent['harga']); ?></strong>
                                    </p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo date('d M Y, H:i', strtotime($recent['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.text-xs {
    font-size: 0.7rem;
}
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
</style>

<?php include 'includes/footer.php'; ?>