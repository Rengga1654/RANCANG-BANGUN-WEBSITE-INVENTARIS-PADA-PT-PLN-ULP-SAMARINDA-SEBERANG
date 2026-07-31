<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Koneksi database
$conn = mysqli_connect("localhost", "root", "", "stockbarang2");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
if (!function_exists('menuAktif')) {
    function menuAktif($page)
    {
        $current_page = basename($_SERVER['PHP_SELF']);
        return ($current_page == $page) ? 'active' : '';
    }
}

// Include notifikasi
require_once 'notifikasi.php';

// Fungsi get stok gudang
function getStokGudang($id_material)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT jumlah FROM stock_gudang WHERE id_material='$id_material'");
    $data = mysqli_fetch_assoc($query);
    return $data ? $data['jumlah'] : 0;
}

// Format tanggal
function formatTanggal($tanggal, $format = 'pendek')
{
    if ($tanggal == '' || $tanggal == '0000-00-00') {
        return '-';
    }
    $timestamp = strtotime($tanggal);
    if ($format == 'panjang') {
        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        return date('j', $timestamp) . ' ' . $bulan[(int)date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }
    return date('d-m-Y', $timestamp);
}

function tgl($tanggal)
{
    return formatTanggal($tanggal, 'pendek');
}

function tgl_panjang($tanggal)
{
    return formatTanggal($tanggal, 'panjang');
}
