<?php
require 'function.php';
require 'cek.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Material - Sistem Gudang</title>
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

        .sidebar {
            transition: transform 0.3s ease;
        }

        .sidebar.toggled {
            transform: translateX(-100%);
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

        .card {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .card-header {
            font-weight: 700;
        }

        .btn {
            border-radius: 8px;
        }

        /* TABEL STYLING */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            padding: 12px 8px;
            font-weight: 600;
        }

        .table tbody td {
            padding: 8px;
            vertical-align: middle;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px;
        }

        /* KHUSUS TABEL MATERIAL - Untuk nama panjang */
        .table-material {
            width: 100%;
        }

        /* Atur lebar kolom */
        .table-material th:nth-child(1) {
            width: 5%;
        }

        /* No */
        .table-material th:nth-child(2) {
            width: 12%;
        }

        /* Kode Material */
        .table-material th:nth-child(3) {
            width: 45%;
        }

        /* Nama Material */
        .table-material th:nth-child(4) {
            width: 8%;
        }

        /* Satuan */
        .table-material th:nth-child(5) {
            width: 15%;
        }

        /* Harga */
        .table-material th:nth-child(6) {
            width: 15%;
        }

        /* Aksi */

        /* Potong teks panjang di kolom Nama Material */
        .table-material td:nth-child(3) {
            max-width: 350px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Munculkan teks penuh saat hover */
        .table-material td:nth-child(3):hover {
            white-space: normal;
            word-break: break-word;
            background-color: #f8f9fc;
            position: relative;
            z-index: 1;
        }

        /* Rata kanan untuk kolom harga */
        .table-material td:nth-child(5) {
            text-align: right;
        }

        /* Rata tengah untuk kolom aksi */
        .table-material td:nth-child(6) {
            text-align: center;
        }

        /* Responsive untuk layar kecil */
        @media (max-width: 768px) {

            .table-material th:nth-child(3),
            .table-material td:nth-child(3) {
                max-width: 200px;
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
                            <?php if ($_SESSION['level'] == 'admin'): ?><span class="badge badge-danger">Admin</span>
                            <?php else: ?><span class="badge badge-info">User Cabang</span><?php endif; ?>
                        </small></div>
                    <div class="dropdown-item-text"><small><strong>Cabang:</strong>
                            <?php
                            if ($_SESSION['level'] == 'user_cabang' && $_SESSION['cabang_id']) {
                                $cabang_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM tujuan WHERE id_tujuan = '{$_SESSION['cabang_id']}'"));
                                echo htmlspecialchars($cabang_user['nama']);
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
                <a href="material.php" class="active"><i class="fas fa-boxes"></i> Daftar Material</a>
                <a href="tujuan.php"><i class="fas fa-map-marker-alt"></i> Daftar Tujuan</a>
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
            <h2>Daftar Material</h2>
            <p>Kelola data material yang digunakan pada sistem gudang.</p>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-boxes"></i> Master Data Material
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <button class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#modalTambahMaterial">
                        <i class="fas fa-plus"></i> Tambah Material
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-material" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Satuan</th>
                                <th>Harga (Rp)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "SELECT * FROM material ORDER BY id_material ASC");
                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($data['kode_material']); ?></td>
                                    <td><?= htmlspecialchars($data['nama']); ?></td>
                                    <td><?= htmlspecialchars($data['satuan']); ?></td>
                                    <td class="text-right">
                                        <?php
                                        if ($data['harga'] > 0) {
                                            echo number_format($data['harga'], 0, ',', '.');
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($_SESSION['level'] == 'admin'): ?>
                                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit<?= $data['id_material']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="hapusMaterial(<?= $data['id_material']; ?>, '<?= htmlspecialchars($data['nama']); ?>')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Terkunci
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Modal Edit Material -->
                                <div class="modal fade" id="modalEdit<?= $data['id_material']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit Material</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="proses_edit_material.php">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_material" value="<?= $data['id_material']; ?>">
                                                    <div class="form-group">
                                                        <label>Kode Material</label>
                                                        <input type="text" name="kode_material" class="form-control" value="<?= htmlspecialchars($data['kode_material']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Nama Material</label>
                                                        <input type="text" name="nama_material" class="form-control" value="<?= htmlspecialchars($data['nama']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Satuan</label>
                                                        <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($data['satuan']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Harga (Rp)</label>
                                                        <input type="text" name="harga" class="form-control" value="<?= number_format($data['harga'], 0, ',', '.'); ?>" onkeyup="formatRibuan(this)">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit_material" class="btn btn-warning">Simpan</button>
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

    <!-- Modal Tambah Material -->
    <div class="modal fade" id="modalTambahMaterial">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Material Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="post" action="proses_tambah_material.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Kode Material</label>
                            <input type="text" name="kode_material" class="form-control" placeholder="Contoh: BRG-001" required>
                            <small class="text-muted">Kode harus unik</small>
                        </div>
                        <div class="form-group">
                            <label>Nama Material</label>
                            <input type="text" name="nama_material" class="form-control" placeholder="Contoh: Material Small Angle / Suspension Assembly ( 50 - 70 sqmm ) ; Non Steinles Steel Strip + Yorke" required>
                        </div>
                        <div class="form-group">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="Contoh: Bh, Roll (100m)" required>
                        </div>
                        <div class="form-group">
                            <label>Harga (Rp)</label>
                            <input type="text" name="harga" class="form-control" placeholder="Contoh: 3.000.000" onkeyup="formatRibuan(this)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_material" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    <script>
        function formatRibuan(input) {
            let value = input.value.replace(/[^0-9]/g, '');
            if (value) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            input.value = value;
        }
    </script>
    <script>
        $('#dataTable').DataTable({
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

        function hapusMaterial(id, nama) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                html: "Material <strong>" + nama + "</strong> akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_material.php?id=' + id;
                }
            });
        }

        // ========== DROPDOWN DENGAN STATE TERSIMPAN ==========
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

        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('toggled');
            localStorage.setItem('sidebarToggled', document.querySelector('.sidebar').classList.contains('toggled') ? 'true' : 'false');
        });
        if (localStorage.getItem('sidebarToggled') === 'true') document.querySelector('.sidebar').classList.add('toggled');
    </script>
</body>

</html>