<?php
require 'function.php';
require 'cek.php';

// Hanya admin yang bisa akses
if ($_SESSION['level'] != 'admin') {
    echo "<script>alert('Akses ditolak!'); window.location.href='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar User - Sistem Gudang</title>
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
            text-decoration: none;
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
            border-bottom: 1px solid var(--border);
        }

        /* TABEL STYLING - mengikuti gaya Daftar Material */
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
            vertical-align: middle;
            color: #111827;
            white-space: nowrap;
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

        /* KHUSUS TABEL PENGGUNA */
        .table-user {
            width: 100%;
        }

        .table-user th:nth-child(1),
        .table-user td:nth-child(1) {
            width: 7%;
            text-align: center;
        }

        .table-user th:nth-child(2) {
            width: 22%;
        }

        .table-user th:nth-child(3) {
            width: 23%;
        }

        .table-user th:nth-child(4),
        .table-user td:nth-child(4) {
            width: 12%;
            text-align: center;
        }

        .table-user th:nth-child(5) {
            width: 22%;
        }

        .table-user th:nth-child(6),
        .table-user td:nth-child(6) {
            width: 14%;
            text-align: center;
            white-space: nowrap;
        }

        .table-user .btn {
            margin: 2px;
        }
    </style>
</head>

<body>

    <?php tampilkanNotifikasi(); ?>

    <!-- NAVBAR DENGAN USER DROPDOWN -->
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

    <!-- SIDEBAR -->
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
                    <a href="user.php" class="active"><i class="fas fa-users"></i> Daftar Pengguna</a>
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
                $cabang = mysqli_query($conn, "SELECT * FROM tujuan ORDER BY id_tujuan ASC");
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

    <!-- CONTENT -->
    <div class="content">
        <div class="page-title mb-4">
            <h2>Daftar Pengguna</h2>
            <p>Halaman untuk mengelola akun admin dan pengguna cabang.</p>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-users"></i> Manajemen Pengguna
                <button class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#modalTambahUser">
                    <i class="fas fa-plus"></i> Tambah Pengguna
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-user" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Email</th>
                                <th>Nama Lengkap</th>
                                <th>Level</th>
                                <th>Cabang</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT u.*, t.nama as cabang_nama 
                                FROM login u
                                LEFT JOIN tujuan t ON t.id_tujuan = u.cabang_id
                                ORDER BY u.iduser ASC
                            ");
                            while ($data = mysqli_fetch_assoc($query)) {
                                $level_badge = ($data['level'] == 'admin') ?
                                    '<span class="badge badge-danger">Admin</span>' :
                                    '<span class="badge badge-info">User Cabang</span>';
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($data['email']); ?></td>
                                    <td><?= htmlspecialchars($data['nama_lengkap'] ?? '-'); ?></td>
                                    <td><?= $level_badge; ?></td>
                                    <td><?= htmlspecialchars($data['cabang_nama'] ?? '-'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit<?= $data['iduser']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if ($data['email'] != 'admin@gmail.com'): ?>
                                            <button class="btn btn-sm btn-danger" onclick="hapusUser(<?= $data['iduser']; ?>, '<?= htmlspecialchars($data['email']); ?>')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Modal Edit User -->
                                <div class="modal fade" id="modalEdit<?= $data['iduser']; ?>">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">Edit Pengguna</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form method="post" action="proses_edit_user.php">
                                                <div class="modal-body">
                                                    <input type="hidden" name="iduser" value="<?= $data['iduser']; ?>">

                                                    <?php if ($data['email'] == 'admin@gmail.com'): ?>
                                                        <!-- ========== EDIT ADMIN SENDIRI ========== -->
                                                        <input type="hidden" name="email" value="<?= $data['email']; ?>">
                                                        <input type="hidden" name="level" value="admin">
                                                        <input type="hidden" name="cabang_id" value="">

                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="email" class="form-control" value="<?= htmlspecialchars($data['email']); ?>" readonly disabled>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Password (kosongkan jika tidak diubah)</label>
                                                            <input type="password" name="password" class="form-control" placeholder="Masukkan password baru">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Nama Lengkap</label>
                                                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap'] ?? ''); ?>">
                                                        </div>

                                                    <?php else: ?>
                                                        <!-- ========== EDIT USER CABANG ========== -->
                                                        <input type="hidden" name="level" value="<?= $data['level']; ?>">

                                                        <div class="form-group">
                                                            <label>Email</label>
                                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Password (kosongkan jika tidak diubah)</label>
                                                            <input type="password" name="password" class="form-control" placeholder="Masukkan password baru">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Nama Lengkap</label>
                                                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap'] ?? ''); ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Cabang</label>
                                                            <select name="cabang_id" class="form-control" required>
                                                                <option value="">Pilih Cabang</option>
                                                                <?php
                                                                // Ambil daftar cabang yang sudah punya user (KECUALI user ini sendiri)
                                                                $cabang_terpakai = [];
                                                                $query_terpakai = mysqli_query($conn, "
                                                                    SELECT DISTINCT cabang_id FROM login 
                                                                    WHERE level = 'user_cabang' 
                                                                    AND cabang_id IS NOT NULL 
                                                                    AND iduser != '{$data['iduser']}'
                                                                ");
                                                                while ($row = mysqli_fetch_assoc($query_terpakai)) {
                                                                    $cabang_terpakai[] = $row['cabang_id'];
                                                                }

                                                                $cabang = mysqli_query($conn, "SELECT * FROM tujuan ORDER BY nama ASC");
                                                                while ($c = mysqli_fetch_assoc($cabang)) {
                                                                    // Tampilkan jika: belum dipakai user lain ATAU itu cabang milik user ini sendiri
                                                                    if (!in_array($c['id_tujuan'], $cabang_terpakai) || $data['cabang_id'] == $c['id_tujuan']) {
                                                                        $selected = ($data['cabang_id'] == $c['id_tujuan']) ? 'selected' : '';
                                                                        echo "<option value='{$c['id_tujuan']}' $selected>{$c['nama']}</option>";
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                            <small class="text-muted">Cabang yang sudah memiliki user lain tidak bisa dipilih.</small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit_user" class="btn btn-warning">Simpan</button>
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

    <!-- Modal Tambah User -->
    <div class="modal fade" id="modalTambahUser">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Pengguna Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form method="post" action="proses_tambah_user.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="user@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap user">
                        </div>
                        <div class="form-group">
                            <label>Level</label>
                            <input type="text" class="form-control" value="User Cabang" readonly disabled>
                            <input type="hidden" name="level" value="user_cabang">
                            <small class="text-muted">Level otomatis User Cabang. User Admin hanya 1.</small>
                        </div>
                        <div class="form-group">
                            <label>Cabang (khusus User Cabang)</label>
                            <select name="cabang_id" class="form-control" required>
                                <option value="">Pilih Cabang</option>
                                <?php
                                // Ambil daftar cabang yang sudah memiliki user cabang
                                $cabang_terpakai = [];
                                $query_terpakai = mysqli_query($conn, "
                                    SELECT DISTINCT cabang_id FROM login 
                                    WHERE level = 'user_cabang' AND cabang_id IS NOT NULL
                                ");
                                while ($row = mysqli_fetch_assoc($query_terpakai)) {
                                    $cabang_terpakai[] = $row['cabang_id'];
                                }

                                // Tampilkan hanya cabang yang BELUM terpakai
                                $cabang = mysqli_query($conn, "SELECT * FROM tujuan ORDER BY nama ASC");
                                while ($c = mysqli_fetch_assoc($cabang)) {
                                    if (!in_array($c['id_tujuan'], $cabang_terpakai)) {
                                        echo "<option value='{$c['id_tujuan']}'>{$c['nama']}</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_user" class="btn btn-success">Simpan</button>
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

        function hapusUser(id, email) {
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                html: "User <strong>" + email + "</strong> akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_user.php?id=' + id;
                }
            });
        }

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
        // ========== SAMPAI SINI ==========

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