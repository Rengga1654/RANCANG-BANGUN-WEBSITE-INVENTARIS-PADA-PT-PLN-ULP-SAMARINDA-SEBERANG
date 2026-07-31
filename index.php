<?php
require 'function.php';
require 'cek.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Stok Gudang - Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="assets/css/animation.css">
    <link rel="stylesheet" href="assets/css/sidebar-fixed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
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

        /* ========== TAMPILAN STOK GUDANG ASLI TETAP DIPERTAHANKAN ========== */
        .stok-badge {
            font-size: 14px;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
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

        .table-stok {
            width: 100%;
        }

        .table-stok td:nth-child(3) {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-stok td:nth-child(3):hover {
            white-space: normal;
            word-break: break-word;
            background-color: #f8f9fc;
        }

        /* ========== PERAPIAN TABEL STOK GUDANG / STOK CABANG ========== */
        .table-responsive {
            overflow-x: auto;
        }

        .table-stok {
            min-width: 1180px;
            font-size: 13px;
        }

        .table-stok thead th {
            background: #ffffff;
            color: #111827;
            font-weight: 700;
            vertical-align: middle !important;
            white-space: nowrap;
            padding: 13px 28px 13px 12px !important;
            border-bottom: 2px solid #dee2e6 !important;
        }

        .table-stok tbody td {
            vertical-align: middle !important;
            padding: 12px !important;
            line-height: 1.45;
        }

        .table-stok th:nth-child(1),
        .table-stok td:nth-child(1) {
            width: 60px;
            text-align: center;
        }

        .table-stok th:nth-child(2),
        .table-stok td:nth-child(2) {
            width: 135px;
            white-space: nowrap;
        }

        .table-stok th:nth-child(3),
        .table-stok td:nth-child(3) {
            width: 410px;
            max-width: 410px;
        }

        .table-stok th:nth-child(4),
        .table-stok td:nth-child(4) {
            width: 95px;
            white-space: nowrap;
        }

        .table-stok th:nth-child(5),
        .table-stok td:nth-child(5) {
            width: 135px;
            text-align: center;
            white-space: nowrap;
        }

        .table-stok th:nth-child(6),
        .table-stok td:nth-child(6) {
            width: 125px;
            text-align: center;
            white-space: nowrap;
        }

        .table-stok th:nth-child(7),
        .table-stok td:nth-child(7) {
            width: 230px;
            text-align: center;
            white-space: nowrap;
        }

        .table-stok .stok-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-width: 85px;
            padding: 7px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        .table-stok .btn {
            margin: 2px;
            padding: 7px 10px;
            font-size: 13px;
            border-radius: 5px;
            line-height: 1.3;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .table-stok tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .table-stok tbody tr:hover {
            background-color: #eef6ff;
        }

        table.dataTable thead>tr>th.sorting::before,
        table.dataTable thead>tr>th.sorting::after,
        table.dataTable thead>tr>th.sorting_asc::before,
        table.dataTable thead>tr>th.sorting_asc::after,
        table.dataTable thead>tr>th.sorting_desc::before,
        table.dataTable thead>tr>th.sorting_desc::after {
            top: 50% !important;
            transform: translateY(-50%);
        }

        @media (max-width: 991px) {
            .content {
                margin-left: 0;
                padding: 18px;
            }

            .sidebar:not(.toggled) {
                width: 260px;
            }
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
                    <div class="dropdown-item-text"><small><strong>Level:</strong>
                            <?php if ($_SESSION['level'] == 'admin'): ?>
                                <span class="badge badge-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-info">User Cabang</span>
                            <?php endif; ?>
                        </small></div>
                    <div class="dropdown-item-text"><small><strong>Cabang:</strong>
                            <?php
                            if ($_SESSION['level'] == 'user_cabang' && $_SESSION['cabang_id']) {
                                $cabang_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM tujuan WHERE id_tujuan = '{$_SESSION['cabang_id']}'"));
                                echo htmlspecialchars($cabang_user['nama'] ?? '-');
                            } else {
                                echo "Semua Cabang";
                            }
                            ?>
                        </small></div>
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

            <a href="index.php" class="active"><i class="fas fa-warehouse"></i> Stok Gudang</a>

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
                    $cabang = mysqli_query($conn, "SELECT * FROM tujuan WHERE id_tujuan = '{$_SESSION['cabang_id']}' ORDER BY id_tujuan ASC");
                } else {
                    $cabang = mysqli_query($conn, "
                    SELECT * FROM tujuan 
                    WHERE nama IN ('Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                    ORDER BY FIELD(nama, 'Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                ");
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

            <?php if ($_SESSION['level'] == 'user_cabang'): ?>
                <a href="permintaan_cabang.php"><i class="fas fa-file-alt"></i> Permintaan Material</a>
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
            <h2>Stok Gudang</h2>
            <p>Daftar stok material yang tersedia di gudang.</p>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-boxes"></i> Daftar Stok Material di Gudang
                <div class="float-right">
                    <?php if ($_SESSION['level'] == 'admin'): ?>
                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#modalTambahStok">
                            <i class="fas fa-plus-circle"></i> Tambah Stok
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-stok" id="dataTable" width="100%">
                        <thead class="bg-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Satuan</th>
                                <th>Stok Saat Ini</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "SELECT m.*, COALESCE(sg.jumlah, 0) as stok FROM material m LEFT JOIN stock_gudang sg ON sg.id_material = m.id_material ORDER BY m.id_material ASC");
                            while ($data = mysqli_fetch_assoc($query)) {
                                $stok = $data['stok'];
                                if ($stok <= 0) $status = '<span class="stok-badge stok-habis"><i class="fas fa-times-circle"></i> Habis</span>';
                                elseif ($stok <= 10) $status = '<span class="stok-badge stok-menipis"><i class="fas fa-exclamation-triangle"></i> Menipis</span>';
                                else $status = '<span class="stok-badge stok-aman"><i class="fas fa-check-circle"></i> Aman</span>';
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($data['kode_material']); ?></td>
                                    <td><?= htmlspecialchars($data['nama']); ?></td>
                                    <td><?= htmlspecialchars($data['satuan']); ?></td>
                                    <td><strong><?= number_format($stok, 0, ',', '.'); ?></strong></td>
                                    <td><?= $status; ?></td>
                                    <td>
                                        <?php if ($_SESSION['level'] == 'admin'): ?>
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEditStok<?= $data['id_material']; ?>"><i class="fas fa-edit"></i> Edit Stok</button>
                                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTambahStokMaterial<?= $data['id_material']; ?>"><i class="fas fa-plus-circle"></i> Tambah Stok</button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-lock"></i> Terkunci</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <!-- Modal Edit Stok -->
                                <div class="modal fade" id="modalEditStok<?= $data['id_material']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit Stok - <?= htmlspecialchars($data['nama']); ?></h5><button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="proses_edit_stok.php">
                                                <div class="modal-body"><input type="hidden" name="id_material" value="<?= $data['id_material']; ?>">
                                                    <div class="form-group"><label>Kode Material</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['kode_material']); ?>" readonly></div>
                                                    <div class="form-group"><label>Nama Material</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" readonly></div>
                                                    <div class="form-group"><label>Satuan</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['satuan']); ?>" readonly></div>
                                                    <div class="form-group"><label>Jumlah Stok</label><input type="number" name="jumlah" class="form-control" value="<?= $stok; ?>" required></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" name="edit_stok" class="btn btn-warning">Simpan</button></div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal Tambah Stok -->
                                <div class="modal fade" id="modalTambahStokMaterial<?= $data['id_material']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">Tambah Stok - <?= htmlspecialchars($data['nama']); ?></h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="proses_tambah_stok_material.php">
                                                <div class="modal-body"><input type="hidden" name="id_material" value="<?= $data['id_material']; ?>">
                                                    <div class="form-group"><label>Kode Material</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['kode_material']); ?>" readonly></div>
                                                    <div class="form-group"><label>Nama Material</label><input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" readonly></div>
                                                    <div class="form-group"><label>Stok Saat Ini</label><input type="text" class="form-control" value="<?= number_format($stok, 0, ',', '.'); ?> <?= htmlspecialchars($data['satuan']); ?>" readonly></div>
                                                    <div class="form-group"><label>Tambah Stok</label><input type="number" name="tambah_stok" class="form-control" placeholder="Jumlah stok" required min="1"></div>
                                                </div>
                                                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" name="tambah_stok_material" class="btn btn-info">Tambah Stok</button></div>
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

    <!-- Modal Tambah Stok Umum -->
    <div class="modal fade" id="modalTambahStok">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Stok Gudang</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="post" action="proses_tambah_stok.php">
                    <div class="modal-body">
                        <div class="form-group"><label>Material</label><select name="id_material" class="form-control" required>
                                <option value="">Pilih Material</option>
                                <?php $material = mysqli_query($conn, "SELECT * FROM material ORDER BY id_material ASC");
                                while ($m = mysqli_fetch_assoc($material)) {
                                    echo "<option value='{$m['id_material']}'>{$m['kode_material']} - {$m['nama']}</option>";
                                } ?>
                            </select></div>
                        <div class="form-group"><label>Jumlah Stok yang Ditambahkan</label><input type="number" name="jumlah" class="form-control" placeholder="Jumlah stok" required min="1"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button><button type="submit" name="tambah_stok" class="btn btn-info"><i class="fas fa-save"></i> Tambah Stok</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script>
        // ========== CUSTOM SORTING UNTUK STATUS (Aman, Menipis, Habis) ==========
        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "status-order-pre": function(a) {
                if (a.indexOf('Aman') !== -1) return 1;
                if (a.indexOf('Menipis') !== -1) return 2;
                if (a.indexOf('Habis') !== -1) return 3;
                return 4;
            },
            "status-order-asc": function(a, b) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },
            "status-order-desc": function(a, b) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            }
        });

        $('#dataTable').DataTable({
            "columnDefs": [{
                "targets": 5, // kolom Status (index ke-5, mulai dari 0)
                "type": "status-order",
                "orderSequence": ["desc", "asc"]
            }],
            "language": {
                "sLengthMenu": "Tampilkan _MENU_ baris",
                "sZeroRecords": "Tidak ada data yang tersedia",
                "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ baris",
                "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 baris",
                "sInfoFiltered": "(disaring dari _MAX_ total baris)",
                "sSearch": "Cari:",
                "oPaginate": {
                    "sFirst": "Pertama",
                    "sLast": "Terakhir",
                    "sNext": "Selanjutnya",
                    "sPrevious": "Sebelumnya"
                }
            }
        });

        // ========== DROPDOWN DENGAN STATE TERSIMPAN (localStorage) ==========
        var dropdown = document.getElementsByClassName("dropdown-btn");

        // Fungsi untuk menyimpan state dropdown
        function saveDropdownState(index, isOpen) {
            localStorage.setItem('dropdownState_' + index, isOpen ? 'open' : 'closed');
        }

        // Fungsi untuk memuat state dropdown
        function loadDropdownState(index) {
            return localStorage.getItem('dropdownState_' + index) === 'open';
        }

        for (var i = 0; i < dropdown.length; i++) {
            var btnIndex = i;
            var dropdownContent = dropdown[i].nextElementSibling;

            // Load state yang tersimpan
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


        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('toggled');
            localStorage.setItem('sidebarToggled', document.querySelector('.sidebar').classList.contains('toggled') ? 'true' : 'false');
        });

        if (localStorage.getItem('sidebarToggled') === 'true') {
            document.querySelector('.sidebar').classList.add('toggled');
        }
    </script>
</body>

</html>