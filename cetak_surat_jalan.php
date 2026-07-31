<?php
require 'function.php';

$id_surat = $_GET['id'];

// Ambil data pengaturan
$pengaturan = mysqli_query($conn, "SELECT * FROM pengaturan WHERE id = 1");
$perusahaan = mysqli_fetch_assoc($pengaturan);

// Ambil data surat jalan dengan detail multi material
$query = mysqli_query($conn, "
    SELECT sj.*, t.nama as tujuan_nama,
        GROUP_CONCAT(CONCAT(m.kode_material, ' - ', m.nama, ' (', sjd.jumlah_kirim, ' ', m.satuan, ')') SEPARATOR '<br>') as detail_material,
        GROUP_CONCAT(sjd.jumlah_kirim) as total_qty
    FROM surat_jalan sj
    JOIN tujuan t ON t.id_tujuan = sj.id_tujuan
    LEFT JOIN surat_jalan_detail sjd ON sjd.id_surat = sj.id_surat
    LEFT JOIN material m ON m.id_material = sjd.id_material
    WHERE sj.id_surat = '$id_surat'
    GROUP BY sj.id_surat
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Surat jalan tidak ditemukan!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Surat Jalan - <?= $data['no_surat']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            padding: 20px;
            font-family: 'Times New Roman', Times, serif;
        }

        .kop {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop h2,
        .kop h3 {
            margin: 0;
        }

        .kop p {
            margin: 0;
        }

        .info {
            margin-bottom: 20px;
        }

        .table-detail {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table-detail th,
        .table-detail td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table-detail th {
            background-color: #f2f2f2;
        }

        .ttd {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .ttd div {
            text-align: center;
            width: 200px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }

        .btn-print {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Cetak / Print</button>
        <a href="surat_jalan.php" class="btn btn-secondary">Kembali</a>
        <hr>
    </div>

    <div class="kop">
        <h2><?= htmlspecialchars($perusahaan['nama_perusahaan']); ?></h2>
        <h3>SURAT JALAN</h3>
        <p><?= nl2br(htmlspecialchars($perusahaan['alamat'])); ?></p>
        <p>Telp. <?= htmlspecialchars($perusahaan['telepon']); ?></p>
    </div>

    <div class="info">
        <table width="100%">
            <tr>
                <td width="20%"><strong>No. Surat Jalan</strong>
                <td width="5%">:
                <td><strong><?= $data['no_surat']; ?></strong>
            </tr>
            <tr>
                <td><strong>Tanggal Surat</strong>
                <td>:
                <td><?= tgl_panjang($data['tanggal_surat']); ?>
            </tr>
            <tr>
                <td><strong>Tanggal Kirim</strong>
                <td>:
                <td><?= tgl_panjang($data['tanggal_kirim']); ?>
            </tr>
            <tr>
                <td><strong>Tujuan</strong>
                <td>:
                <td><?= htmlspecialchars($data['tujuan_nama']); ?>
            </tr>
        </table>
    </div>

    <table class="table-detail">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Material</th>
                <th>Nama Material</th>
                <th>Satuan</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil detail per item untuk ditampilkan di tabel
            $query_detail = mysqli_query($conn, "
                SELECT m.kode_material, m.nama, m.satuan, sjd.jumlah_kirim
                FROM surat_jalan_detail sjd
                JOIN material m ON m.id_material = sjd.id_material
                WHERE sjd.id_surat = '$id_surat'
            ");
            $no = 1;
            while ($detail = mysqli_fetch_assoc($query_detail)) {
            ?>
                <tr>
                    <td style="text-align: center;"><?= $no++; ?></td>
                    <td><?= $detail['kode_material']; ?></td>
                    <td><?= $detail['nama']; ?></td>
                    <td><?= $detail['satuan']; ?></td>
                    <td style="text-align: center;"><?= $detail['jumlah_kirim']; ?></td>
                    <td><?= $data['catatan'] ?? '-'; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="ttd">
        <div>
            <p>Dibuat oleh,</p><br><br>
            <p><u>Admin Gudang</u></p>
        </div>
        <div>
            <p>Mengetahui,</p><br><br>
            <p><u>Penerima</u></p>
        </div>
    </div>

    <div class="footer">
        <small>Surat Jalan ini adalah bukti resmi pengiriman Material.</small>
    </div>

    <script>
        window.print();
    </script>
</body>

</html>