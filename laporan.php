<?php
require 'function.php';
require 'cek.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("Location: permintaan_cabang.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laporan - Sistem Gudang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
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

        .sidebar.toggled~.content {
            margin-left: 0 !important;
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
            border-bottom: none;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table th {
            vertical-align: middle;
            font-weight: 700;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
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
                <a href="material.php"><i class="fas fa-boxes"></i> Daftar Material</a>
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
                <a href="laporan.php" class="active"><i class="fas fa-chart-line"></i> Laporan</a>
            <?php endif; ?>

            <hr>

            
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="page-title">
            <h2>Laporan Transaksi</h2>
            <p>Halaman untuk melihat ringkasan transaksi material masuk, material keluar, transfer antar cabang, dan rekap pemakaian.</p>
        </div>

        <!-- Laporan Material Masuk ke Gudang -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <i class="fas fa-arrow-down"></i> Laporan Material Masuk ke Gudang
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableMasuk">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nama Material</th>
                                <th>Jumlah</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT t.*, m.kode_material, m.nama, m.satuan
                                FROM transaksi_masuk_gudang t
                                JOIN material m ON m.id_material = t.id_material
                                ORDER BY t.tanggal DESC
                            ");
                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= tgl_panjang($data['tanggal']); ?></td>
                                    <td><?= $data['kode_material']; ?></td>
                                    <td><?= $data['nama']; ?></td>
                                    <td><?= $data['qty']; ?> <?= $data['satuan']; ?></td>
                                    <td><?= $data['supplier'] ?? '-'; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Laporan Material Keluar dari Gudang -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-arrow-up"></i> Laporan Material Keluar dari Gudang
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableKeluar">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nama Material</th>
                                <th>Jumlah</th>
                                <th>Tujuan Cabang</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT t.*, m.kode_material, m.nama, m.satuan, tj.nama as tujuan
                                FROM transaksi_keluar_gudang t
                                JOIN material m ON m.id_material = t.id_material
                                JOIN tujuan tj ON tj.id_tujuan = t.id_tujuan
                                ORDER BY t.tanggal DESC
                            ");
                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= tgl_panjang($data['tanggal']); ?></td>
                                    <td><?= $data['kode_material']; ?></td>
                                    <td><?= $data['nama']; ?></td>
                                    <td><?= $data['qty']; ?> <?= $data['satuan']; ?></td>
                                    <td><?= $data['tujuan']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Laporan Transfer Antar Cabang -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <i class="fas fa-exchange-alt"></i> Laporan Transfer Antar Cabang
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tableTransfer">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nama Material</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Dari Cabang</th>
                                <th>Ke Cabang</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = mysqli_query($conn, "
                                SELECT t.*, m.kode_material, m.nama, m.satuan, 
                                td.nama as dari_nama, tk.nama as ke_nama
                                FROM transaksi_keluar_tujuan t
                                JOIN material m ON m.id_material = t.id_material
                                JOIN tujuan td ON td.id_tujuan = t.dari_tujuan
                                JOIN tujuan tk ON tk.id_tujuan = t.ke_tujuan
                                ORDER BY t.tanggal DESC
                            ");
                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= tgl_panjang($data['tanggal']); ?></td>
                                    <td><?= $data['kode_material']; ?></td>
                                    <td><?= $data['nama']; ?></td>
                                    <td><?= $data['qty']; ?> <?= $data['satuan']; ?></td>
                                    <td><?= $data['satuan']; ?></td>
                                    <td><?= $data['dari_nama']; ?></td>
                                    <td><?= $data['ke_nama']; ?></td>
                                    <td><?= $data['keterangan'] ?? '-'; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- LAPORAN REKAP MATERIAL KELUAR + TOTAL PER TANGGAL -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-chart-line"></i> Rekap Material Keluar per Material
                <div class="float-right">
                    <form method="GET" class="form-inline">
                        <select name="bulan" class="form-control form-control-sm mr-2">
                            <option value="">Semua Bulan</option>
                            <option value="01" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '01') ? 'selected' : ''; ?>>Januari</option>
                            <option value="02" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '02') ? 'selected' : ''; ?>>Februari</option>
                            <option value="03" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '03') ? 'selected' : ''; ?>>Maret</option>
                            <option value="04" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '04') ? 'selected' : ''; ?>>April</option>
                            <option value="05" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '05') ? 'selected' : ''; ?>>Mei</option>
                            <option value="06" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '06') ? 'selected' : ''; ?>>Juni</option>
                            <option value="07" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '07') ? 'selected' : ''; ?>>Juli</option>
                            <option value="08" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '08') ? 'selected' : ''; ?>>Agustus</option>
                            <option value="09" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '09') ? 'selected' : ''; ?>>September</option>
                            <option value="10" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '10') ? 'selected' : ''; ?>>Oktober</option>
                            <option value="11" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '11') ? 'selected' : ''; ?>>November</option>
                            <option value="12" <?= (isset($_GET['bulan']) && $_GET['bulan'] == '12') ? 'selected' : ''; ?>>Desember</option>
                        </select>
                        <select name="tahun" class="form-control form-control-sm mr-2">
                            <?php for ($y = 2024; $y <= 2030; $y++): ?>
                                <option value="<?= $y; ?>" <?= (isset($_GET['tahun']) && $_GET['tahun'] == $y) ? 'selected' : ''; ?>><?= $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-light">Filter</button>
                        <a href="laporan.php" class="btn btn-sm btn-secondary ml-1">Reset</a>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <!-- 1. REKAP PER MATERIAL -->
                <h6>A. Rekap per Material</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped" id="tableRekap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Material</th>
                                <th>Nama Material</th>
                                <th>Satuan</th>
                                <th class="text-right">Harga Satuan (Rp)</th>
                                <th class="text-right">Total Keluar</th>
                                <th class="text-right">Total Harga (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $bulan_filter = isset($_GET['bulan']) ? $_GET['bulan'] : '';
                            $tahun_filter = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

                            $where_rekap = "";
                            if (!empty($bulan_filter)) {
                                $where_rekap = "AND MONTH(k.tanggal) = '$bulan_filter' AND YEAR(k.tanggal) = '$tahun_filter'";
                            }

                            $no_rekap = 1;
                            $query_rekap = mysqli_query($conn, "
                                SELECT 
                                    m.kode_material,
                                    m.nama,
                                    m.satuan,
                                    m.harga,
                                    COALESCE(SUM(k.qty), 0) as total_qty
                                FROM material m
                                LEFT JOIN transaksi_keluar_gudang k ON k.id_material = m.id_material
                                WHERE 1=1 $where_rekap
                                GROUP BY m.id_material
                                ORDER BY m.id_material ASC
                            ");

                            $grand_qty = 0;
                            $grand_total = 0;

                            while ($row = mysqli_fetch_assoc($query_rekap)):
                                $total_qty = $row['total_qty'];
                                $total_harga = $total_qty * $row['harga'];
                                $grand_qty += $total_qty;
                                $grand_total += $total_harga;
                            ?>
                                <tr>
                                    <td><?= $no_rekap++; ?></td>
                                    <td><?= $row['kode_material']; ?></td>
                                    <td><?= $row['nama']; ?></td>
                                    <td><?= $row['satuan']; ?></td>
                                    <td class="text-right"><?= number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td class="text-right"><?= number_format($total_qty, 0, ',', '.'); ?></td>
                                    <td class="text-right"><?= number_format($total_harga, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <th colspan="5" class="text-right">TOTAL</th>
                                <th class="text-right"><?= number_format($grand_qty, 0, ',', '.'); ?></th>
                                <th class="text-right"><?= number_format($grand_total, 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 2. TOTAL NILAI DAN QTY PER TANGGAL -->
                <h6>B. Total Pemakaian per Tanggal</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="tablePerTanggal">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th class="text-right">Total Material Keluar</th>
                                <th class="text-right">Total Nilai Pemakaian (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil semua tanggal yang ada transaksi dalam periode filter
                            $where_tanggal = "";
                            if (!empty($bulan_filter)) {
                                $where_tanggal = "WHERE MONTH(tanggal) = '$bulan_filter' AND YEAR(tanggal) = '$tahun_filter'";
                            }

                            $query_tanggal = mysqli_query($conn, "
                                SELECT 
                                    tanggal,
                                    SUM(qty) as total_qty,
                                    SUM(qty * harga) as total_nilai
                                FROM (
                                    SELECT k.tanggal, k.qty, m.harga
                                    FROM transaksi_keluar_gudang k
                                    JOIN material m ON m.id_material = k.id_material
                                ) as data
                                $where_tanggal
                                GROUP BY tanggal
                                ORDER BY tanggal ASC
                            ");

                            $no_tgl = 1;
                            $grand_total_qty = 0;
                            $grand_total_nilai = 0;

                            while ($row_tgl = mysqli_fetch_assoc($query_tanggal)):
                                $grand_total_qty += $row_tgl['total_qty'];
                                $grand_total_nilai += $row_tgl['total_nilai'];
                            ?>
                                <tr>
                                    <td><?= $no_tgl++; ?></td>
                                    <td><?= tgl_panjang($row_tgl['tanggal']); ?></td>
                                    <td class="text-right"><?= number_format($row_tgl['total_qty'], 0, ',', '.'); ?></td>
                                    <td class="text-right"><?= number_format($row_tgl['total_nilai'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>

                            <?php if ($no_tgl == 1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data transaksi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr>
                                <th colspan="2" class="text-right">TOTAL</th>
                                <th class="text-right"><?= number_format($grand_total_qty, 0, ',', '.'); ?></th>
                                <th class="text-right"><?= number_format($grand_total_nilai, 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

            <script>
                $(document).ready(function() {
                    var bahasaTabel = {
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
                    };

                    $('#tableMasuk').DataTable({
                        pageLength: 10,
                        language: bahasaTabel
                    });

                    $('#tableKeluar').DataTable({
                        pageLength: 10,
                        language: bahasaTabel
                    });

                    $('#tableTransfer').DataTable({
                        pageLength: 10,
                        language: bahasaTabel
                    });

                    $('#tableRekap').DataTable({
                        pageLength: 10,
                        language: bahasaTabel
                    });

                    $('#tablePerTanggal').DataTable({
                        pageLength: 10,
                        language: bahasaTabel
                    });
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
                // ========== SAMPAI SINI ==========

                // Toggle sidebar model lama, tetapi content ikut melebar
                const sidebarToggle = document.getElementById('sidebarToggle');
                const sidebar = document.querySelector('.sidebar');

                if (sidebarToggle && sidebar) {
                    sidebarToggle.addEventListener('click', function() {
                        sidebar.classList.toggle('toggled');
                        localStorage.setItem('sidebarToggled', sidebar.classList.contains('toggled') ? 'true' : 'false');
                    });

                    if (localStorage.getItem('sidebarToggled') === 'true') {
                        sidebar.classList.add('toggled');
                    }
                }
            </script>
</body>

</html>