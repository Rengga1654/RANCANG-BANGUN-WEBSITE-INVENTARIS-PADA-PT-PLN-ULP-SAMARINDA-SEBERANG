<?php
require 'function.php';
require 'cek.php';

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
    <title>Surat Jalan - Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- CSS Flatpickr: wajib agar ikon panah kalender tidak membesar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="assets/css/animation.css">
    <link rel="stylesheet" href="assets/css/sidebar-fixed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        :root {
            --primary: #0d6efd;
            --sidebar-width: 260px;
            --topbar-height: 56px;
            --page-bg: #f5f7fb;
            --text-main: #111827;
            --text-soft: #6b7280;
            --border: #e5e7eb;
        }

        html,
        body {
            overflow-x: hidden !important;
        }

        body {
            background: var(--page-bg);
            color: var(--text-main);
            font-size: 14px;
        }

        /* TOPBAR */
        .sb-topnav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1039;
            background: #111827 !important;
            height: var(--topbar-height);
            box-shadow: 0 4px 18px rgba(15, 23, 42, .18);
        }

        #sidebarToggle {
            color: #ffffff;
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        #sidebarToggle:hover {
            color: #60a5fa;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 18px rgba(15, 23, 42, .22);
        }

        .sidebar.toggled {
            display: none;
        }

        .sidebar .brand-sidebar {
            height: var(--topbar-height);
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
            transition: all 0.25s ease;
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
            text-decoration: none;
        }

        .sidebar hr {
            margin: 10px 14px;
            border: 0;
            border-top: 1px solid rgba(255, 255, 255, .08);
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

        /* CONTENT */
        .content {
            margin-left: var(--sidebar-width);
            padding: 28px;
            min-height: 100vh;
            margin-top: var(--topbar-height);
            transition: margin-left 0.2s ease;
            overflow-x: hidden;
        }

        body.sidebar-collapsed .content {
            margin-left: 0 !important;
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

        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .card-header {
            font-weight: 700;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .status-badge {
            font-size: 12px;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .status-draft {
            background-color: #ffc107;
            color: #856404;
        }

        .status-dikirim {
            background-color: #007bff;
            color: white;
        }

        .status-diterima {
            background-color: #28a745;
            color: white;
        }

        .btn-print {
            background-color: #6c757d;
            color: white;
        }

        .btn-print:hover {
            background-color: #5a6268;
            color: white;
        }

        /* Perbaikan tabel surat jalan - mengikuti gaya Daftar Permintaan */
        .table-surat {
            width: 100%;
            min-width: 1260px;
            font-size: 13px;
        }

        .table-surat th {
            background: #ffffff;
            color: #111827;
            font-weight: 700;
            vertical-align: middle !important;
            white-space: nowrap;
            padding: 12px 10px;
        }

        .table-surat td {
            vertical-align: top !important;
            padding: 10px;
            line-height: 1.45;
        }

        /* No */
        .table-surat th:nth-child(1),
        .table-surat td:nth-child(1) {
            width: 55px;
            text-align: center;
        }

        /* No. Surat */
        .table-surat th:nth-child(2),
        .table-surat td:nth-child(2) {
            width: 150px;
            white-space: nowrap;
        }

        /* Tanggal Surat */
        .table-surat th:nth-child(3),
        .table-surat td:nth-child(3) {
            width: 130px;
            white-space: nowrap;
        }

        /* Tanggal Kirim */
        .table-surat th:nth-child(4),
        .table-surat td:nth-child(4) {
            width: 130px;
            white-space: nowrap;
        }

        /* Tujuan */
        .table-surat th:nth-child(5),
        .table-surat td:nth-child(5) {
            width: 190px;
            white-space: nowrap;
        }

        /* Material */
        .table-surat th:nth-child(6),
        .table-surat td:nth-child(6) {
            width: 385px;
            max-width: 385px;
        }

        /* Status */
        .table-surat th:nth-child(7),
        .table-surat td:nth-child(7) {
            width: 120px;
            text-align: center;
        }

        /* Aksi */
        .table-surat th:nth-child(8),
        .table-surat td:nth-child(8) {
            width: 100px;
            text-align: center;
        }

        .table-surat .material-ringkas {
            max-width: 365px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-surat .material-ringkas:hover {
            white-space: normal;
            word-break: break-word;
            background-color: #f8f9fc;
            padding: 6px;
            border-radius: 6px;
        }

        .table-surat .status-badge {
            display: inline-block;
            min-width: 70px;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            white-space: nowrap;
            text-align: center;
        }

        .table-surat .btn-print {
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

        .table-surat tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .table-surat tbody tr:hover {
            background-color: #eef6ff;
        }

        /* Pengaman tambahan Flatpickr agar ikon panah tidak membesar */
        .flatpickr-calendar {
            z-index: 99999 !important;
            font-size: 14px !important;
        }

        .flatpickr-calendar:not(.open) {
            display: none !important;
        }

        .flatpickr-prev-month svg,
        .flatpickr-next-month svg {
            width: 14px !important;
            height: 14px !important;
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            width: 34px !important;
            height: 34px !important;
            line-height: 34px !important;
        }
    </style>
</head>

<body>

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <button class="btn btn-link btn-sm" id="sidebarToggle" type="button">
            <i class="fas fa-bars"></i>
        </button>
        <span class="navbar-brand text-white">
            <i class="fas fa-warehouse mr-2 text-primary"></i>SISTEM GUDANG
        </span>
        <div class="ml-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-link text-white dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['email']); ?>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-item-text">
                        <small><strong>Level:</strong> <span class="badge badge-danger">Admin</span></small>
                    </div>
                    <div class="dropdown-item-text">
                        <small><strong>Cabang:</strong> Semua Cabang</small>
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
            <?php $current_page = basename($_SERVER['PHP_SELF']); ?>

            <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>

            <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-warehouse"></i> Stok Gudang
            </a>

            <a href="barang_masuk.php" class="<?= ($current_page == 'barang_masuk.php') ? 'active' : ''; ?>">
                <i class="fas fa-arrow-down"></i> Material Masuk
            </a>

            <a href="barang_keluar.php" class="<?= ($current_page == 'barang_keluar.php') ? 'active' : ''; ?>">
                <i class="fas fa-arrow-up"></i> Material Keluar
            </a>

            <hr>

            <div class="dropdown-btn">
                <i class="fas fa-database"></i> MASTER DATA <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-container">
                <a href="user.php" class="<?= ($current_page == 'user.php') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Daftar Pengguna
                </a>
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
                    $cabang = mysqli_query($conn, "
                        SELECT * FROM tujuan 
                        WHERE nama IN ('Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                        ORDER BY FIELD(nama, 'Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                    ");
                } elseif ($_SESSION['level'] == 'user_cabang') {
                    $cabang = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan = '{$_SESSION['cabang_id']}' ORDER BY id_tujuan ASC");
                } else {
                    $cabang = mysqli_query($conn, "SELECT * FROM tujuan WHERE 1=0");
                }

                while ($c = mysqli_fetch_assoc($cabang)) {
                    $active = (isset($_GET['id']) && $_GET['id'] == $c['id_tujuan'] && basename($_SERVER['PHP_SELF']) == 'stok_cabang.php') ? 'active' : '';
                ?>
                    <a href="stok_cabang.php?id=<?= $c['id_tujuan']; ?>" class="<?= $active; ?>">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($c['nama']); ?>
                    </a>
                <?php } ?>
            </div>

            <hr>

            <a href="daftar_permintaan.php" class="<?= ($current_page == 'daftar_permintaan.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Daftar Permintaan
            </a>

            <a href="surat_jalan.php" class="<?= ($current_page == 'surat_jalan.php') ? 'active' : ''; ?>">
                <i class="fas fa-truck"></i> Surat Jalan
            </a>

            <a href="pengaturan.php" class="<?= ($current_page == 'pengaturan.php') ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Pengaturan
            </a>

            <hr>

            <a href="laporan.php" class="<?= ($current_page == 'laporan.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Laporan
            </a>

            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </div>

    <div class="content">
        <div class="page-title">
            <h2>Surat Jalan</h2>
            <p>Kelola dokumen pengiriman material dari gudang ke cabang.</p>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-list"></i> Daftar Surat Jalan
                <button class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#modalBuatSuratJalan">
                    <i class="fas fa-plus"></i> Buat Surat Jalan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-surat" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Surat</th>
                                <th>Tanggal Surat</th>
                                <th>Tanggal Kirim</th>
                                <th>Tujuan</th>
                                <th>Material</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT sj.*, t.nama as tujuan_nama,
                                    GROUP_CONCAT(CONCAT(m.kode_material, ' - ', m.nama, ' (', sjd.jumlah_kirim, ' ', m.satuan, ')') SEPARATOR '<br>') as detail_material
                                FROM surat_jalan sj
                                JOIN tujuan t ON t.id_tujuan = sj.id_tujuan
                                LEFT JOIN surat_jalan_detail sjd ON sjd.id_surat = sj.id_surat
                                LEFT JOIN material m ON m.id_material = sjd.id_material
                                GROUP BY sj.id_surat
                                ORDER BY 
                                    CASE 
                                        WHEN LOWER(sj.status) = 'draft' THEN 0
                                        ELSE 1
                                    END,
                                    sj.id_surat DESC
                            ");
                            while ($data = mysqli_fetch_assoc($query)) {
                                $status_class = '';
                                $status_text = ucfirst($data['status']);
                                switch ($data['status']) {
                                    case 'draft':
                                        $status_class = 'status-draft';
                                        break;
                                    case 'dikirim':
                                        $status_class = 'status-dikirim';
                                        break;
                                    case 'diterima':
                                        $status_class = 'status-diterima';
                                        break;
                                }
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($data['no_surat']); ?></td>
                                    <td><?= tgl_panjang($data['tanggal_surat']); ?></td>
                                    <td><?= tgl_panjang($data['tanggal_kirim']); ?></td>
                                    <td><?= htmlspecialchars($data['tujuan_nama']); ?></td>
                                    <td>
                                        <div class="material-ringkas" title="<?= htmlspecialchars(strip_tags(str_replace('<br>', ' | ', $data['detail_material'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?= $data['detail_material']; ?>
                                        </div>
                                    </td>
                                    <td><span class="status-badge <?= $status_class; ?>"><?= $status_text; ?></span></td>
                                    <td>
                                        <a href="cetak_surat_jalan.php?id=<?= $data['id_surat']; ?>" target="_blank" class="btn btn-sm btn-print">
                                            <i class="fas fa-print"></i> Cetak
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buat Surat Jalan -->
    <div class="modal fade" id="modalBuatSuratJalan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Buat Surat Jalan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="post" action="buat_surat_jalan.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nomor Surat</label>
                            <input type="text" name="no_surat" class="form-control" placeholder="Otomatis saat disimpan" readonly>
                        </div>
                        <div class="form-group">
                            <label>Pilih Permintaan yang Disetujui</label>
                            <select name="id_permintaan" class="form-control" required>
                                <option value="">Pilih Permintaan</option>
                                <?php
                                $permintaan = mysqli_query($conn, "
                                    SELECT p.*, t.nama as cabang_nama,
                                        GROUP_CONCAT(CONCAT(m.kode_material, ' - ', m.nama, ' (', pd.jumlah, ' ', m.satuan, ')') SEPARATOR ', ') as detail_material
                                    FROM permintaan p
                                    JOIN tujuan t ON t.id_tujuan = p.id_tujuan
                                    LEFT JOIN permintaan_detail pd ON pd.id_permintaan = p.id_permintaan
                                    LEFT JOIN material m ON m.id_material = pd.id_material
                                    LEFT JOIN surat_jalan sj ON sj.id_permintaan = p.id_permintaan
                                    WHERE p.status = 'disetujui'
                                    AND sj.id_surat IS NULL
                                    GROUP BY p.id_permintaan
                                    ORDER BY p.tanggal_permintaan DESC
                                ");
                                while ($p = mysqli_fetch_assoc($permintaan)) {
                                    echo "<option value='{$p['id_permintaan']}'>
                                        {$p['cabang_nama']} - {$p['detail_material']}
                                    </option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Surat</label>
                            <input type="date" name="tanggal_surat" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Kirim</label>
                            <input type="date" name="tanggal_kirim" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pengiriman..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="buat_surat_jalan" class="btn btn-success">Buat Surat Jalan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php tampilkanNotifikasi(); ?>

    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script>
        // === FLATPICKR ===
        if (typeof flatpickr !== 'undefined') {
            flatpickr("input[name='tanggal_surat'], input[name='tanggal_kirim']", {
                dateFormat: "Y-m-d",
                altFormat: "d-m-Y",
                locale: "id",
                altInput: true,
                allowInput: true,
                disableMobile: true
            });
        }

        // === DATATABLE ===
        $(document).ready(function() {
            $('#dataTable').DataTable({
                pageLength: 10,
                language: {
                    emptyTable: "Tidak ada data pada tabel",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ baris",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 baris",
                    infoFiltered: "(difilter dari _MAX_ total baris)",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    loadingRecords: "Memuat...",
                    processing: "Sedang memproses...",
                    search: "Cari:",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    aria: {
                        sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                        sortDescending: ": aktifkan untuk mengurutkan kolom turun"
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

        // === DROPDOWN SIDEBAR ===
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

        // === TOGGLE SIDEBAR: SIDEBAR TERTUTUP DAN CONTENT IKUT MELEBAR ===
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');

        if (sidebarToggle && sidebar) {
            function setSidebarState(isCollapsed) {
                sidebar.classList.toggle('toggled', isCollapsed);
                document.body.classList.toggle('sidebar-collapsed', isCollapsed);
                localStorage.setItem('sidebarToggled', isCollapsed ? 'true' : 'false');
            }

            const savedState = localStorage.getItem('sidebarToggled') === 'true';
            setSidebarState(savedState);

            sidebarToggle.addEventListener('click', function() {
                const isCollapsed = sidebar.classList.contains('toggled');
                setSidebarState(!isCollapsed);
            });
        }
    </script>
</body>

</html>