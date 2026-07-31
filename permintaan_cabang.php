<?php
require 'function.php';
require 'cek.php';

$id_tujuan = isset($_GET['id']) ? $_GET['id'] : 0;

// Jika user cabang, ambil cabang_id dari session
if ($_SESSION['level'] == 'user_cabang') {
    $id_tujuan = $_SESSION['cabang_id'];
}

// Ambil data cabang
$query_cabang = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan='$id_tujuan'");
$cabang = mysqli_fetch_assoc($query_cabang);

if (!$cabang) {
    echo "<script>alert('Cabang tidak ditemukan!'); window.location.href='index.php';</script>";
    exit();
}

// Generate nomor permintaan otomatis
$tahun = date('Y');
$bulan = date('m');
$query_no = mysqli_query($conn, "SELECT COUNT(*) as total FROM permintaan WHERE YEAR(created_at) = '$tahun' AND MONTH(created_at) = '$bulan'");
$count = mysqli_fetch_assoc($query_no);
$no_permintaan = "REQ/" . $tahun . $bulan . "/" . str_pad(($count['total'] + 1), 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Permintaan Material - <?= $cabang['nama']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/animation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <style>
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

        /* ========== SIDEBAR SAMA SEPERTI DASHBOARD ========== */
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
            transition: margin-left 0.3s ease;
        }

        body.sidebar-collapsed .content {
            margin-left: 0 !important;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.toggled {
                transform: translateX(0);
            }

            .content {
                margin-left: 0 !important;
                padding: 18px;
            }

            body.sidebar-collapsed .content {
                margin-left: 0 !important;
            }
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
        }

        .page-title p {
            color: var(--text-soft);
            margin-bottom: 18px;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .card-header {
            font-weight: 700;
            padding: 13px 18px;
        }

        .form-control {
            border-radius: 9px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
        }

        /* Status sederhana seperti badge biasa */
        /* Status sederhana seperti badge biasa */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
            text-align: center;
            margin: 0 auto;
        }

        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .status-disetujui {
            background-color: #17a2b8;
            color: white;
        }

        .status-dikirim {
            background-color: #007bff;
            color: white;
        }

        .status-selesai {
            background-color: #28a745;
            color: white;
        }

        .status-ditolak {
            background-color: #dc3545;
            color: white;
        }

        .status-perlu_perbaikan {
            background-color: #ffc107;
            color: #212529;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table th {
            background-color: #f8fafc;
            color: #111827;
            font-weight: 700;
            vertical-align: middle !important;
        }

        .table td {
            vertical-align: middle !important;
        }

        .detail-verifikasi {
            font-size: 11px;
            background-color: #f8f9fc;
            padding: 5px;
            border-radius: 5px;
        }

        .remove-row {
            cursor: pointer;
            color: red;
        }

        .remove-row:hover {
            color: darkred;
        }

        .item-row select,
        .item-row input {
            width: 100%;
        }

        /* Tengah khusus kolom Status dan Aksi */
        #dataTable tbody td:nth-child(5),
        #dataTable tbody td:nth-child(8) {
            text-align: center !important;
            vertical-align: middle !important;
        }

        /* Kotak status biar pas di tengah */
        #dataTable tbody td:nth-child(5) .status-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 28px !important;
            padding: 0 10px !important;
            margin: 0 auto !important;
        }

        /* Tombol Verifikasi biar pas di tengah */
        #dataTable tbody td:nth-child(8) .btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 34px !important;
            padding: 0 12px !important;
            margin: 0 auto !important;
            line-height: 1 !important;
        }

        /* Jarak ikon dan tulisan tombol */
        #dataTable tbody td:nth-child(8) .btn i {
            margin-right: 5px;
        }

        .item-row select,
        .item-row input {
            width: 100%;
        }
    </style>
</head>

