<?php
require 'function.php';
require 'cek.php';

if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Fungsi bulan romawi untuk nomor surat
function bulanRomawi($bulan)
{
    $romawi = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
        5 => 'V',
        6 => 'VI',
        7 => 'VII',
        8 => 'VIII',
        9 => 'IX',
        10 => 'X',
        11 => 'XI',
        12 => 'XII'
    ];

    return $romawi[(int)$bulan];
}

// Fungsi untuk membaca tanggal agar aman dipakai buat nomor surat
function tanggalUntukNomor($tanggal)
{
    $tanggal = trim($tanggal);

    if ($tanggal == '') {
        return date('Y-m-d');
    }

    // Format Y-m-d
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        return $tanggal;
    }

    // Format d-m-Y
    if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $tanggal, $m)) {
        return $m[3] . '-' . $m[2] . '-' . $m[1];
    }

    // Format d/m/Y
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $tanggal, $m)) {
        return $m[3] . '-' . $m[2] . '-' . $m[1];
    }

    $time = strtotime(str_replace('/', '-', $tanggal));
    return $time ? date('Y-m-d', $time) : date('Y-m-d');
}

// Fungsi generate nomor surat otomatis
function generateNoSuratJalan($conn, $tanggal_surat)
{
    $tanggal = tanggalUntukNomor($tanggal_surat);
    $bulan = (int)date('n', strtotime($tanggal));
    $tahun = date('Y', strtotime($tanggal));
    $bulan_romawi = bulanRomawi($bulan);

    $like = "SJ/%/$bulan_romawi/$tahun";

    $query = mysqli_query($conn, "
        SELECT no_surat 
        FROM surat_jalan 
        WHERE no_surat LIKE '$like'
        ORDER BY id_surat DESC
    ");

    $nomor_terakhir = 0;

    while ($row = mysqli_fetch_assoc($query)) {
        $pattern = '/^SJ\/(\d+)\/' . preg_quote($bulan_romawi, '/') . '\/' . $tahun . '$/';

        if (preg_match($pattern, $row['no_surat'], $match)) {
            $nomor_terakhir = max($nomor_terakhir, (int)$match[1]);
        }
    }

    $nomor_baru = $nomor_terakhir + 1;

    do {
        $no_surat = "SJ/" . str_pad($nomor_baru, 3, '0', STR_PAD_LEFT) . "/$bulan_romawi/$tahun";

        $cek = mysqli_query($conn, "
            SELECT id_surat 
            FROM surat_jalan 
            WHERE no_surat = '$no_surat'
            LIMIT 1
        ");

        $nomor_baru++;
    } while (mysqli_num_rows($cek) > 0);

    return $no_surat;
}

if (isset($_POST['buat_surat_jalan'])) {
    $id_permintaan = (int)$_POST['id_permintaan'];
    $tanggal_surat = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
    $tanggal_kirim = mysqli_real_escape_string($conn, $_POST['tanggal_kirim']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan'] ?? '');

    // Jika nomor surat kosong, sistem buat otomatis
    $no_surat_input = trim($_POST['no_surat'] ?? '');

    if ($no_surat_input == '') {
        $no_surat_input = generateNoSuratJalan($conn, $tanggal_surat);
    }

    $no_surat = mysqli_real_escape_string($conn, $no_surat_input);

    // Ambil data permintaan header
    $query_permintaan = mysqli_query($conn, "SELECT * FROM permintaan WHERE id_permintaan = '$id_permintaan'");
    $permintaan = mysqli_fetch_assoc($query_permintaan);

    if (!$permintaan) {
        setNotifikasi('error', 'Permintaan tidak ditemukan!');
        header("Location: surat_jalan.php");
        exit;
    }

    $id_tujuan = $permintaan['id_tujuan'];

    // Cek apakah permintaan ini sudah pernah dibuatkan surat jalan
    $cek_permintaan_sj = mysqli_query($conn, "
        SELECT id_surat, no_surat 
        FROM surat_jalan 
        WHERE id_permintaan = '$id_permintaan'
        LIMIT 1
    ");

    if (mysqli_num_rows($cek_permintaan_sj) > 0) {
        $data_sj = mysqli_fetch_assoc($cek_permintaan_sj);

        setNotifikasi('error', 'Permintaan ini sudah dibuatkan surat jalan dengan nomor ' . $data_sj['no_surat'] . '.');
        header("Location: surat_jalan.php");
        exit;
    }

    // Ambil detail permintaan
    $query_detail = mysqli_query($conn, "SELECT * FROM permintaan_detail WHERE id_permintaan = '$id_permintaan'");

    if (mysqli_num_rows($query_detail) == 0) {
        setNotifikasi('error', 'Detail permintaan tidak ditemukan!');
        header("Location: surat_jalan.php");
        exit;
    }

    // Cek apakah nomor surat sudah ada
    $cek_surat = mysqli_query($conn, "SELECT * FROM surat_jalan WHERE no_surat = '$no_surat'");
    if (mysqli_num_rows($cek_surat) > 0) {
        setNotifikasi('error', 'Nomor surat sudah ada!');
        header("Location: surat_jalan.php");
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Simpan surat jalan header
        $insert = mysqli_query($conn, "INSERT INTO surat_jalan 
            (no_surat, id_permintaan, id_tujuan, tanggal_surat, tanggal_kirim, catatan, status) 
            VALUES 
            ('$no_surat', '$id_permintaan', '$id_tujuan', '$tanggal_surat', '$tanggal_kirim', '$catatan', 'draft')
        ");

        if (!$insert) {
            throw new Exception('Gagal menyimpan surat jalan');
        }

        $id_surat = mysqli_insert_id($conn);

        // Simpan detail surat jalan
        while ($detail = mysqli_fetch_assoc($query_detail)) {
            $insert_detail = mysqli_query($conn, "INSERT INTO surat_jalan_detail 
                (id_surat, id_material, jumlah_kirim) 
                VALUES 
                ('$id_surat', '{$detail['id_material']}', '{$detail['jumlah']}')
            ");

            if (!$insert_detail) {
                throw new Exception('Gagal menyimpan detail surat jalan');
            }
        }

        mysqli_commit($conn);
        setNotifikasi('success', 'Surat jalan berhasil dibuat dengan nomor ' . $no_surat . '!');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal membuat surat jalan: ' . $e->getMessage());
    }

    header("Location: surat_jalan.php");
    exit;
}
?>