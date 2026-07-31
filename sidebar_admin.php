<?php
// Pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi database tersedia
if (!isset($conn)) {
    require_once __DIR__ . '/function.php';
}

// Ambil nama file halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// Fungsi active menu, aman kalau sudah ada di function.php
if (!function_exists('menuAktif')) {
    function menuAktif($page)
    {
        $current_page = basename($_SERVER['PHP_SELF']);
        return ($current_page == $page) ? 'active' : '';
    }
}

$level = $_SESSION['level'] ?? '';
?>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="text-center text-white py-3 bg-dark">
        <h5>SISTEM GUDANG</h5>
    </div>

    <nav>
        <?php if ($level == 'admin'): ?>
            <a href="dashboard.php" class="<?= menuAktif('dashboard.php'); ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        <?php endif; ?>

        <a href="index.php" class="<?= menuAktif('index.php'); ?>">
            <i class="fas fa-warehouse"></i> Stok Gudang
        </a>

        <?php if ($level == 'admin'): ?>
            <a href="barang_masuk.php" class="<?= menuAktif('barang_masuk.php'); ?>">
                <i class="fas fa-arrow-down"></i> Material Masuk
            </a>

            <a href="barang_keluar.php" class="<?= menuAktif('barang_keluar.php'); ?>">
                <i class="fas fa-arrow-up"></i> Material Keluar
            </a>
        <?php endif; ?>

        <hr style="margin: 5px 0; border-color: #555;">

        <!-- MASTER DATA DROPDOWN -->
        <div class="dropdown-btn">
            <div class="sb-nav-link-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            MASTER DATA
            <i class="fas fa-caret-down"></i>
        </div>

        <div class="dropdown-container">
            <?php if ($level == 'admin'): ?>
                <a href="user.php" class="<?= menuAktif('user.php'); ?>">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    Daftar Pengguna
                </a>
            <?php endif; ?>

            <a href="material.php" class="<?= menuAktif('material.php'); ?>">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                Daftar Material
            </a>

            <a href="tujuan.php" class="<?= menuAktif('tujuan.php'); ?>">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                Daftar Tujuan
            </a>
        </div>

        <hr style="margin: 5px 0; border-color: #555;">

        <!-- STOK CABANG DROPDOWN -->
        <div class="dropdown-btn">
            <div class="sb-nav-link-icon">
                <i class="fas fa-building"></i>
            </div>
            STOK CABANG
            <i class="fas fa-caret-down"></i>
        </div>

        <div class="dropdown-container">
            <?php
            if ($level == 'user_cabang') {
                $id_cabang = (int) ($_SESSION['cabang_id'] ?? 0);
                $query_cabang = "SELECT * FROM tujuan WHERE id_tujuan = '$id_cabang' ORDER BY id_tujuan ASC";
            } elseif ($level == 'admin') {
                $query_cabang = "
                    SELECT * FROM tujuan
                    WHERE nama IN ('Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                    ORDER BY FIELD(nama, 'Out - DGA 1', 'Out - DGA 2', 'Out - Har', 'Out - PRC')
                ";
            } else {
                $query_cabang = "SELECT * FROM tujuan WHERE 1 = 0";
            }

            $cabang = mysqli_query($conn, $query_cabang);

            if ($cabang) {
                while ($c = mysqli_fetch_assoc($cabang)) {
                    $activeCabang = (
                        $current_page == 'stok_cabang.php' &&
                        isset($_GET['id']) &&
                        $_GET['id'] == $c['id_tujuan']
                    ) ? 'active' : '';
            ?>
                    <a href="stok_cabang.php?id=<?= $c['id_tujuan']; ?>" class="<?= $activeCabang; ?>">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($c['nama']); ?>
                    </a>
            <?php
                }
            }
            ?>
        </div>

        <hr style="margin: 5px 0; border-color: #555;">

        <?php if ($level == 'user_cabang'): ?>
            <a href="permintaan_cabang.php" class="<?= menuAktif('permintaan_cabang.php'); ?>">
                <i class="fas fa-file-alt"></i> Permintaan Material
            </a>
        <?php endif; ?>

        <?php if ($level == 'admin'): ?>
            <a href="daftar_permintaan.php" class="<?= menuAktif('daftar_permintaan.php'); ?>">
                <i class="fas fa-file-alt"></i> Daftar Permintaan
            </a>

            <a href="surat_jalan.php" class="<?= menuAktif('surat_jalan.php'); ?>">
                <i class="fas fa-truck"></i> Surat Jalan
            </a>

            <a href="pengaturan.php" class="<?= menuAktif('pengaturan.php'); ?>">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
        <?php endif; ?>

        <hr style="margin: 5px 0; border-color: #555;">

        <a href="laporan.php" class="<?= menuAktif('laporan.php'); ?>">
            <i class="fas fa-chart-line"></i> Laporan
        </a>

        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Keluar
        </a>
    </nav>
</div>