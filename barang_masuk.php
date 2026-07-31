<?php
require 'function.php';
require 'cek.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Material Masuk - Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <link rel="stylesheet" href="assets/css/animation.css">
    <link rel="stylesheet" href="assets/css/sidebar-fixed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        :root {
            --primary: #0d6efd;
            --sidebar: #182230;
            --page-bg: #f5f7fb;
            --text-main: #111827;
        }

        body {
            background: var(--page-bg);
            color: var(--text-main);
            font-size: 14px;
        }

        /* SIDEBAR - disamakan dengan dashboard modern */
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

        .sidebar h6 {
            font-size: 12px;
            margin: 10px 0 5px 0;
            padding-left: 15px;
            letter-spacing: 1px;
            font-weight: normal;
            color: #adb5bd;
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

        .sidebar.toggled+.content {
            margin-left: 0;
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

        /* ========== TABEL Material MASUK ========== */
        .table-masuk {
            width: 100%;
        }

        .table-masuk td:nth-child(4) {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-masuk td:nth-child(4):hover {
            white-space: normal;
            word-break: break-word;
            background-color: #f8f9fc;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 6px 0;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
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
                                echo $cabang_user['nama'];
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
                <a href="barang_masuk.php" class="active"><i class="fas fa-arrow-down"></i> Material Masuk</a>
                <a href="barang_keluar.php"><i class="fas fa-arrow-up"></i> Material Keluar</a>
            <?php endif; ?>

            <hr>
            <div class="dropdown-btn">
                <i class="fas fa-database"></i> MASTER DATA <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-container">
                <?php if ($_SESSION['level'] == 'admin'): ?>
                    <a href="user.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div> Daftar Pengguna
                    </a>
                <?php endif; ?>
                <a href="material.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div> Daftar Material
                </a>
                <a href="tujuan.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-map-marker-alt"></i></div> Daftar Tujuan
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
                ?>
                    <a href="stok_cabang.php?id=<?= $c['id_tujuan']; ?>">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($c['nama']); ?>
                    </a>
                <?php } ?>
            </div>

            <hr>

            <?php if ($_SESSION['level'] == 'user_cabang'): ?>
                <a href="permintaan_cabang.php">
                    <i class="fas fa-file-alt"></i> Permintaan Material
                </a>
            <?php endif; ?>

            <?php if ($_SESSION['level'] == 'admin'): ?>
                <a href="daftar_permintaan.php">
                    <i class="fas fa-file-alt"></i> Daftar Permintaan
                </a>
                <a href="surat_jalan.php">
                    <i class="fas fa-truck"></i> Surat Jalan
                </a>
                <a href="pengaturan.php">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            <?php endif; ?>

            <hr>

            <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </div>

    <div class="content">
        <div class="page-header mb-4">
            <h1 class="page-title">Material Masuk ke Gudang</h1>
            <p class="page-subtitle">Halaman untuk mencatat material masuk ke gudang.</p>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-paper-plane"></i> Form Material Masuk
            </div>


            <div class="card-body">
                <form method="post" action="proses_masuk_gudang.php">
                    <div class="row">
                        <div class="col-md-3"><label>Tanggal</label><input type="text" name="tanggal" class="form-control tanggal" placeholder="Hari/Bulan/Tahun" required></div>
                        <div class="col-md-3"><label>Pilih Material</label>
                            <select name="id_material" class="form-control" required>
                                <option value="">Material</option>
                                <?php $material = mysqli_query($conn, "SELECT * FROM material ORDER BY id_material ASC");
                                while ($m = mysqli_fetch_assoc($material)) {
                                    echo "<option value='{$m['id_material']}'>{$m['kode_material']} - {$m['nama']}</option>";
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-2"><label>Jumlah</label><input type="number" name="qty" class="form-control" placeholder="Jumlah" required></div>
                        <div class="col-md-2">
                            <label>Supplier</label>
                            <input type="text" class="form-control" value="Pusat" readonly disabled>
                            <input type="hidden" name="supplier" value="Pusat">
                        </div>
                        <div class="col-md-2"><label>&nbsp;</label><button type="submit" name="barang_masuk" class="btn btn-success btn-block">Simpan</button></div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-info text-white"><i class="fas fa-history"></i> Riwayat Material Masuk</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-masuk" id="dataTable" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Jumlah</th>
                                <th>Supplier</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            $query = mysqli_query($conn, "SELECT t.*, m.kode_material, m.nama, m.satuan FROM transaksi_masuk_gudang t JOIN material m ON m.id_material = t.id_material ORDER BY t.tanggal DESC");
                            while ($data = mysqli_fetch_assoc($query)) { ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= tgl_panjang($data['tanggal']); ?></td>
                                    <td><?= $data['kode_material']; ?></td>
                                    <td><?= $data['nama']; ?></td>
                                    <td><?= $data['qty']; ?> <?= $data['satuan']; ?></td>
                                    <td><?= $data['supplier'] ?? '-'; ?></td>
                                    <td><button class="btn btn-sm btn-danger" onclick="hapusTransaksi(<?= $data['id_transaksi']; ?>,<?= $data['id_material']; ?>,<?= $data['qty']; ?>)"><i class="fas fa-trash"></i> Hapus</button></td>
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
        flatpickr(".tanggal", {
            dateFormat: "Y-m-d",
            altFormat: "d-m-Y",
            locale: "id",
            altInput: true,
            allowInput: true
        });
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

        function hapusTransaksi(id, id_material, qty) {
            Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Transaksi ini akan dihapus dan stok akan dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'hapus_transaksi.php?type=masuk_gudang&id=' + id + '&id_material=' + id_material + '&qty=' + qty;
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
        if (localStorage.getItem('sidebarToggled') === 'true') document.querySelector('.sidebar').classList.add('toggled');
    </script>
</body>

</html>