<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("location: login.php");
    exit();
}

// Jika user cabang, batasi akses halaman
if ($_SESSION['level'] == 'user_cabang') {
    $current_page = basename($_SERVER['PHP_SELF']);

    // Halaman yang BOLEH diakses user cabang
    $allowed_pages = [
        'stok_cabang.php',
        'logout.php',
        'laporan.php',
        'material.php',
        'tujuan.php',
        'index.php',
        'permintaan_cabang.php',
        'proses_permintaan.php',
        'proses_verifikasi.php',
        'hapus_permintaan.php',

        // tambahkan ini
        'proses_gunakan_cabang.php',
        'proses_transfer.php',
        'hapus_transaksi.php'
    ];

    if (!in_array($current_page, $allowed_pages)) {
        header("location: stok_cabang.php?id=" . $_SESSION['cabang_id']);
        exit();
    }
}
