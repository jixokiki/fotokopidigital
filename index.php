<?php
session_start();
include "db.php";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Form Pemesanan Cetak Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 bg-white p-4 rounded shadow">
                <h2 class="mb-4">Form Pemesanan Cetak Dokumen</h2>
                <form action="upload.php" method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>No HP</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Alamat</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Jenis Cetak</label>
                        <select name="print_type" class="form-control">
                            <option value="hitam_putih">Hitam Putih</option>
                            <option value="warna">Warna</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Ukuran Kertas</label>
                        <select name="paper_size" class="form-control">
                            <option value="A4">A4</option>
                            <option value="F4">F4</option>
                            <option value="A3">A3</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Jenis Jilid</label>
                        <select name="binding" class="form-control">
                            <option value="tidak">Tidak</option>
                            <option value="spiral">Spiral</option>
                            <option value="jilid_lipat">Jilid Lipat</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Antar-Jemput</label><br>
                        <input type="checkbox" name="delivery" value="1"> Ya, saya ingin antar-jemput
                    </div>
                    <div class="col-md-6">
                        <label>Metode Pembayaran</label>
                        <select name="payment_method" class="form-control">
                            <option value="qris">QRIS</option>
                            <option value="e-wallet">E-Wallet</option>
                            <option value="cod">COD</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Upload File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Kirim Pesanan</button>
                    </div>
                </form>
            </div>
            <div class="col-md-10 bg-white p-4 rounded shadow">
                <h2 class="mb-4">🔍 Berkas</h2>
                <p>cari file punya anda disini...</p>
                <!-- 🔍 Form Pencarian -->
                <form method="GET" class="row g-2 mb-4">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control"
                            placeholder="Masukkan Nama / Email / No HP..." value="<?= $_GET['search'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-100">Cari</button>
                    </div>
                </form>

                <?php
$o = null;

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $keyword = $_GET['search'];
    $sql = "SELECT * FROM orders 
            JOIN users ON orders.user_id = users.id 
            WHERE users.name LIKE '%$keyword%' 
               OR users.email LIKE '%$keyword%' 
               OR users.phone LIKE '%$keyword%' 
            ORDER BY orders.created_at DESC 
            LIMIT 1";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $o = $result->fetch_assoc();
    }
} elseif (isset($_GET['show_updated']) && isset($_SESSION['updated_order'])) {
    $o = $_SESSION['updated_order'];
}

if ($o):
$delivery = $o['delivery'] == '1' ? 'Ya' : 'Tidak';
$harga = isset($o['total_price']) ? number_format($o['total_price'], 0, ',', '.') : '-';
?>

                <hr>
                <h4 class="mt-4">
                    <?= isset($_GET['search']) ? '🔍 Hasil Pencarian' : '✅ Pesanan Terbaru yang Diperbarui' ?></h4>
                <table class="table table-bordered mt-3">
                    <tr>
                        <th>Nama</th>
                        <td><?= $o['name'] ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $o['email'] ?></td>
                    </tr>
                    <tr>
                        <th>File</th>
                        <td><?= $o['file_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Cetak</th>
                        <td><?= $o['print_type'] ?></td>
                    </tr>
                    <tr>
                        <th>Ukuran Kertas</th>
                        <td><?= $o['paper_size'] ?></td>
                    </tr>
                    <tr>
                        <th>Jilid</th>
                        <td><?= $o['binding'] ?></td>
                    </tr>
                    <tr>
                        <th>Antar</th>
                        <td><?= $delivery ?></td>
                    </tr>
                    <tr>
                        <th>Pembayaran</th>
                        <td><?= $o['payment_method'] ?></td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>Rp<?= $harga ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= $o['status'] ?></td>
                    </tr>
                    <tr>
                        <th>Waktu</th>
                        <td><?= $o['created_at'] ?></td>
                    </tr>
                </table>

                <?php if (!isset($_GET['search'])) unset($_SESSION['updated_order']); endif; ?>

            </div>
        </div>

</body>

</html>