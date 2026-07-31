<?php
require 'function.php';
require 'cek.php';

if (isset($_POST['verifikasi'])) {
    $id_permintaan = $_POST['id_permintaan'];
    $id_tujuan = $_POST['id_tujuan'];
    $jumlah_diterima = $_POST['jumlah_diterima'];
    $tanggal_terima = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['tanggal_terima'])));
    $status_verifikasi = $_POST['status_verifikasi'];
    $keterangan = $_POST['keterangan'] ?? '';

    // Ambil data permintaan (header)
    $query_permintaan = mysqli_query($conn, "SELECT * FROM permintaan WHERE id_permintaan = '$id_permintaan'");
    $permintaan = mysqli_fetch_assoc($query_permintaan);
    $catatan_lama = $permintaan['catatan'] ?? '';

    // Ambil detail permintaan (semua material)
    $query_detail = mysqli_query($conn, "
        SELECT pd.*, m.kode_material, m.nama, m.satuan, m.harga
        FROM permintaan_detail pd
        JOIN material m ON m.id_material = pd.id_material
        WHERE pd.id_permintaan = '$id_permintaan'
    ");

    $total_dipesan = 0;
    $detail_material = [];
    while ($row = mysqli_fetch_assoc($query_detail)) {
        $total_dipesan += $row['jumlah'];
        $detail_material[] = $row;
    }

    // Status verifikasi dipaksa mengikuti jumlah diterima
    $jumlah_diterima = (int)$jumlah_diterima;
    $total_dipesan = (int)$total_dipesan;

    if ($jumlah_diterima < $total_dipesan) {
        $status_verifikasi = 'kurang';
    } elseif ($jumlah_diterima > $total_dipesan) {
        $status_verifikasi = 'lebih';
    } else {
        $status_verifikasi = 'sesuai';
    }

    // Validasi user cabang
    if ($_SESSION['level'] == 'user_cabang' && $_SESSION['cabang_id'] != $id_tujuan) {
        setNotifikasi('error', 'Anda tidak memiliki akses!');
        header("Location: permintaan_cabang.php?id=" . $_SESSION['cabang_id']);
        exit;
    }

    // Mulai transaksi
    mysqli_begin_transaction($conn);

    try {
        // Simpan verifikasi
        mysqli_query($conn, "INSERT INTO verifikasi_penerimaan (id_permintaan, id_tujuan, tanggal_terima, jumlah_diterima, status_verifikasi, keterangan) 
                            VALUES ('$id_permintaan', '$id_tujuan', '$tanggal_terima', '$jumlah_diterima', '$status_verifikasi', '$keterangan')");

        if ($status_verifikasi == 'sesuai' && $jumlah_diterima == $total_dipesan) {
            // ========== SESUAI ==========
            mysqli_query($conn, "UPDATE permintaan SET status = 'selesai' WHERE id_permintaan = '$id_permintaan'");
            mysqli_commit($conn);
            setNotifikasi('success', 'Verifikasi berhasil! Material sesuai dengan pesanan.');
        } else {
            // ========== TIDAK SESUAI ==========
            // Untuk setiap material, kembalikan stok
            foreach ($detail_material as $item) {
                $id_material = $item['id_material'];
                $jumlah = $item['jumlah'];

                // Kembalikan stok cabang
                mysqli_query($conn, "UPDATE stock_tujuan SET jumlah = jumlah - $jumlah 
                                    WHERE id_material='$id_material' AND id_tujuan='$id_tujuan'");

                // Kembalikan stok gudang
                mysqli_query($conn, "UPDATE stock_gudang SET jumlah = jumlah + $jumlah 
                                    WHERE id_material = '$id_material'");

                // Hapus transaksi keluar gudang terkait
                mysqli_query($conn, "DELETE FROM transaksi_keluar_gudang 
                                    WHERE id_material='$id_material' AND id_tujuan='$id_tujuan' 
                                    ORDER BY id_transaksi DESC LIMIT 1");

                // Hapus transaksi masuk tujuan terkait
                mysqli_query($conn, "DELETE FROM transaksi_masuk_tujuan 
                                    WHERE id_material='$id_material' AND id_tujuan='$id_tujuan' 
                                    ORDER BY id_transaksi DESC LIMIT 1");
            }

            // Update status permintaan
            $detail_verifikasi = "\n[Verifikasi] Status: $status_verifikasi | Total Dipesan: $total_dipesan | Diterima: $jumlah_diterima | Keterangan: $keterangan";
            $catatan_baru = $catatan_lama . $detail_verifikasi;

            mysqli_query($conn, "UPDATE permintaan SET status = 'perlu_perbaikan', catatan = '$catatan_baru' 
                                WHERE id_permintaan = '$id_permintaan'");

            mysqli_commit($conn);

            $pesan = "Material tidak sesuai! Total Dipesan: $total_dipesan, Diterima: $jumlah_diterima. Status: $status_verifikasi. $keterangan";
            setNotifikasi('error', $pesan);
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        setNotifikasi('error', 'Gagal verifikasi penerimaan!');
    }

    header("Location: permintaan_cabang.php?id=$id_tujuan");
    exit;
}
?>