<body>
    <?php tampilkanNotifikasi(); ?>

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <button class="btn btn-link btn-sm" id="sidebarToggle"><i class="fas fa-bars"></i></button>
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
                    <div class="dropdown-item-text">
                        <small><strong>Cabang:</strong>
                            <?php
                            if ($_SESSION['level'] == 'user_cabang' && $_SESSION['cabang_id']) {
                                $cabang_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM tujuan WHERE id_tujuan = '{$_SESSION['cabang_id']}'"));
                                echo htmlspecialchars($cabang_user['nama'] ?? '-');
                            } else {
                                echo "Semua Cabang";
                            }
                            ?>
                        </small>
                    </div>
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
            <?php if ($_SESSION['level'] == 'admin'): ?>
                <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
            <?php endif; ?>

            <a href="index.php"><i class="fas fa-warehouse"></i> Stok Gudang</a>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <a href="barang_masuk.php"><i class="fas fa-arrow-down"></i> Material Masuk</a>
                <a href="barang_keluar.php"><i class="fas fa-arrow-up"></i> Material Keluar</a>
            <?php endif; ?>

            <hr>

            <div class="dropdown-btn">
                <i class="fas fa-database"></i> MASTER DATA <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-container">
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <a href="user.php"><i class="fas fa-users"></i> Daftar Pengguna</a>
                <?php endif; ?>
                <a href="material.php"><i class="fas fa-boxes"></i> Daftar Material</a>
                <a href="tujuan.php"><i class="fas fa-map-marker-alt"></i> Daftar Tujuan</a>
            </div>

            <hr>

            <div class="dropdown-btn">
                <i class="fas fa-building"></i> STOK CABANG <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-container">
                <?php
                if ($_SESSION['level'] == 'user_cabang') {
                    $cabang_list = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan = '{$_SESSION['cabang_id']}' ORDER BY id_tujuan ASC");
                } else {
                    $cabang_list = mysqli_query($conn, "SELECT * FROM tujuan ORDER BY id_tujuan ASC");
                }

                while ($c = mysqli_fetch_assoc($cabang_list)) {
                    $active = ($c['id_tujuan'] == $id_tujuan) ? 'active' : '';
                ?>
                    <a href="stok_cabang.php?id=<?= $c['id_tujuan']; ?>" class="<?= $active; ?>">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($c['nama']); ?>
                    </a>
                <?php } ?>
            </div>

            <hr>

            <?php if ($_SESSION['level'] == 'user_cabang'): ?>
                <a href="permintaan_cabang.php?id=<?= $id_tujuan; ?>" class="active"><i class="fas fa-file-alt"></i> Permintaan Material</a>
            <?php endif; ?>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <a href="daftar_permintaan.php"><i class="fas fa-file-alt"></i> Daftar Permintaan</a>
                <a href="surat_jalan.php"><i class="fas fa-truck"></i> Surat Jalan</a>
                <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
                <hr>
                <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
            <?php endif; ?>

            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </div>

    <div class="content">
        <div class="page-title">
            <h2>Permintaan Material</h2>
            <p>Halaman untuk membuat dan memantau permintaan material cabang <?= htmlspecialchars($cabang['nama']); ?>.</p>
        </div>

        <!-- Form Permintaan Material (Multi Material) -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white"><i class="fas fa-plus"></i> Buat Permintaan Material</div>
            <div class="card-body">
                <form id="formPermintaanMaterial" method="post" action="proses_permintaan.php" novalidate>
                    <input type="hidden" name="id_tujuan" value="<?= $id_tujuan; ?>">
                    <input type="hidden" name="no_permintaan" value="<?= $no_permintaan; ?>">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nomor Permintaan</label>
                                <input type="text" class="form-control" value="<?= $no_permintaan; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tanggal Permintaan</label>
                                <input type="text" name="tanggal_permintaan" class="form-control" placeholder="Hari/Bulan/Tahun" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control" rows="1" placeholder="Catatan tambahan untuk admin..."></textarea>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-3">Daftar Material</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabelMaterial">
                            <thead class="bg-light">
                                <tr>
                                    <th>Material</th>
                                    <th width="20%">Jumlah</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="listMaterial">
                                <tr class="item-row">
                                    <td>
                                        <select name="id_material[]" class="form-control" required>
                                            <option value=""> Pilih Material </option>
                                            <?php
                                            $material = mysqli_query($conn, "
                                                SELECT m.*, COALESCE(sg.jumlah, 0) as stok
                                                FROM material m
                                                LEFT JOIN stock_gudang sg ON sg.id_material = m.id_material
                                                WHERE COALESCE(sg.jumlah, 0) > 0
                                                ORDER BY m.id_material ASC
                                            ");
                                            while ($m = mysqli_fetch_assoc($material)) {
                                                echo "<option value='{$m['id_material']}' data-stok='{$m['stok']}'>{$m['kode_material']} - {$m['nama']} (Stok tersedia: {$m['stok']} {$m['satuan']})</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control" placeholder="Jumlah" min="1" required>
                                    </td>
                                    <td class="text-center">
                                        <i class="fas fa-trash-alt text-danger remove-row" style="cursor:pointer"></i>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        <button type="button" class="btn btn-sm btn-primary" id="tambahBaris">
                                            <i class="fas fa-plus"></i> Tambah Material
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" name="buat_permintaan" class="btn btn-primary">Kirim Permintaan</button>
                            <a href="permintaan_cabang.php?id=<?= $id_tujuan; ?>" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Riwayat Permintaan -->
        <div class="card">
            <div class="card-header bg-info text-white"><i class="fas fa-history"></i> Riwayat Permintaan Material</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Permintaan</th>
                                <th>Tanggal</th>
                                <th>Material</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Detail Verifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT p.*, 
                                    COALESCE(SUM(pd.jumlah), 0) as total_jumlah,
                                    GROUP_CONCAT(CONCAT(m.kode_material, ' - ', m.nama, ' (', pd.jumlah, ' ', m.satuan, ')') SEPARATOR '<br>') as detail_material
                                FROM permintaan p
                                LEFT JOIN permintaan_detail pd ON pd.id_permintaan = p.id_permintaan
                                LEFT JOIN material m ON m.id_material = pd.id_material
                                WHERE p.id_tujuan = '$id_tujuan'
                                GROUP BY p.id_permintaan
                                ORDER BY p.id_permintaan DESC
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
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $data['no_permintaan']; ?></td>
                                    <td><?= tgl_panjang($data['tanggal_permintaan']); ?></td>
                                    <td><?= $data['detail_material']; ?></td>
                                    <td><span class="status-badge <?= $status_class; ?>"><?= $status_text; ?></span></td>
                                    <td><?= nl2br(htmlspecialchars($data['catatan'] ?? '-')); ?></td>
                                    <td>
                                        <?php
                                        $query_verif = mysqli_query($conn, "SELECT * FROM verifikasi_penerimaan WHERE id_permintaan = '{$data['id_permintaan']}' ORDER BY id_verifikasi DESC LIMIT 1");
                                        $verif = mysqli_fetch_assoc($query_verif);
                                        if ($verif) {
                                            echo '<div class="detail-verifikasi">';
                                            echo '<small><strong>Status:</strong> <span class="badge badge-secondary">' . ucwords(str_replace('_', ' ', $verif['status_verifikasi'])) . '</span></small><br>';
                                            echo '<small><strong>Diterima:</strong> ' . $verif['jumlah_diterima'] . '</small><br>';
                                            echo '<small><strong>Keterangan:</strong> ' . ($verif['keterangan'] ?: '-') . '</small>';
                                            echo '</div>';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($data['status'] == 'dikirim'): ?>
                                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalVerifikasi<?= $data['id_permintaan']; ?>">
                                                <i class="fas fa-check-circle"></i> Verifikasi
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($data['status'] == 'pending' && $_SESSION['level'] == 'user_cabang'): ?>
                                            <button class="btn btn-sm btn-danger" onclick="hapusPermintaan(<?= $data['id_permintaan']; ?>)">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($data['status'] == 'perlu_perbaikan'): ?>
                                            <span class="badge badge-warning">Buat Permintaan Baru</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Modal Verifikasi -->
                                <div class="modal fade" id="modalVerifikasi<?= $data['id_permintaan']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">Verifikasi Penerimaan Material</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="proses_verifikasi.php">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_permintaan" value="<?= $data['id_permintaan']; ?>">
                                                    <input type="hidden" name="id_tujuan" value="<?= $id_tujuan; ?>">
                                                    <input type="hidden" name="jumlah_diminta" value="<?= (int)$data['total_jumlah']; ?>">

                                                    <div class="form-group">
                                                        <label>Material yang Dipesan</label>
                                                        <div class="form-control" style="height: auto;"><?= $data['detail_material']; ?></div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Jumlah yang Diterima</label>
                                                        <input type="number"
                                                            name="jumlah_diterima"
                                                            class="form-control jumlah-diterima"
                                                            placeholder="Jumlah yang diterima"
                                                            min="0"
                                                            required
                                                            data-jumlah-diminta="<?= (int)$data['total_jumlah']; ?>"
                                                            data-status-target="#statusVerifikasi<?= $data['id_permintaan']; ?>">

                                                        <small class="text-muted">
                                                            Jumlah yang diminta: <?= (int)$data['total_jumlah']; ?>
                                                        </small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Tanggal Terima</label>
                                                        <input type="text" name="tanggal_terima" class="form-control" placeholder="DD/MM/YYYY" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Status Verifikasi</label>
                                                        <select name="status_verifikasi"
                                                            id="statusVerifikasi<?= $data['id_permintaan']; ?>"
                                                            class="form-control status-verifikasi"
                                                            required
                                                            tabindex="-1"
                                                            style="pointer-events: none;">
                                                            <option value="sesuai">Sesuai</option>
                                                            <option value="kurang">Kurang</option>
                                                            <option value="lebih">Lebih</option>
                                                        </select>

                                                        <small class="text-muted">
                                                            Status otomatis menyesuaikan jumlah yang diterima.
                                                        </small>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Keterangan</label>
                                                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan verifikasi..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="verifikasi" class="btn btn-success">Verifikasi</button>
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
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

    <script>
        flatpickr("input[name='tanggal_permintaan'], input[name='tanggal_terima']", {
            dateFormat: "Y-m-d",
            altFormat: "d-m-Y",
            locale: "id",
            altInput: true,
            allowInput: true,
            disableMobile: true
        });

        // Validasi permintaan material dengan notif Bahasa Indonesia
        function tampilkanNotifPermintaan(pesan, elemen) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Periksa Permintaan',
                    text: pesan,
                    confirmButtonText: 'Oke'
                }).then(function() {
                    if (elemen) {
                        if (elemen._flatpickr) {
                            elemen._flatpickr.open();
                        } else {
                            elemen.focus();
                        }
                    }
                });
            } else {
                alert(pesan);
                if (elemen) {
                    elemen.focus();
                }
            }
        }

        function pesanValidasiJumlah(input) {
            var nilai = (input.value || '').trim();
            var jumlah = parseInt(nilai, 10);
            var max = parseInt(input.getAttribute('max'), 10);

            if (nilai === '') {
                return 'Jumlah permintaan wajib diisi.';
            }

            if (isNaN(jumlah) || jumlah < 1) {
                return 'Jumlah permintaan minimal 1.';
            }

            if (!isNaN(max) && jumlah > max) {
                return 'Jumlah permintaan tidak boleh melebihi stok tersedia. Stok tersedia hanya ' + max + '.';
            }

            return '';
        }

        // Batasi jumlah maksimal berdasarkan stok
        $(document).on('change', 'select[name="id_material[]"]', function() {
            var stok = parseInt($(this).find(':selected').data('stok'), 10);
            var jumlahInput = $(this).closest('tr').find('input[name="jumlah[]"]');

            if (!isNaN(stok) && stok > 0) {
                jumlahInput.attr('max', stok);
                jumlahInput.attr('placeholder', 'Maksimal ' + stok);
            } else {
                jumlahInput.removeAttr('max');
                jumlahInput.attr('placeholder', 'Jumlah');
            }
        });

        $(document).on('input', 'input[name="jumlah[]"]', function() {
            this.setCustomValidity('');
        });

        var formPermintaanMaterial = document.getElementById('formPermintaanMaterial');
        if (formPermintaanMaterial) {
            formPermintaanMaterial.addEventListener('submit', function(e) {
                var pesan = '';
                var elemenFokus = null;
                var tanggalInput = formPermintaanMaterial.querySelector('input[name="tanggal_permintaan"]');

                if (!tanggalInput || tanggalInput.value.trim() === '') {
                    pesan = 'Tanggal permintaan wajib diisi.';
                    elemenFokus = tanggalInput;
                }

                var rows = formPermintaanMaterial.querySelectorAll('#listMaterial .item-row');
                rows.forEach(function(row, index) {
                    if (pesan !== '') {
                        return;
                    }

                    var selectMaterial = row.querySelector('select[name="id_material[]"]');
                    var jumlahInput = row.querySelector('input[name="jumlah[]"]');
                    var nomorBaris = index + 1;

                    if (!selectMaterial || selectMaterial.value === '') {
                        pesan = 'Material pada baris ke-' + nomorBaris + ' wajib dipilih.';
                        elemenFokus = selectMaterial;
                        return;
                    }

                    if (jumlahInput) {
                        pesan = pesanValidasiJumlah(jumlahInput);
                        if (pesan !== '') {
                            elemenFokus = jumlahInput;
                        }
                    }
                });

                if (pesan !== '') {
                    e.preventDefault();
                    tampilkanNotifPermintaan(pesan, elemenFokus);
                }
            });
        }

        $(document).ready(function() {
            $('#dataTable').DataTable({
                pageLength: 10,
                language: {
                    lengthMenu: "Tampilkan _MENU_ baris",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ baris",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 baris",
                    infoFiltered: "(difilter dari _MAX_ total baris)",
                    search: "Cari:",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Selanjutnya"
                    }
                }
            });

            // Tambah baris material
            $('#tambahBaris').click(function() {
                var newRow = $('.item-row:first').clone();
                newRow.find('select').val('');
                newRow.find('input[type="number"]').val('').removeAttr('max').attr('placeholder', 'Jumlah').each(function() {
                    this.setCustomValidity('');
                });
                newRow.find('.remove-row').show();
                $('#listMaterial').append(newRow);
            });

            // Hapus baris
            $(document).on('click', '.remove-row', function() {
                if ($('.item-row').length > 1) {
                    $(this).closest('.item-row').remove();
                } else {
                    Swal.fire('Info', 'Minimal 1 material harus diisi', 'info');
                }
            });
        });

        // Status verifikasi otomatis mengikuti jumlah diterima
        function aturStatusVerifikasi(input) {
            var jumlahDiminta = parseInt($(input).data('jumlah-diminta'));
            var jumlahDiterima = parseInt($(input).val());
            var targetStatus = $(input).data('status-target');
            var statusSelect = $(targetStatus);

            if (!statusSelect.length) {
                return;
            }

            if (isNaN(jumlahDiterima)) {
                statusSelect.val('sesuai');
                return;
            }

            if (jumlahDiterima < jumlahDiminta) {
                statusSelect.val('kurang');
            } else if (jumlahDiterima > jumlahDiminta) {
                statusSelect.val('lebih');
            } else {
                statusSelect.val('sesuai');
            }
        }

        $(document).on('input', '.jumlah-diterima', function() {
            aturStatusVerifikasi(this);
        });

        function hapusPermintaan(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Permintaan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_permintaan.php?id=' + id;
                }
            });
        }

        // ========== DROPDOWN SIDEBAR (SAMA SEPERTI FILE LAIN) ==========
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