<?php
require 'function.php';
require 'cek.php';

// Hanya admin yang bisa akses
if ($_SESSION['level'] != 'admin') {
    header("Location: index.php");
    exit;
}

// ===== Helper validasi tanggal =====
function validDateDashboard($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Ambil filter dari GET
$allowed_filter = ['hari', 'minggu', 'bulan'];
$filter = isset($_GET['filter']) && in_array($_GET['filter'], $allowed_filter) ? $_GET['filter'] : 'bulan';

// Jenis grafik utama
$allowed_jenis_grafik = ['keluar', 'masuk', 'perbandingan'];
$jenis_grafik = isset($_GET['jenis_grafik']) && in_array($_GET['jenis_grafik'], $allowed_jenis_grafik) ? $_GET['jenis_grafik'] : 'keluar';

// Filter tanggal range
$tgl_mulai = isset($_GET['tgl_mulai']) && validDateDashboard($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) && validDateDashboard($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : date('Y-m-t');

// Pastikan tanggal mulai tidak lebih besar dari tanggal selesai
if (strtotime($tgl_mulai) > strtotime($tgl_selesai)) {
    $temp = $tgl_mulai;
    $tgl_mulai = $tgl_selesai;
    $tgl_selesai = $temp;
}

// Ambil filter material dari GET
$material_filter = isset($_GET['material_id']) && $_GET['material_id'] != '' ? (int)$_GET['material_id'] : 0;
$current_page = basename($_SERVER['PHP_SELF']);
$periode_card = date('d/m/Y', strtotime($tgl_mulai)) . ' - ' . date('d/m/Y', strtotime($tgl_selesai));

// Ambil daftar material untuk dropdown
$query_material_list = mysqli_query($conn, "SELECT id_material, nama FROM material ORDER BY id_material ASC");
$material_list = [];
while ($row = mysqli_fetch_assoc($query_material_list)) {
    $material_list[] = $row;
}

// Ambil nama material yang dipilih
$material_terpilih = '';
if ($material_filter > 0) {
    $query_nama = mysqli_query($conn, "SELECT nama FROM material WHERE id_material = $material_filter");
    $nama_material = mysqli_fetch_assoc($query_nama);
    $material_terpilih = $nama_material ? $nama_material['nama'] : '';
}

$data_labels = [];
$data_jumlah = [];
$data_masuk = [];

// ========== GRAFIK MATERIAL KELUAR / MASUK PER PERIODE ==========
if ($filter == 'hari') {
    $start = new DateTime($tgl_mulai);
    $end = new DateTime($tgl_selesai);
    $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $daterange = new DatePeriod($start, $interval, $end);

    $hari_indonesia = [
        'Mon' => 'Sen',
        'Tue' => 'Sel',
        'Wed' => 'Rab',
        'Thu' => 'Kam',
        'Fri' => 'Jum',
        'Sat' => 'Sab',
        'Sun' => 'Min'
    ];

    foreach ($daterange as $date) {
        $tanggal = $date->format('Y-m-d');
        $nama_hari = $date->format('D');
        $nama_hari_indo = $hari_indonesia[$nama_hari] . ' ' . $date->format('d/m');

        $sql_keluar = "SELECT COALESCE(SUM(qty), 0) as total
                       FROM transaksi_keluar_gudang
                       WHERE tanggal = '$tanggal'";
        if ($material_filter > 0) {
            $sql_keluar .= " AND id_material = $material_filter";
        }
        $query_keluar = mysqli_query($conn, $sql_keluar);
        $data_keluar = mysqli_fetch_assoc($query_keluar);

        $sql_masuk = "SELECT COALESCE(SUM(qty), 0) as total
                      FROM transaksi_masuk_gudang
                      WHERE tanggal = '$tanggal'";
        if ($material_filter > 0) {
            $sql_masuk .= " AND id_material = $material_filter";
        }
        $query_masuk = mysqli_query($conn, $sql_masuk);
        $data_masuk_row = mysqli_fetch_assoc($query_masuk);

        $data_labels[] = $nama_hari_indo;
        $data_jumlah[] = (int)$data_keluar['total'];
        $data_masuk[] = (int)$data_masuk_row['total'];
    }
} elseif ($filter == 'minggu') {
    $start = new DateTime($tgl_mulai);
    $end = new DateTime($tgl_selesai);
    $end->modify('+1 day');

    $interval = new DateInterval('P1W');
    $period = new DatePeriod($start, $interval, $end);
    $minggu_ke = 1;

    foreach ($period as $week_start) {
        $week_end = clone $week_start;
        $week_end->modify('+6 days');

        if ($week_end > $end) {
            $week_end = clone $end;
            $week_end->modify('-1 day');
        }

        $start_str = $week_start->format('Y-m-d');
        $end_str = $week_end->format('Y-m-d');

        $sql_keluar = "SELECT COALESCE(SUM(qty), 0) as total
                       FROM transaksi_keluar_gudang
                       WHERE tanggal BETWEEN '$start_str' AND '$end_str'";
        if ($material_filter > 0) {
            $sql_keluar .= " AND id_material = $material_filter";
        }
        $query_keluar = mysqli_query($conn, $sql_keluar);
        $data_keluar = mysqli_fetch_assoc($query_keluar);

        $sql_masuk = "SELECT COALESCE(SUM(qty), 0) as total
                      FROM transaksi_masuk_gudang
                      WHERE tanggal BETWEEN '$start_str' AND '$end_str'";
        if ($material_filter > 0) {
            $sql_masuk .= " AND id_material = $material_filter";
        }
        $query_masuk = mysqli_query($conn, $sql_masuk);
        $data_masuk_row = mysqli_fetch_assoc($query_masuk);

        $data_labels[] = "Minggu $minggu_ke";
        $data_jumlah[] = (int)$data_keluar['total'];
        $data_masuk[] = (int)$data_masuk_row['total'];

        $minggu_ke++;
    }
} else {
    $start = new DateTime($tgl_mulai);
    $end = new DateTime($tgl_selesai);
    $end->modify('+1 month');

    $months = [];
    $current = clone $start;
    $current->modify('first day of this month');

    while ($current <= $end) {
        $key = $current->format('Y-m');
        if (!isset($months[$key])) {
            $months[$key] = [
                'tahun' => $current->format('Y'),
                'bulan' => $current->format('m')
            ];
        }
        $current->modify('+1 month');
    }

    $bulan_indonesia = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ags',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des'
    ];

    foreach ($months as $month) {
        $tahun = $month['tahun'];
        $bulan = $month['bulan'];
        $nama_bulan_indo = $bulan_indonesia[(int)$bulan] . ' ' . $tahun;

        $sql_keluar = "SELECT COALESCE(SUM(qty), 0) as total
                       FROM transaksi_keluar_gudang
                       WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'";
        if ($material_filter > 0) {
            $sql_keluar .= " AND id_material = $material_filter";
        }
        $query_keluar = mysqli_query($conn, $sql_keluar);
        $data_keluar = mysqli_fetch_assoc($query_keluar);

        $sql_masuk = "SELECT COALESCE(SUM(qty), 0) as total
                      FROM transaksi_masuk_gudang
                      WHERE MONTH(tanggal) = '$bulan' AND YEAR(tanggal) = '$tahun'";
        if ($material_filter > 0) {
            $sql_masuk .= " AND id_material = $material_filter";
        }
        $query_masuk = mysqli_query($conn, $sql_masuk);
        $data_masuk_row = mysqli_fetch_assoc($query_masuk);

        $data_labels[] = $nama_bulan_indo;
        $data_jumlah[] = (int)$data_keluar['total'];
        $data_masuk[] = (int)$data_masuk_row['total'];
    }
}

// ========== GRAFIK MATERIAL KELUAR PER CABANG ==========
$judul_cabang = $periode_card;
$filter_material_cabang = $material_filter > 0 ? " AND k.id_material = $material_filter" : "";
$query_cabang = mysqli_query($conn, "
    SELECT t.nama as cabang, COALESCE(SUM(k.qty), 0) as total
    FROM tujuan t
    LEFT JOIN transaksi_keluar_gudang k
        ON k.id_tujuan = t.id_tujuan
        AND k.tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'
        $filter_material_cabang
    GROUP BY t.id_tujuan
    ORDER BY total DESC, t.nama ASC
    LIMIT 10
");

$cabang_labels = [];
$cabang_data = [];
while ($row = mysqli_fetch_assoc($query_cabang)) {
    if ((int)$row['total'] > 0) {
        $cabang_labels[] = $row['cabang'];
        $cabang_data[] = (int)$row['total'];
    }
}

// ========== MATERIAL TERBANYAK KELUAR ==========
$query_material_top = mysqli_query($conn, "
    SELECT m.kode_material, m.nama, m.satuan, COALESCE(SUM(k.qty), 0) as total
    FROM material m
    LEFT JOIN transaksi_keluar_gudang k
        ON k.id_material = m.id_material
        AND k.tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'
    GROUP BY m.id_material
    HAVING total > 0
    ORDER BY total DESC
    LIMIT 5
");

$material_top = [];
while ($row = mysqli_fetch_assoc($query_material_top)) {
    $material_top[] = $row;
}

// ========== AKTIVITAS TERBARU TANPA TABEL BARU ==========
$aktivitas = [];

$query_aktivitas_masuk = mysqli_query($conn, "
    SELECT tm.tanggal, m.kode_material, m.nama, tm.qty
    FROM transaksi_masuk_gudang tm
    JOIN material m ON m.id_material = tm.id_material
    ORDER BY tm.tanggal DESC, tm.id_transaksi DESC
    LIMIT 2
");

while ($row = mysqli_fetch_assoc($query_aktivitas_masuk)) {
    $aktivitas[] = [
        'icon' => 'fas fa-arrow-down',
        'class' => 'activity-green',
        'judul' => 'Material masuk: ' . $row['kode_material'] . ' - ' . $row['nama'],
        'detail' => number_format($row['qty'], 0, ',', '.') . ' unit masuk ke gudang',
        'tanggal' => $row['tanggal']
    ];
}

$query_aktivitas_surat = mysqli_query($conn, "
    SELECT sj.no_surat, sj.created_at, t.nama as tujuan_nama
    FROM surat_jalan sj
    JOIN tujuan t ON t.id_tujuan = sj.id_tujuan
    ORDER BY sj.created_at DESC
    LIMIT 2
");

while ($row = mysqli_fetch_assoc($query_aktivitas_surat)) {
    $aktivitas[] = [
        'icon' => 'fas fa-truck',
        'class' => 'activity-orange',
        'judul' => 'Surat jalan dibuat: ' . $row['no_surat'],
        'detail' => 'Tujuan: ' . $row['tujuan_nama'],
        'tanggal' => $row['created_at']
    ];
}

$query_aktivitas_permintaan = mysqli_query($conn, "
    SELECT p.no_permintaan, p.created_at, t.nama as tujuan_nama
    FROM permintaan p
    JOIN tujuan t ON t.id_tujuan = p.id_tujuan
    ORDER BY p.created_at DESC
    LIMIT 2
");

while ($row = mysqli_fetch_assoc($query_aktivitas_permintaan)) {
    $aktivitas[] = [
        'icon' => 'fas fa-file-alt',
        'class' => 'activity-purple',
        'judul' => 'Permintaan: ' . $row['no_permintaan'],
        'detail' => 'Dari cabang: ' . $row['tujuan_nama'],
        'tanggal' => $row['created_at']
    ];
}

usort($aktivitas, function ($a, $b) {
    return strtotime($b['tanggal']) - strtotime($a['tanggal']);
});

$aktivitas = array_slice($aktivitas, 0, 4);

// ========== STATISTIK CARDS ==========
$query_stok = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah), 0) as total FROM stock_gudang");
$total_stok = (int)mysqli_fetch_assoc($query_stok)['total'];

$query_jml_cabang = mysqli_query($conn, "SELECT COUNT(*) as total FROM tujuan");
$total_cabang = (int)mysqli_fetch_assoc($query_jml_cabang)['total'];

$query_masuk_filter = mysqli_query($conn, "
    SELECT COALESCE(SUM(qty), 0) as total
    FROM transaksi_masuk_gudang
    WHERE tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'
");
$total_masuk_filter = (int)mysqli_fetch_assoc($query_masuk_filter)['total'];

$query_keluar_filter = mysqli_query($conn, "
    SELECT COALESCE(SUM(qty), 0) as total
    FROM transaksi_keluar_gudang
    WHERE tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'
");
$total_keluar_filter = (int)mysqli_fetch_assoc($query_keluar_filter)['total'];

$query_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM permintaan WHERE status = 'pending'");
$total_pending = (int)mysqli_fetch_assoc($query_pending)['total'];

$query_surat_draft = mysqli_query($conn, "SELECT COUNT(*) as total FROM surat_jalan WHERE status = 'draft'");
$total_surat_draft = (int)mysqli_fetch_assoc($query_surat_draft)['total'];

$query_stok_rendah = mysqli_query($conn, "SELECT COUNT(*) as total FROM stock_gudang WHERE jumlah <= 10");
$total_stok_rendah = (int)mysqli_fetch_assoc($query_stok_rendah)['total'];

$total_material = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM material"));
$total_user = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM login"));

// Judul grafik utama
if ($jenis_grafik == 'masuk') {
    $grafik_title = "Grafik Material Masuk";
} elseif ($jenis_grafik == 'perbandingan') {
    $grafik_title = "Grafik Perbandingan Material Masuk dan Keluar";
} else {
    $grafik_title = "Grafik Material Keluar";
}

if ($material_filter > 0 && $material_terpilih != '') {
    $grafik_title .= " - $material_terpilih";
}

$chart_cabang_height = max(300, count($cabang_labels) * 42);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Sistem Gudang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/animation.css">
    <link rel="stylesheet" href="assets/css/sidebar-fixed.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        .dashboard-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
            margin-bottom: 18px;
        }

        .dashboard-card .card-body {
            padding: 18px;
        }

        .section-title {
            font-weight: 800;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .section-title i {
            color: var(--primary);
        }

        .filter-card label {
            font-weight: 600;
            color: #374151;
            font-size: 13px;
        }

        .filter-card .form-control {
            border-radius: 9px;
            height: 42px;
            border-color: #d1d5db;
        }

        .btn-modern-primary {
            background: linear-gradient(135deg, #0d6efd, #2563eb);
            border: 0;
            border-radius: 9px;
            color: #fff;
            font-weight: 700;
            height: 42px;
            padding: 0 18px;
            box-shadow: 0 8px 18px rgba(13, 110, 253, .22);
        }

        .btn-modern-secondary {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            color: #374151;
            font-weight: 700;
            height: 42px;
            padding: 10px 18px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
            padding: 18px;
            height: 100%;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .09);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex: 0 0 52px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 900;
            line-height: 1.1;
        }

        .stat-label {
            font-weight: 700;
            color: #1f2937;
        }

        .stat-subtitle {
            font-size: 11px;
            color: var(--text-soft);
            margin-top: 3px;
        }

        .icon-blue {
            background: #dbeafe;
            color: #0d6efd;
        }

        .icon-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-orange {
            background: #ffedd5;
            color: #f97316;
        }

        .icon-teal {
            background: #cffafe;
            color: #0891b2;
        }

        .icon-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .icon-red {
            background: #fee2e2;
            color: #ef4444;
        }

        .number-blue {
            color: #0d6efd;
        }

        .number-green {
            color: #16a34a;
        }

        .number-orange {
            color: #f97316;
        }

        .number-teal {
            color: #0891b2;
        }

        .number-purple {
            color: #7c3aed;
        }

        .number-red {
            color: #ef4444;
        }

        .chart-header {
            padding: 16px 18px 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .chart-title {
            font-weight: 800;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .chart-title i.blue {
            color: #0d6efd;
        }

        .chart-title i.green {
            color: #16a34a;
        }

        .chart-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .material-filter-select {
            min-width: 220px;
            border-radius: 8px;
            font-size: 13px;
        }

        .filter-btn .btn {
            margin-left: 4px;
            border-radius: 8px;
            font-weight: 700;
            border: 1px solid #d1d5db;
        }

        .btn-filter-active {
            background: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;
        }

        .chart-wrap {
            position: relative;
            height: 310px;
            padding: 10px 12px 18px 12px;
        }

        #grafik-material {
            scroll-margin-top: 80px;
        }

        .empty-chart {
            min-height: 260px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-soft);
            text-align: center;
        }

        .quick-list,
        .activity-list,
        .top-material-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .quick-list li,
        .activity-list li,
        .top-material-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #eef2f7;
        }

        .quick-list li:last-child,
        .activity-list li:last-child,
        .top-material-list li:last-child {
            border-bottom: 0;
        }

        .quick-left,
        .activity-left,
        .material-left {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .mini-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 34px;
        }

        .quick-text,
        .activity-text,
        .material-text {
            min-width: 0;
        }

        .quick-text strong,
        .activity-text strong,
        .material-text strong {
            display: block;
            font-size: 13px;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 390px;
        }

        .activity-text small,
        .material-text small {
            color: var(--text-soft);
        }

        .badge-soft {
            border-radius: 999px;
            padding: 5px 10px;
            font-weight: 800;
            min-width: 34px;
            text-align: center;
        }

        .activity-green {
            background: #dcfce7;
            color: #16a34a;
        }

        .activity-orange {
            background: #ffedd5;
            color: #f97316;
        }

        .activity-purple {
            background: #ede9fe;
            color: #7c3aed;
        }

        .activity-blue {
            background: #dbeafe;
            color: #0d6efd;
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

        @keyframes dashboardFadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-animate .page-title,
        .dashboard-animate .dashboard-card,
        .dashboard-animate .stat-card {
            opacity: 0;
            animation: dashboardFadeUp 0.45s ease forwards;
        }

        .dashboard-animate .page-title {
            animation-delay: 0.03s;
        }

        .dashboard-animate .filter-card {
            animation-delay: 0.08s;
        }

        .dashboard-animate .stat-card {
            animation-delay: 0.14s;
        }

        .dashboard-animate .dashboard-card {
            animation-delay: 0.20s;
        }

        @media (prefers-reduced-motion: reduce) {

            .dashboard-animate .page-title,
            .dashboard-animate .dashboard-card,
            .dashboard-animate .stat-card {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
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
                    <div class="dropdown-item-text">
                        <small><strong>Level:</strong> <span class="badge badge-danger">Admin</span></small>
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
            <a href="<?= $current_page; ?>" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="index.php"><i class="fas fa-warehouse"></i> Stok Gudang</a>
            <a href="barang_masuk.php"><i class="fas fa-arrow-down"></i> Material Masuk</a>
            <a href="barang_keluar.php"><i class="fas fa-arrow-up"></i> Material Keluar</a>

            <hr>

            <div class="dropdown-btn">
                <i class="fas fa-database"></i> MASTER DATA <i class="fas fa-caret-down"></i>
            </div>
            <div class="dropdown-container">
                <a href="user.php"><i class="fas fa-users"></i> Daftar Pengguna</a>
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
                ?>
                    <a href="stok_cabang.php?id=<?= $c['id_tujuan']; ?>">
                        <i class="fas fa-building"></i> <?= htmlspecialchars($c['nama']); ?>
                    </a>
                <?php } ?>
            </div>

            <hr>

            <a href="daftar_permintaan.php"><i class="fas fa-file-alt"></i> Daftar Permintaan</a>
            <a href="surat_jalan.php"><i class="fas fa-truck"></i> Surat Jalan</a>
            <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>

            <hr>

            <a href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </div>

    <div class="content dashboard-animate">
        <div class="page-title">
            <h2>Dashboard</h2>
            <p>Ringkasan aktivitas gudang dan pergerakan material.</p>
        </div>

        <!-- Filter Periode -->
        <div class="dashboard-card filter-card">
            <div class="card-body">
                <div class="section-title"><i class="fas fa-calendar-alt"></i> Filter Periode</div>

                <form method="GET">
                    <input type="hidden" name="jenis_grafik" value="<?= $jenis_grafik; ?>">
                    <input type="hidden" name="filter" value="<?= $filter; ?>">
                    <input type="hidden" name="material_id" value="<?= $material_filter; ?>">

                    <div class="form-row align-items-end">
                        <div class="form-group col-lg-3 col-md-4 mb-2">
                            <label>Dari Tanggal</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai; ?>">
                        </div>

                        <div class="form-group col-lg-3 col-md-4 mb-2">
                            <label>Sampai Tanggal</label>
                            <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai; ?>">
                        </div>

                        <div class="form-group col-lg-3 col-md-4 mb-2">
                            <button type="submit" class="btn btn-modern-primary mr-2">Tampilkan</button>
                            <a href="<?= $current_page; ?>" class="btn btn-modern-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistik Cards -->
        <div class="row mb-3">
            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fas fa-warehouse"></i></div>
                    <div>
                        <div class="stat-number number-blue"><?= number_format($total_stok, 0, ',', '.'); ?></div>
                        <div class="stat-label">Total Stok Gudang</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fas fa-arrow-down"></i></div>
                    <div>
                        <div class="stat-number number-green"><?= number_format($total_masuk_filter, 0, ',', '.'); ?></div>
                        <div class="stat-label">Material Masuk</div>
                        <div class="stat-subtitle">Periode <?= $periode_card; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="stat-card">
                    <div class="stat-icon icon-orange"><i class="fas fa-arrow-up"></i></div>
                    <div>
                        <div class="stat-number number-orange"><?= number_format($total_keluar_filter, 0, ',', '.'); ?></div>
                        <div class="stat-label">Material Keluar</div>
                        <div class="stat-subtitle">Periode <?= $periode_card; ?></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="stat-card">
                    <div class="stat-icon icon-teal"><i class="fas fa-building"></i></div>
                    <div>
                        <div class="stat-number number-teal"><?= number_format($total_cabang, 0, ',', '.'); ?></div>
                        <div class="stat-label">Total Cabang</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="stat-card">
                    <div class="stat-icon icon-purple"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="stat-number number-purple"><?= number_format($total_pending, 0, ',', '.'); ?></div>
                        <div class="stat-label">Permintaan Pending</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3">
                <div class="stat-card">
                    <div class="stat-icon icon-red"><i class="fas fa-truck"></i></div>
                    <div>
                        <div class="stat-number number-red"><?= number_format($total_surat_draft, 0, ',', '.'); ?></div>
                        <div class="stat-label">Surat Jalan Belum Dikirim</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Utama -->
        <div class="row">
            <div class="col-xl-6 mb-3" id="grafik-material">
                <div class="dashboard-card h-100">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-chart-line blue"></i> <?= htmlspecialchars($grafik_title); ?>
                        </div>

                        <div class="chart-controls">
                            <select id="filterMaterial" class="form-control form-control-sm material-filter-select">
                                <option value="0">Semua Material</option>
                                <?php foreach ($material_list as $m): ?>
                                    <option value="<?= $m['id_material']; ?>" <?= $material_filter == $m['id_material'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($m['nama']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <div class="filter-btn">
                                <a href="?jenis_grafik=keluar&filter=<?= $filter; ?>&material_id=<?= $material_filter; ?>&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>#grafik-material"
                                    class="btn btn-sm btn-light <?= $jenis_grafik == 'keluar' ? 'btn-filter-active' : ''; ?>">
                                    Keluar
                                </a>

                                <a href="?jenis_grafik=masuk&filter=<?= $filter; ?>&material_id=<?= $material_filter; ?>&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>#grafik-material"
                                    class="btn btn-sm btn-light <?= $jenis_grafik == 'masuk' ? 'btn-filter-active' : ''; ?>">
                                    Masuk
                                </a>

                                <a href="?jenis_grafik=perbandingan&filter=<?= $filter; ?>&material_id=<?= $material_filter; ?>&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>#grafik-material"
                                    class="btn btn-sm btn-light <?= $jenis_grafik == 'perbandingan' ? 'btn-filter-active' : ''; ?>">
                                    Perbandingan
                                </a>
                            </div>

                            <div class="filter-btn">
                                <a href="?jenis_grafik=<?= $jenis_grafik; ?>&filter=hari&material_id=<?= $material_filter; ?>&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>#grafik-material"
                                    class="btn btn-sm btn-light <?= $filter == 'hari' ? 'btn-filter-active' : ''; ?>">
                                    Hari
                                </a>

                                <a href="?jenis_grafik=<?= $jenis_grafik; ?>&filter=minggu&material_id=<?= $material_filter; ?>&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>#grafik-material"
                                    class="btn btn-sm btn-light <?= $filter == 'minggu' ? 'btn-filter-active' : ''; ?>">
                                    Minggu
                                </a>

                                <a href="?jenis_grafik=<?= $jenis_grafik; ?>&filter=bulan&material_id=<?= $material_filter; ?>&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>#grafik-material"
                                    class="btn btn-sm btn-light <?= $filter == 'bulan' ? 'btn-filter-active' : ''; ?>">
                                    Bulan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="chart-wrap">
                        <canvas id="chartBarangKeluar"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 mb-3">
                <div class="dashboard-card h-100">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-chart-bar green"></i> Material Keluar per Tujuan/Cabang
                        </div>
                        <small class="text-muted"><?= $judul_cabang; ?></small>
                    </div>

                    <div class="chart-wrap" style="height: <?= $chart_cabang_height; ?>px;">
                        <canvas id="chartPerCabang"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian bawah -->
        <div class="row">
            <div class="col-xl-6 mb-3">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-history"></i> Aktivitas Admin Terbaru</div>

                        <?php if (count($aktivitas) > 0): ?>
                            <ul class="activity-list">
                                <?php foreach ($aktivitas as $item): ?>
                                    <li>
                                        <div class="activity-left">
                                            <div class="mini-icon <?= $item['class']; ?>">
                                                <i class="<?= $item['icon']; ?>"></i>
                                            </div>

                                            <div class="activity-text">
                                                <strong title="<?= htmlspecialchars($item['judul']); ?>">
                                                    <?= htmlspecialchars($item['judul']); ?>
                                                </strong>
                                                <small><?= htmlspecialchars($item['detail']); ?></small>
                                            </div>
                                        </div>

                                        <small class="text-muted"><?= date('d/m/Y', strtotime($item['tanggal'])); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">Belum ada aktivitas terbaru.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-3">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-list-check"></i> Ringkasan Cepat</div>

                        <ul class="quick-list">
                            <li>
                                <div class="quick-left">
                                    <div class="mini-icon icon-purple"><i class="fas fa-file-alt"></i></div>
                                    <div class="quick-text"><strong>Permintaan Pending</strong></div>
                                </div>
                                <span class="badge-soft icon-purple"><?= $total_pending; ?></span>
                            </li>

                            <li>
                                <div class="quick-left">
                                    <div class="mini-icon icon-red"><i class="fas fa-truck"></i></div>
                                    <div class="quick-text"><strong>Surat Jalan Draft</strong></div>
                                </div>
                                <span class="badge-soft icon-red"><?= $total_surat_draft; ?></span>
                            </li>

                            <li>
                                <div class="quick-left">
                                    <div class="mini-icon icon-orange"><i class="fas fa-exclamation-triangle"></i></div>
                                    <div class="quick-text"><strong>Stok ≤ 10</strong></div>
                                </div>
                                <span class="badge-soft icon-orange"><?= $total_stok_rendah; ?></span>
                            </li>

                            <li>
                                <div class="quick-left">
                                    <div class="mini-icon icon-teal"><i class="fas fa-boxes"></i></div>
                                    <div class="quick-text"><strong>Total Material</strong></div>
                                </div>
                                <span class="badge-soft icon-teal"><?= $total_material; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 mb-3">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-star"></i> 5 Material Terbanyak Keluar</div>

                        <?php if (count($material_top) > 0): ?>
                            <ul class="top-material-list">
                                <?php foreach ($material_top as $m): ?>
                                    <li>
                                        <div class="material-left">
                                            <div class="mini-icon activity-blue"><i class="fas fa-box"></i></div>

                                            <div class="material-text">
                                                <strong title="<?= htmlspecialchars($m['nama']); ?>">
                                                    <?= htmlspecialchars($m['kode_material']); ?> - <?= htmlspecialchars($m['nama']); ?>
                                                </strong>
                                                <small><?= htmlspecialchars($m['satuan']); ?></small>
                                            </div>
                                        </div>

                                        <span class="badge-soft icon-blue">
                                            <?= number_format($m['total'], 0, ',', '.'); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">Belum ada material keluar pada periode ini.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const lineLabels = <?= json_encode($data_labels); ?>;
        const lineData = <?= json_encode($data_jumlah); ?>;
        const lineDataMasuk = <?= json_encode($data_masuk); ?>;
        const jenisGrafik = "<?= $jenis_grafik; ?>";
        const cabangLabels = <?= json_encode($cabang_labels); ?>;
        const cabangData = <?= json_encode($cabang_data); ?>;

        // Plugin sederhana untuk menampilkan angka di ujung bar horizontal
        const valueLabelPlugin = {
            id: 'valueLabelPlugin',
            afterDatasetsDraw(chart) {
                const {
                    ctx,
                    chartArea
                } = chart;
                const dataset = chart.data.datasets[0];
                const meta = chart.getDatasetMeta(0);

                ctx.save();
                ctx.font = '700 12px Arial';
                ctx.fillStyle = '#111827';
                ctx.textBaseline = 'middle';

                meta.data.forEach((bar, index) => {
                    const value = dataset.data[index];
                    if (value === null || value === undefined) return;

                    const xPos = chart.scales.x.getPixelForValue(value);
                    const yPos = bar.y;
                    const label = Number(value).toLocaleString('id-ID');
                    const labelX = Math.min(xPos + 8, chartArea.right - 4);

                    ctx.fillText(label, labelX, yPos);
                });

                ctx.restore();
            }
        };

        // Grafik Material Masuk / Keluar / Perbandingan
        const ctx1 = document.getElementById('chartBarangKeluar').getContext('2d');

        let chartDatasets = [];

        if (jenisGrafik === 'masuk') {
            chartDatasets = [{
                label: 'Jumlah Material Masuk',
                data: lineDataMasuk,
                backgroundColor: 'rgba(22, 163, 74, 0.12)',
                borderColor: '#16a34a',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#16a34a',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }];
        } else if (jenisGrafik === 'perbandingan') {
            chartDatasets = [{
                    label: 'Jumlah Material Masuk',
                    data: lineDataMasuk,
                    backgroundColor: 'rgba(22, 163, 74, 0.08)',
                    borderColor: '#16a34a',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: false,
                    pointBackgroundColor: '#16a34a',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Jumlah Material Keluar',
                    data: lineData,
                    backgroundColor: 'rgba(13, 110, 253, 0.08)',
                    borderColor: '#0d6efd',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: false,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }
            ];
        } else {
            chartDatasets = [{
                label: 'Jumlah Material Keluar',
                data: lineData,
                backgroundColor: 'rgba(13, 110, 253, 0.12)',
                borderColor: '#0d6efd',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#0d6efd',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }];
        }

        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: lineLabels,
                datasets: chartDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return Number(context.raw).toLocaleString('id-ID') + ' unit';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah'
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, .18)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Periode'
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, .16)'
                        }
                    }
                }
            }
        });

        // Grafik Material Keluar per Cabang
        const ctx2 = document.getElementById('chartPerCabang').getContext('2d');

        if (cabangLabels.length > 0) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: cabangLabels,
                    datasets: [{
                        label: 'Jumlah Material Keluar',
                        data: cabangData,
                        backgroundColor: '#22c55e',
                        borderColor: '#16a34a',
                        borderWidth: 1,
                        borderRadius: 8,
                        minBarLength: 8,
                        barThickness: 18
                    }]
                },
                plugins: [valueLabelPlugin],
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            right: 45
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return Number(context.raw).toLocaleString('id-ID') + ' unit';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah'
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, .18)'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: false
                            }
                        }
                    }
                }
            });
        } else {
            ctx2.canvas.parentNode.innerHTML = '<div class="empty-chart"><div><i class="fas fa-info-circle mb-2"></i><br>Belum ada data material keluar untuk periode ini.</div></div>';
        }

        // Filter material tetap kembali ke bagian grafik
        $('#filterMaterial').on('change', function() {
            var material_id = $(this).val();
            window.location.href = '?jenis_grafik=<?= $jenis_grafik; ?>&filter=<?= $filter; ?>&material_id=' + material_id + '&tgl_mulai=<?= $tgl_mulai; ?>&tgl_selesai=<?= $tgl_selesai; ?>#grafik-material';
        });

        // Dropdown sidebar dengan state tersimpan
        var dropdown = document.getElementsByClassName("dropdown-btn");

        function saveDropdownState(index, isOpen) {
            localStorage.setItem('dropdownState_' + index, isOpen ? 'open' : 'closed');
        }

        function loadDropdownState(index) {
            return localStorage.getItem('dropdownState_' + index) === 'open';
        }

        for (var i = 0; i < dropdown.length; i++) {
            var dropdownContent = dropdown[i].nextElementSibling;

            if (loadDropdownState(i)) {
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
            localStorage.setItem(
                'sidebarToggled',
                document.querySelector('.sidebar').classList.contains('toggled') ? 'true' : 'false'
            );
        });

        if (localStorage.getItem('sidebarToggled') === 'true') {
            document.querySelector('.sidebar').classList.add('toggled');
        }
    </script>
</body>

</html>