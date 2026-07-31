<?php
require 'function.php';
require 'cek.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
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

$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Nilai Pemakaian Harian - <?= $nama_bulan[$bulan] . ' ' . $tahun; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }

        .table th,
        .table td {
            font-size: 12px;
            padding: 8px;
            vertical-align: middle;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #f8f9fc;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-3">
        <h4>Laporan Nilai Pemakaian Material</h4>
        <h5>Bulan: <?= $nama_bulan[$bulan] . ' ' . $tahun; ?></h5>

        <form method="GET" class="form-inline mb-3 no-print">
            <select name="bulan" class="form-control mr-2">
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
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th class="text-right">Total Nilai Pemakaian (Rp)</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $grand_total = 0;

                    for ($tgl = 1; $tgl <= $jumlah_hari; $tgl++) {
                        $tanggal = "$tahun-$bulan-" . str_pad($tgl, 2, '0', STR_PAD_LEFT);

                        $query = mysqli_query($conn, "
                        SELECT SUM(k.qty * m.harga) as total_nilai
                        FROM transaksi_keluar_gudang k
                        JOIN material m ON m.id_material = k.id_material
                        WHERE k.tanggal = '$tanggal'
                    ");
                        $data = mysqli_fetch_assoc($query);
                        $total_nilai = $data['total_nilai'] ?? 0;
                        $grand_total += $total_nilai;

                        // Tampilkan hanya jika ada transaksi (total_nilai > 0)
                        if ($total_nilai > 0):
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= tgl_panjang($tanggal); ?></td>
                                <td class="text-right"><?= number_format($total_nilai, 0, ',', '.'); ?></td>
                                <td>-</td>
                            </tr>
                    <?php
                        endif;
                    }
                    ?>
                </tbody>
                <tfoot class="total-row">
                    <tr>
                        <th colspan="2" class="text-right">GRAND TOTAL</th>
                        <th class="text-right"><?= number_format($grand_total, 0, ',', '.'); ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row mt-3 no-print">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Menampilkan total nilai pemakaian (Qty × Harga Satuan) per tanggal.
                </div>
            </div>
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