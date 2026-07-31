<?php
require 'function.php';
require 'cek.php';

// Hanya admin yang bisa akses
if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Permintaan Material - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/animation.css">
    <link rel="stylesheet" href="assets/css/sidebar-fixed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        /* style sama seperti file daftar_permintaan Anda yang lama */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            background-color: #343a40;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar.toggled {
            display: none;
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

        .content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
            margin-top: 56px;
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

        /* Status dibuat sederhana seperti badge surat jalan */
        .status-badge {
            display: inline-block !important;
            padding: 6px 10px !important;
            border-radius: 4px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            min-width: auto !important;
            white-space: nowrap !important;
            text-align: center !important;
            box-shadow: none !important;
            border: none !important;
        }

        /* Hilangkan ikon warning */
        .status-perlu_perbaikan::before {
            content: none !important;
        }

        .status-pending {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .status-perlu_perbaikan {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .status-dikirim {
            background-color: #007bff !important;
            color: #fff !important;
        }

        .status-selesai {
            background-color: #28a745 !important;
            color: #fff !important;
        }

        .status-ditolak {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .status-disetujui {
            background-color: #17a2b8 !important;
            color: #fff !important;
        }

        .status-dikirim {
            background-color: #dbeafe;
            color: #1d4ed8;
            border-color: #93c5fd;
        }

        .status-selesai {
            background-color: #dcfce7;
            color: #166534;
            border-color: #86efac;
        }

        .status-ditolak {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .status-perlu_perbaikan {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffe69c;
        }

        .status-perlu_perbaikan::before {
            content: "\f071";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            font-size: 12px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn-action {
            margin: 2px;
        }

        /* Perbaikan tabel daftar permintaan */
        .table-permintaan {
            width: 100%;
            min-width: 1320px;
            font-size: 13px;
        }

        .table-permintaan th {
            background: #ffffff;
            color: #111827;
            font-weight: 700;
            vertical-align: middle !important;
            white-space: nowrap;
            padding: 12px 10px;
        }

        .table-permintaan td {
            vertical-align: top !important;
            padding: 10px;
            line-height: 1.45;
        }

        /* No */
        .table-permintaan th:nth-child(1),
        .table-permintaan td:nth-child(1) {
            width: 55px;
            text-align: center;
        }

        /* No. Permintaan */
        .table-permintaan th:nth-child(2),
        .table-permintaan td:nth-child(2) {
            width: 145px;
            white-space: nowrap;
        }

        /* Tanggal */
        .table-permintaan th:nth-child(3),
        .table-permintaan td:nth-child(3) {
            width: 120px;
            white-space: nowrap;
        }

        /* Cabang */
        .table-permintaan th:nth-child(4),
        .table-permintaan td:nth-child(4) {
            width: 180px;
            white-space: nowrap;
        }

        /* Material */
        .table-permintaan th:nth-child(5),
        .table-permintaan td:nth-child(5) {
            width: 370px;
            max-width: 370px;
        }

        /* Status */
        .table-permintaan th:nth-child(6),
        .table-permintaan td:nth-child(6) {
            width: 125px;
            text-align: center;
        }

        /* Catatan */
        .table-permintaan th:nth-child(7),
        .table-permintaan td:nth-child(7) {
            width: 240px;
            max-width: 240px;
        }

        /* Surat Jalan */
        .table-permintaan th:nth-child(8),
        .table-permintaan td:nth-child(8) {
            width: 115px;
            text-align: center;
        }

        /* Aksi */
        .table-permintaan th:nth-child(9),
        .table-permintaan td:nth-child(9) {
            width: 140px;
            text-align: center;
        }

        .material-ringkas {
            max-width: 350px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .material-ringkas:hover {
            white-space: normal;
            word-break: break-word;
            background-color: #f8f9fc;
            padding: 6px;
            border-radius: 6px;
        }

        .catatan-ringkas {
            max-height: 82px;
            overflow-y: auto;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px;
            font-size: 13px;
        }

        .table-permintaan .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 110px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            text-align: center;
        }

        .table-permintaan .badge {
            font-size: 12px;
            padding: 6px 8px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .table-permintaan .btn-action {
            margin: 2px;
            padding: 7px 10px;
            font-size: 13px;
            border-radius: 8px;
            line-height: 1.3;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .table-permintaan tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .table-permintaan tbody tr:hover {
            background-color: #eef6ff;
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
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 18px rgba(15, 23, 42, .22);
        }

        .sidebar.toggled {
            display: none;
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
            color: #dbeafe;
            padding: 11px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
            border-radius: 10px;
            margin: 4px 12px;
            background: transparent;
        }

        .sidebar a i,
        .sidebar .dropdown-btn i {
            width: 20px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active,
        .sidebar .dropdown-btn:hover {
            color: #fff;
            background: linear-gradient(135deg, #0d6efd, #38bdf8);
            box-shadow: 0 8px 18px rgba(13, 110, 253, .28);
        }

        .sidebar hr {
            margin: 10px 14px;
            border-color: rgba(255, 255, 255, .08);
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
            padding-left: 10px;
            margin-bottom: 4px;
            background: transparent;
        }

        .sidebar .dropdown-container a {
            padding: 9px 18px;
            font-size: 13px;
            margin-left: 26px;
        }

        .sidebar .dropdown-container a.active {
            color: #fff;
            background: linear-gradient(135deg, #0d6efd, #38bdf8);
            box-shadow: 0 8px 18px rgba(13, 110, 253, .28);
        }

        .content {
            margin-left: 260px;
            padding: 28px;
            min-height: 100vh;
            margin-top: 56px;
        }

        .sb-topnav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1039;
            background: #111827 !important;
            height: 56px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .18);
        }

        #sidebarToggle {
            color: white;
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        #sidebarToggle:hover {
            color: #60a5fa;
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
                    <div class="dropdown-item-text"><small><strong>Level:</strong> <span class="badge badge-danger">Admin</span></small></div>
                    <div class="dropdown-item-text"><small><strong>Cabang:</strong> Semua Cabang</small></div>
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
                if ($_SESSION['level'] == 'user_cabang') {
                    $id_cabang_sidebar = (int)($_SESSION['cabang_id'] ?? 0);
                    $list_cabang_sidebar = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan = '$id_cabang_sidebar' ORDER BY id_tujuan ASC");
                } elseif ($_SESSION['level'] == 'admin') {
                    $list_cabang_sidebar = mysqli_query($conn, "
                        SELECT * FROM tujuan
                        WHERE nama IN ('Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                        ORDER BY FIELD(nama, 'Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                    ");
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

            <hr>
            <a href="laporan.php" class="<?= ($current_page == 'laporan.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Laporan
            </a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </div>

    <div class="content">
        <div class="page-title">
            <h2>Daftar Permintaan Material</h2>
            <p>Kelola permintaan material dari cabang dan proses pengiriman material.</p>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-list"></i> Semua Permintaan dari Cabang
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-permintaan" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Permintaan</th>
                                <th>Tanggal</th>
                                <th>Cabang</th>
                                <th>Material</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Surat Jalan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT p.*, t.nama as cabang_nama,
                                    GROUP_CONCAT(CONCAT(m.kode_material, ' - ', m.nama, ' (', pd.jumlah, ' ', m.satuan, ')') SEPARATOR '<br>') as detail_material
                                FROM permintaan p
                                JOIN tujuan t ON t.id_tujuan = p.id_tujuan
                                LEFT JOIN permintaan_detail pd ON pd.id_permintaan = p.id_permintaan
                                LEFT JOIN material m ON m.id_material = pd.id_material
                                GROUP BY p.id_permintaan
                                ORDER BY 
                                    CASE 
                                        WHEN LOWER(p.status) = 'pending' THEN 0 
                                        ELSE 1 
                                    END,
                                    p.id_permintaan DESC
                            ");
                            while ($data = mysqli_fetch_assoc($query)) {
                                $status_class = '';
                                $status_text = ucwords(str_replace('_', ' ', $data['status']));
                                switch ($data['status']) {
                                    case 'pending':
                                        $status_class = 'status-pending';
                                        break;
                                    case 'disetujui':
                                        $status_class = 'status-disetujui';
                                        break;
                                    case 'dikirim':
                                        $status_class = 'status-dikirim';
                                        break;
                                    case 'selesai':
                                        $status_class = 'status-selesai';
                                        break;
                                    case 'ditolak':
                                        $status_class = 'status-ditolak';
                                        break;
                                    case 'perlu_perbaikan':
                                        $status_class = 'status-perlu_perbaikan';
                                        break;
                                }

                                // Cek apakah surat jalan sudah dibuat
                                $cek_surat = mysqli_query($conn, "SELECT * FROM surat_jalan WHERE id_permintaan = '{$data['id_permintaan']}'");
                                $sudah_buat_surat = mysqli_num_rows($cek_surat) > 0;
                                $surat_info = $sudah_buat_surat ? '<span class="badge badge-success">Sudah dibuat</span>' : '<span class="badge badge-danger">Belum dibuat</span>';
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $data['no_permintaan']; ?></td>
                                    <td><?= tgl_panjang($data['tanggal_permintaan']); ?></td>
                                    <td><?= htmlspecialchars($data['cabang_nama']); ?></td>
                                    <td>
                                        <div class="material-ringkas" title="<?= htmlspecialchars(strip_tags(str_replace('<br>', ' | ', $data['detail_material'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?= $data['detail_material']; ?>
                                        </div>
                                    </td>
                                    <td><span class="status-badge <?= $status_class; ?>"><?= $status_text; ?></span></td>
                                    <td>
                                        <?php
                                        $catatan_permintaan = trim($data['catatan'] ?? '');
                                        ?>

                                        <?php if ($catatan_permintaan != ''): ?>
                                            <div class="catatan-ringkas">
                                                <?= nl2br(htmlspecialchars($catatan_permintaan)); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $surat_info; ?></td>
                                    <td>
                                        <?php if ($data['status'] == 'pending'): ?>
                                            <button class="btn btn-sm btn-success btn-action" onclick="setujuiPermintaan(<?= $data['id_permintaan']; ?>)">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                            <button class="btn btn-sm btn-danger btn-action" onclick="tolakPermintaan(<?= $data['id_permintaan']; ?>)">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        <?php elseif ($data['status'] == 'disetujui'): ?>
                                            <?php if ($sudah_buat_surat): ?>
                                                <button class="btn btn-sm btn-primary btn-action" onclick="kirimBarang(<?= $data['id_permintaan']; ?>)">
                                                    <i class="fas fa-truck"></i> Kirim Material
                                                </button>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Buat surat jalan dulu</span>
                                            <?php endif; ?>
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

    <!-- Flatpickr (jika ada) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        // === FLATPICKR (jika pakai) ===
        flatpickr("input[name='tanggal_permintaan'], input[name='tanggal_terima']", {
            dateFormat: "Y-m-d",
            altFormat: "d-m-Y",
            locale: "id",
            altInput: true,
            allowInput: true,
            disableMobile: true
        });

        // === DATATABLE ===
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "pageLength": 10,
                "language": {
                    "emptyTable": "Tidak ada data pada tabel",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ baris",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 baris",
                    "infoFiltered": "(difilter dari _MAX_ total baris)",
                    "lengthMenu": "Tampilkan _MENU_ baris",
                    "loadingRecords": "Memuat...",
                    "processing": "Sedang memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Data tidak ditemukan",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "aria": {
                        "sortAscending": ": aktifkan untuk mengurutkan kolom naik",
                        "sortDescending": ": aktifkan untuk mengurutkan kolom turun"
                    }
                }
            });

            // Kode tambahan untuk tambah baris (jika ada)
            $('#tambahBaris').click(function() {
                var newRow = $('.item-row:first').clone();
                newRow.find('select').val('');
                newRow.find('input[type="number"]').val('');
                newRow.find('.remove-row').show();
                $('#listMaterial').append(newRow);
            });

            $(document).on('click', '.remove-row', function() {
                if ($('.item-row').length > 1) {
                    $(this).closest('.item-row').remove();
                } else {
                    Swal.fire('Info', 'Minimal 1 material harus diisi', 'info');
                }
            });
        });

        // === DROPDOWN SIDEBAR (TARUH DI SINI, DI LUAR $(document).ready) ===
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

        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('toggled');
            localStorage.setItem('sidebarToggled', document.querySelector('.sidebar').classList.contains('toggled') ? 'true' : 'false');
        });

        if (localStorage.getItem('sidebarToggled') === 'true') {
            document.querySelector('.sidebar').classList.add('toggled');
        }

        function setujuiPermintaan(id) {
            Swal.fire({
                title: 'Setujui Permintaan?',
                text: "Permintaan akan disetujui dan siap dikirim!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'proses_permintaan_admin.php?action=setujui&id=' + id;
                }
            });
        }

        function tolakPermintaan(id) {
            Swal.fire({
                title: 'Tolak Permintaan?',
                text: "Permintaan akan ditolak!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'proses_permintaan_admin.php?action=tolak&id=' + id;
                }
            });
        }

        function kirimBarang(id) {
            Swal.fire({
                title: 'Kirim Material?',
                text: "Material akan dikirim ke cabang!",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'proses_permintaan_admin.php?action=kirim&id=' + id;
                }
            });
        }
    </script>
</body>

</html>