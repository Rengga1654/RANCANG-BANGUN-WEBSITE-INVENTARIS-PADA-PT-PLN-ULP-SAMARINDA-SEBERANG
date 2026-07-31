<?php
// notifikasi.php
function setNotifikasi($status, $pesan)
{
    $_SESSION['notifikasi'] = [
        'status' => $status,
        'pesan' => $pesan
    ];
}

function tampilkanNotifikasi()
{
    if (isset($_SESSION['notifikasi'])) {
        $status = $_SESSION['notifikasi']['status'];
        $pesan = $_SESSION['notifikasi']['pesan'];
        unset($_SESSION['notifikasi']);
?>
        <script>
            Swal.fire({
                icon: '<?= $status; ?>',
                title: '<?= ($status == 'success') ? 'Berhasil!' : 'Gagal!'; ?>',
                text: '<?= $pesan; ?>',
                confirmButtonColor: '<?= ($status == 'success') ? '#28a745' : '#dc3545'; ?>',
                confirmButtonText: 'OK'
            });
        </script>
<?php
    }
}
?>