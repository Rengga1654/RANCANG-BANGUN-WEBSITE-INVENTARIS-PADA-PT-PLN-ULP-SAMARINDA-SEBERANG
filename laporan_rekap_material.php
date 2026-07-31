<?php
require 'function.php';
require 'cek.php';

// Filter bulan dan tahun (opsional)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$nama_bulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

// Bangun WHERE clause
$where = "";
if (!empty($bulan)) {
    $where = "WHERE MONTH(k.tanggal) = '$bulan' AND YEAR(k.tanggal) = '$tahun'";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Rekap Material Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="container-fluid mt-4">
        <h2>Rekap Material Keluar</h2>
        <h5>
            <?php if (!empty($bulan)): ?>
                Periode: <?= $nama_bulan[$bulan] . ' ' . $tahun; ?>
            <?php else: ?>
                Semua Data
            <?php endif; ?>
        </h5>

        <!-- Filter -->
        <form method="GET" class="form-inline mb-4 no-print">
            <select name="bulan" class="form-control mr-2">
                <option value="">-- Semua Bulan --</option>
                <?php foreach ($nama_bulan as $key => $nama): ?>
                    <option value="<?= $key; ?>" <?= $bulan == $key ? 'selected' : ''; ?>><?= $nama; ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tahun" class="form-control mr-2">
                <?php for ($y = 2024; $y <= 2030; $y++): ?>
                    <option value="<?= $y; ?>" <?= $tahun == $y ? 'selected' : ''; ?>><?= $y; ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <button type="button" class="btn btn-success ml-2" onclick="window.print()">Cetak</button>
            <a href="laporan_rekap_material.php" class="btn btn-secondary ml-2">Reset</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable">
                <thead class="bg-primary text-white">
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
                    $no = 1;
                    $query = mysqli_query($conn, "
                    SELECT 
                        m.kode_material,
                        m.nama,
                        m.satuan,
                        m.harga,
                        COALESCE(SUM(k.qty), 0) as total_qty
                    FROM material m
                    LEFT JOIN transaksi_keluar_gudang k ON k.id_material = m.id_material
                    $where
                    GROUP BY m.id_material
                    ORDER BY m.id_material ASC
                ");

                    $grand_total_harga = 0;
                    $grand_total_qty = 0;

                    while ($row = mysqli_fetch_assoc($query)):
                        $total_qty = $row['total_qty'];
                        $total_harga = $total_qty * $row['harga'];
                        $grand_total_qty += $total_qty;
                        $grand_total_harga += $total_harga;

                        // Tampilkan semua material (bisa difilter nanti)
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
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
                        <th colspan="5" class="text-right">GRAND TOTAL</th>
                        <th class="text-right"><?= number_format($grand_total_qty, 0, ',', '.'); ?></th>
                        <th class="text-right"><?= number_format($grand_total_harga, 0, ',', '.'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "pageLength": 25,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Indonesian.json"
                }
            });
        });
    </script>
</body>

</html>