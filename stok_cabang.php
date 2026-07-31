<?php
require 'function.php';
require 'cek.php';

$id_tujuan = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kunci akses URL khusus admin: admin hanya boleh membuka 4 tujuan ini
if ($_SESSION['level'] == 'admin') {
    $cek_akses_admin = mysqli_query($conn, "
        SELECT id_tujuan 
        FROM tujuan 
        WHERE id_tujuan = '$id_tujuan'
        AND nama IN ('Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
    ");

    if (!$cek_akses_admin || mysqli_num_rows($cek_akses_admin) == 0) {
        header("Location: dashboard.php");
        exit();
    }
}

// Ambil data cabang
$query_cabang = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan='$id_tujuan'");
$cabang = mysqli_fetch_assoc($query_cabang);

if (!$cabang) {
    echo "<script>alert('Cabang tidak ditemukan!'); window.location.href='index.php';</script>";
    exit();
}

// User cabang: simpan informasi apakah ini cabangnya sendiri
$is_own_cabang = ($_SESSION['level'] == 'user_cabang' && $_SESSION['cabang_id'] == $id_tujuan);
// Admin bisa aksi di semua cabang
$can_act = ($_SESSION['level'] == 'admin' || $is_own_cabang);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Stok <?= $cabang['nama']; ?> - Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/animation.css">
    <link rel="stylesheet" href="assets/css/sidebar-fixed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            background-color: #343a40;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.toggled {
            transform: translateX(-100%);
        }

        .sidebar a {
            color: white;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .sidebar a i {
            width: 20px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        .sidebar a.active {
            background-color: #007bff;
        }

        .sidebar hr {
            margin: 5px 0;
            border-color: #555;
        }

        .sidebar h6 {
            font-size: 12px;
            margin: 10px 0 5px 0;
            padding-left: 15px;
            letter-spacing: 1px;
            font-weight: normal;
            color: #adb5bd;
        }

        /* DROPDOWN */
        .sidebar .dropdown-btn {
            cursor: pointer;
            user-select: none;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            font-size: 14px;
        }

        .sidebar .dropdown-btn:hover {
            background-color: #007bff;
        }

        .sidebar .dropdown-btn .fa-caret-down {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .sidebar .dropdown-btn .fa-caret-down.rotate {
            transform: rotate(180deg);
        }

        .sidebar .dropdown-container {
            display: none;
            background-color: #2c3136;
            padding-left: 35px;
        }

        .sidebar .dropdown-container a {
            padding: 8px 15px;
            font-size: 13px;
        }

        .sidebar .dropdown-container a:hover {
            background-color: #007bff;
        }

        .sidebar .dropdown-container a.active {
            background-color: #007bff;
        }

        .content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }

        .sidebar.toggled+.content {
            margin-left: 0;
        }

        .sb-topnav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1039;
            background-color: #343a40 !important;
            height: 56px;
        }

        .content {
            margin-top: 56px;
            margin-left: 260px;
            padding: 20px;
        }

        #sidebarToggle {
            color: white;
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        #sidebarToggle:hover {
            color: #007bff;
        }

        .stok-badge {
            font-size: 14px;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .stok-aman {
            background-color: #d4edda;
            color: #155724;
        }

        .stok-menipis {
            background-color: #fff3cd;
            color: #856404;
        }

        .stok-habis {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* TABEL STOK CABANG */
        .table-cabang td:nth-child(3) {
            /* Kolom Nama Material */
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-cabang td:nth-child(3):hover {
            white-space: normal;
            word-break: break-word;
            background-color: #f8f9fc;
        }

        /* TABEL RIWAYAT MASUK CABANG */
        .table-riwayat-masuk td:nth-child(4) {
            /* Kolom Nama Material */
            max-width: 280px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-riwayat-masuk td:nth-child(4):hover {
            white-space: normal;
            word-break: break-word;
            background-color: #f8f9fc;
        }
    

        /* ===== TAMPILAN SIDEBAR/TOPBAR PATOKAN DASHBOARD ===== */
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b5ed7;
            --sidebar: #182230;
            --sidebar-soft: #202d3e;
            --page-bg: #f5f7fb;
            --text-main: #111827;
            --text-soft: #6b7280;
            --border: #e5e7eb;
        }

        body {
            background: var(--page-bg);
            color: var(--text-main);
            font-size: 14px;
        }

        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: 260px !important;
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%) !important;
            overflow-y: auto !important;
            z-index: 1000 !important;
            box-shadow: 4px 0 18px rgba(15, 23, 42, .22) !important;
            transition: transform 0.5s ease !important;
        }

        .sidebar.toggled {
            transform: translateX(-100%) !important;
        }

        .sidebar .brand-sidebar {
            height: 56px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 18px;
            color: #fff;
            font-weight: 700;
            letter-spacing: .2px;
            background: #111827;
        }

        .sidebar .text-center {
            display: none !important;
        }

        .sidebar a,
        .sidebar .dropdown-btn {
            color: #dbeafe !important;
            padding: 11px 18px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            text-decoration: none !important;
            transition: all 0.2s ease !important;
            font-size: 14px !important;
            border-radius: 10px !important;
            margin: 4px 12px !important;
            background: transparent;
        }

        .sidebar a i,
        .sidebar .dropdown-btn i {
            width: 20px !important;
            text-align: center !important;
            font-size: 14px !important;
        }

        .sidebar a:hover,
        .sidebar a.active,
        .sidebar .dropdown-btn:hover {
            color: #fff !important;
            background: linear-gradient(135deg, #0d6efd, #38bdf8) !important;
            box-shadow: 0 8px 18px rgba(13, 110, 253, .28) !important;
        }

        .sidebar hr {
            margin: 10px 14px !important;
            border: 0 !important;
            border-top: 1px solid rgba(255, 255, 255, .08) !important;
        }

        .sidebar .dropdown-btn {
            cursor: pointer;
            user-select: none;
        }

        .sidebar .dropdown-btn .sb-nav-link-icon {
            width: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar .dropdown-btn .fa-caret-down {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .sidebar .dropdown-btn .fa-caret-down.rotate {
            transform: rotate(180deg);
        }

        .sidebar .dropdown-container {
            display: none;
            padding-left: 10px !important;
            margin-bottom: 4px !important;
            background: transparent !important;
        }

        .sidebar .dropdown-container a {
            padding: 9px 18px !important;
            font-size: 13px !important;
            margin-left: 26px !important;
        }

        .sidebar .dropdown-container a.active {
            color: #fff !important;
            background: linear-gradient(135deg, #0d6efd, #38bdf8) !important;
            box-shadow: 0 8px 18px rgba(13, 110, 253, .28) !important;
        }

        .content {
            margin-left: 260px !important;
            padding: 28px !important;
            min-height: 100vh;
            margin-top: 56px !important;
            transition: margin-left 0.4s ease !important;
        }

        body.sidebar-collapsed .content {
            margin-left: 0 !important;
        }

        .sb-topnav {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1039 !important;
            background: #111827 !important;
            height: 56px !important;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .18) !important;
        }

        #sidebarToggle {
            color: white !important;
            background: transparent !important;
            border: none !important;
            font-size: 20px !important;
            cursor: pointer;
        }

        #sidebarToggle:hover {
            color: #60a5fa !important;
        }

        .page-title h2 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 2px;
            color: var(--text-main);
        }

        .page-title p {
            color: var(--text-soft);
            margin-bottom: 18px;
        }

    </style>
</head>

<body>

    <?php tampilkanNotifikasi(); ?>

    
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <button class="btn btn-link btn-sm" id="sidebarToggle" type="button"><i class="fas fa-bars"></i></button>
        <span class="navbar-brand text-white"><i class="fas fa-warehouse mr-2 text-primary"></i>SISTEM GUDANG</span>
        <div class="ml-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-link text-white dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['email']); ?>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-item-text">
                        <small><strong>Level:</strong>
                            <?php if ($_SESSION['level'] == 'admin'): ?>
                                <span class="badge badge-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-info">User Cabang</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php if ($_SESSION['level'] == 'user_cabang' && isset($cabang['nama'])): ?>
                        <div class="dropdown-item-text"><small><strong>Cabang:</strong> <?= htmlspecialchars($cabang['nama']); ?></small></div>
                    <?php elseif ($_SESSION['level'] == 'admin'): ?>
                        <div class="dropdown-item-text"><small><strong>Cabang:</strong> Semua Cabang</small></div>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </div>
            </div>
        </div>
    </nav>


    <div class="sidebar">
        <div class="brand-sidebar">
            <i class="fas fa-warehouse text-primary"></i>
            <span>SISTEM GUDANG</span>
        </div>
        <nav>
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            <?php endif; ?>

            <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-warehouse"></i> Stok Gudang
            </a>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <a href="barang_masuk.php" class="<?= ($current_page == 'barang_masuk.php') ? 'active' : ''; ?>">
                    <i class="fas fa-arrow-down"></i> Material Masuk
                </a>
                <a href="barang_keluar.php" class="<?= ($current_page == 'barang_keluar.php') ? 'active' : ''; ?>">
                    <i class="fas fa-arrow-up"></i> Material Keluar
                </a>
            <?php endif; ?>

            <hr>
            <div class="dropdown-btn">
                <i class="fas fa-database"></i> MASTER DATA <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-container">
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <a href="user.php" class="<?= ($current_page == 'user.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> Daftar Pengguna
                    </a>
                <?php endif; ?>
                <a href="material.php" class="<?= ($current_page == 'material.php') ? 'active' : ''; ?>">
                    <i class="fas fa-boxes"></i> Daftar Material
                </a>
                <a href="tujuan.php" class="<?= ($current_page == 'tujuan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt"></i> Daftar Tujuan
                </a>
            </div>

            <hr>
            <div class="dropdown-btn">
                <i class="fas fa-building"></i> STOK CABANG <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-container">
                <?php
                if ($_SESSION['level'] == 'admin') {
                    $list_cabang_sidebar = mysqli_query($conn, "
                        SELECT * FROM tujuan 
                        WHERE nama IN ('Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                        ORDER BY FIELD(nama, 'Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                    ");
                } elseif ($_SESSION['level'] == 'user_cabang') {
                    $id_cabang_sidebar = (int)($_SESSION['cabang_id'] ?? 0);
                    $list_cabang_sidebar = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan = '$id_cabang_sidebar' ORDER BY id_tujuan ASC");
                } else {
                    $list_cabang_sidebar = mysqli_query($conn, "SELECT * FROM tujuan WHERE 1=0");
                }

                while ($c_sidebar = mysqli_fetch_assoc($list_cabang_sidebar)) {
                    $active_cabang = ($current_page == 'stok_cabang.php' && isset($_GET['id']) && $_GET['id'] == $c_sidebar['id_tujuan']) ? 'active' : '';
                ?>
                    <a href="stok_cabang.php?id=<?= $c_sidebar['id_tujuan']; ?>" class="<?= $active_cabang; ?>">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($c_sidebar['nama']); ?>
                    </a>
                <?php } ?>
            </div>

            <hr>
            <?php if ($_SESSION['level'] == 'user_cabang'): ?>
                <a href="permintaan_cabang.php" class="<?= ($current_page == 'permintaan_cabang.php') ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Permintaan Material
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <a href="daftar_permintaan.php" class="<?= ($current_page == 'daftar_permintaan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Daftar Permintaan
                </a>
                <a href="surat_jalan.php" class="<?= ($current_page == 'surat_jalan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-truck"></i> Surat Jalan
                </a>
                <a href="pengaturan.php" class="<?= ($current_page == 'pengaturan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <hr>
                <a href="laporan.php" class="<?= ($current_page == 'laporan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Laporan
                </a>
            <?php endif; ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </div>

<div class="content">
        <div class="page-title">
            <h2>Stok Material - <?= htmlspecialchars($cabang['nama']); ?></h2>
            <p>Daftar stok material dan aktivitas transaksi pada cabang ini.</p>
        </div>
<!-- Daftar Stok -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-boxes"></i> Daftar Stok Material
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-cabang" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Satuan</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT m.*, COALESCE(st.jumlah, 0) as stok 
                                FROM material m
                                LEFT JOIN stock_tujuan st ON st.id_material = m.id_material AND st.id_tujuan = '$id_tujuan'
                                WHERE COALESCE(st.jumlah, 0) > 0
                                ORDER BY m.nama ASC
                            ");
                            while ($data = mysqli_fetch_assoc($query)) {
                                $stok = $data['stok'];

                                if ($stok <= 0) {
                                    $status = '<span class="stok-badge stok-habis"><i class="fas fa-times-circle"></i> Habis</span>';
                                } elseif ($stok <= 10) {
                                    $status = '<span class="stok-badge stok-menipis"><i class="fas fa-exclamation-triangle"></i> Menipis</span>';
                                } else {
                                    $status = '<span class="stok-badge stok-aman"><i class="fas fa-check-circle"></i> Aman</span>';
                                }
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($data['kode_material']); ?></td>
                                    <td><?= htmlspecialchars($data['nama']); ?></td>
                                    <td><?= htmlspecialchars($data['satuan']); ?></td>
                                    <td><strong><?= number_format($stok, 0, ',', '.'); ?></strong></td>
                                    <td><?= $status; ?></td>
                                    <td>
                                        <?php if ($can_act): ?>
                                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalGunakan<?= $data['id_material']; ?>">
                                                <i class="fas fa-trash-alt"></i> Gunakan
                                            </button>
                                            <!-- <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalTransfer<?= $data['id_material']; ?>">
                                                <i class="fas fa-exchange-alt"></i>Transfer 
                                            </button> -->
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Terkunci
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                <tr>

                                    <!-- Modal Gunakan -->
                                    <div class="modal fade" id="modalGunakan<?= $data['id_material']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Gunakan Material</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="proses_gunakan_cabang.php">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_material" value="<?= $data['id_material']; ?>">
                                                        <input type="hidden" name="id_tujuan" value="<?= $id_tujuan; ?>">
                                                        <div class="form-group">
                                                            <label>Material</label>
                                                            <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Stok Tersedia</label>
                                                            <input type="text" class="form-control" value="<?= $stok; ?> <?= htmlspecialchars($data['satuan']); ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Jumlah yang Digunakan</label>
                                                            <input type="number" name="qty" class="form-control" placeholder="Jumlah" max="<?= $stok; ?>" min="1" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Tanggal</label>
                                                            <input type="date" name="tanggal" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Keterangan</label>
                                                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Untuk proyek A, Rusak, Dijual"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" name="gunakan_cabang" class="btn btn-danger">Gunakan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Transfer -->
                                    <div class="modal fade" id="modalTransfer<?= $data['id_material']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Transfer Material</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <form method="post" action="proses_transfer.php">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id_material" value="<?= $data['id_material']; ?>">
                                                        <input type="hidden" name="dari_tujuan" value="<?= $id_tujuan; ?>">
                                                        <div class="form-group">
                                                            <label>Material</label>
                                                            <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Stok Tersedia</label>
                                                            <input type="text" class="form-control" value="<?= $stok; ?> <?= htmlspecialchars($data['satuan']); ?>" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Transfer Ke Cabang</label>
                                                            <select name="ke_tujuan" class="form-control" required>
                                                                <option value="">Pilih Cabang Tujuan</option>
                                                                <?php
                                                                $cabang_lain = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan != '$id_tujuan' ORDER BY nama ASC");
                                                                while ($cl = mysqli_fetch_assoc($cabang_lain)) {
                                                                    echo "<option value='{$cl['id_tujuan']}'>{$cl['nama']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Jumlah Transfer</label>
                                                            <input type="number" name="qty" class="form-control" placeholder="Jumlah" max="<?= $stok; ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Tanggal Transfer</label>
                                                            <input type="date" name="tanggal" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Keterangan</label>
                                                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan transfer"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" name="transfer_cabang" class="btn btn-warning">Transfer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Material Masuk -->
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <i class="fas fa-history"></i> Riwayat Material Masuk ke <?= htmlspecialchars($cabang['nama']); ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-riwayat-masuk" id="dataTableMasuk" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Jumlah</th>
                                <th>Sumber</th>
                                <th>Asal Transfer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no2 = 1;
                            $query2 = mysqli_query($conn, "
                                SELECT t.*, m.kode_material, m.nama, m.satuan
                                FROM transaksi_masuk_tujuan t
                                JOIN material m ON m.id_material = t.id_material
                                WHERE t.id_tujuan = '$id_tujuan'
                                ORDER BY t.tanggal DESC
                            ");
                            while ($data2 = mysqli_fetch_assoc($query2)) {
                            ?>
                                <tr>
                                    <td><?= $no2++; ?></td>
                                    <td><?= tgl_panjang($data2['tanggal']); ?></td>
                                    <td><?= $data2['kode_material']; ?></td>
                                    <td><?= $data2['nama']; ?></td>
                                    <td><?= $data2['qty']; ?> <?= $data2['satuan']; ?></td>
                                    <td><?= htmlspecialchars($data2['sumber'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($data2['asal_transfer'] ?? '-'); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Transfer ke Cabang Lain -->
        <div class="card mt-4">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-paper-plane"></i> Riwayat Transfer ke Cabang Lain
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-cabang" id="dataTableTransfer" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Jumlah</th>
                                <th>Dikirim Ke</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no3 = 1;
                            $query3 = mysqli_query($conn, "
                                SELECT t.*, m.kode_material, m.nama, m.satuan, tj.nama as tujuan_nama
                                FROM transaksi_keluar_tujuan t
                                JOIN material m ON m.id_material = t.id_material
                                JOIN tujuan tj ON tj.id_tujuan = t.ke_tujuan
                                WHERE t.dari_tujuan = '$id_tujuan'
                                ORDER BY t.tanggal DESC
                            ");
                            while ($data3 = mysqli_fetch_assoc($query3)) {
                            ?>
                                <td>
                                <td><?= $no3++; ?></td>
                                <td><?= tgl_panjang($data3['tanggal']); ?></td>
                                <td><?= $data3['kode_material']; ?></td>
                                <td><?= $data3['nama']; ?></td>
                                <td><?= $data3['qty']; ?> <?= $data3['satuan']; ?></td>
                                <td><?= $data3['tujuan_nama'] ?? '-'; ?></td>
                                <td><?= $data3['keterangan'] ?? '-'; ?></td>
                                <td>
                                    <?php if ($can_act): ?>
                                        <button class="btn btn-sm btn-danger" onclick="hapusTransfer(<?= $data3['id_transaksi']; ?>, <?= $data3['id_material']; ?>, <?= $data3['qty']; ?>, <?= $id_tujuan; ?>, <?= $data3['ke_tujuan']; ?>)">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-lock"></i> Terkunci
                                        </button>
                                    <?php endif; ?>
                                </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Material Digunakan -->
        <div class="card mt-4">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-trash-alt"></i> Riwayat Material Keluar (Digunakan)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-cabang" id="dataTableGunakan" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no4 = 1;
                            $query4 = mysqli_query($conn, "
                                SELECT t.*, m.kode_material, m.nama, m.satuan
                                FROM transaksi_keluar_dari_cabang t
                                JOIN material m ON m.id_material = t.id_material
                                WHERE t.id_tujuan = '$id_tujuan'
                                ORDER BY t.tanggal DESC
                            ");
                            while ($data4 = mysqli_fetch_assoc($query4)) {
                            ?>
                                <tr>
                                    <td><?= $no4++; ?></td>
                                    <td><?= tgl_panjang($data4['tanggal']); ?></td>
                                    <td><?= $data4['kode_material']; ?></td>
                                    <td><?= $data4['nama']; ?></td>
                                    <td><?= $data4['qty']; ?> <?= $data4['satuan']; ?></td>
                                    <td><?= $data4['satuan']; ?></td>
                                    <td><?= $data4['keterangan'] ?? '-'; ?></td>
                                    <td>
                                        <?php if ($can_act): ?>
                                            <button class="btn btn-sm btn-danger" onclick="hapusGunakan(<?= $data4['id_transaksi']; ?>, <?= $data4['id_material']; ?>, <?= $data4['qty']; ?>, <?= $id_tujuan; ?>)">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Terkunci
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            var bahasaDataTable = {
                lengthMenu: "Tampilkan _MENU_ baris",
                zeroRecords: "Data tidak ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ baris",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 baris",
                infoFiltered: "(difilter dari _MAX_ total baris)",
                search: "Cari:",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    previous: "Sebelumnya",
                    next: "Selanjutnya"
                }
            };

            $('#dataTable').DataTable({ pageLength: 10, language: bahasaDataTable });
            $('#dataTableMasuk').DataTable({ pageLength: 10, language: bahasaDataTable });
            $('#dataTableTransfer').DataTable({ pageLength: 10, language: bahasaDataTable });
            $('#dataTableGunakan').DataTable({ pageLength: 10, language: bahasaDataTable });
        });

        function hapusTransfer(id, id_material, qty, dari_tujuan, ke_tujuan) {
            Swal.fire({
                title: 'Yakin ingin membatalkan transfer?',
                text: "Stok akan dikembalikan ke cabang asal!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_transaksi.php?type=transfer&id=' + id + '&id_material=' + id_material + '&qty=' + qty + '&dari_tujuan=' + dari_tujuan + '&ke_tujuan=' + ke_tujuan;
                }
            });
        }

        function hapusGunakan(id, id_material, qty, id_tujuan) {
            Swal.fire({
                title: 'Yakin ingin membatalkan?',
                text: "Stok akan dikembalikan ke cabang!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_transaksi.php?type=gunakan&id=' + id + '&id_material=' + id_material + '&qty=' + qty + '&id_tujuan=' + id_tujuan;
                }
            });
        }

        // ========== DROPDOWN DENGAN STATE TERSIMPAN (localStorage) ==========
        var dropdown = document.getElementsByClassName("dropdown-btn");

        function saveDropdownState(index, isOpen) {
            localStorage.setItem('dropdownState_' + index, isOpen ? 'open' : 'closed');
        }

        function loadDropdownState(index) {
            return localStorage.getItem('dropdownState_' + index) === 'open';
        }

        for (var i = 0; i < dropdown.length; i++) {
            var btnIndex = i;
            var dropdownContent = dropdown[i].nextElementSibling;

            if (loadDropdownState(btnIndex)) {
                dropdownContent.style.display = "block";
                dropdown[i].querySelector('.fa-caret-down').classList.add('rotate');
            }

            dropdown[i].addEventListener("click", function(index) {
                return function() {
                    this.classList.toggle("active");
                    var dropdownContent = this.nextElementSibling;
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                        this.querySelector('.fa-caret-down').classList.remove('rotate');
                        saveDropdownState(index, false);
                    } else {
                        dropdownContent.style.display = "block";
                        this.querySelector('.fa-caret-down').classList.add('rotate');
                        saveDropdownState(index, true);
                    }
                };
            }(i));
        }
        // ========== SAMPAI SINI ==========

        // ========== TOGGLE SIDEBAR + KONTEN IKUT MEMBESAR ==========
        var sidebarToggle = document.getElementById('sidebarToggle');
        var sidebar = document.querySelector('.sidebar');

        function setSidebarState(isCollapsed) {
            if (!sidebar) return;
            sidebar.classList.toggle('toggled', isCollapsed);
            document.body.classList.toggle('sidebar-collapsed', isCollapsed);
            localStorage.setItem('sidebarToggled', isCollapsed ? 'true' : 'false');
        }

        if (sidebarToggle && sidebar) {
            var savedState = localStorage.getItem('sidebarToggled') === 'true';
            setSidebarState(savedState);

            sidebarToggle.addEventListener('click', function() {
                var isCollapsed = sidebar.classList.contains('toggled');
                setSidebarState(!isCollapsed);
            });
        }
    </script>
</body>

</html>