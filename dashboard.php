<?php
// ✅ FILE: dashboard.php - Admin Dashboard untuk Memantau Pesanan & Stok
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// Ambil data pesanan yang belum selesai
$sql_orders = "SELECT orders.id, users.name, users.email, orders.file_name, orders.print_type, orders.paper_size, orders.binding, orders.delivery, orders.payment_method, orders.total_price, orders.status, orders.created_at 
               FROM orders
               JOIN users ON orders.user_id = users.id
               WHERE orders.status != 'selesai'
               ORDER BY orders.created_at DESC";
$result_orders = $conn->query($sql_orders);

// Ambil data stok
$sql_stok = "SELECT * FROM stok";
$result_stok = $conn->query($sql_stok);

// // Proses update stok manual (bukan AJAX)
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['update_stok'])) {
//         $stok_id = $_POST['stok_id'];
//         $jumlah = $_POST['jumlah'];
//         $conn->query("UPDATE stok SET jumlah = jumlah + $jumlah WHERE id = '$stok_id'");
//     }
// }

// if (isset($_POST['tambah_barang'])) {
//     $item = $_POST['item'];
//     $jumlah = $_POST['jumlah'];
//     $conn->query("INSERT INTO stok (item, jumlah) VALUES ('$item', '$jumlah')");
//     header("Location: dashboard.php");
//     exit();
// }

// Proses tambah stok
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_barang'])) {
        $item = $_POST['item'];
        $jumlah = $_POST['jumlah'];
        $conn->query("INSERT INTO stok (item, jumlah) VALUES ('$item', '$jumlah')");
        $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', '$jumlah', 'tambah')");
        exit(json_encode(['success' => true]));
    }

    if (isset($_POST['hapus_stok'])) {
        $stok_id = $_POST['stok_id'];
        $item_row = $conn->query("SELECT item FROM stok WHERE id = '$stok_id'")->fetch_assoc();
        $item = $item_row['item'] ?? '-';
        $conn->query("DELETE FROM stok WHERE id = '$stok_id'");
        $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', 0, 'hapus')");
        exit(json_encode(['success' => true]));
    }

    if (isset($_POST['update_stok'])) {
        $stok_id = $_POST['stok_id'];
        $jumlah = $_POST['jumlah'];
        $conn->query("UPDATE stok SET jumlah = jumlah + $jumlah WHERE id = '$stok_id'");
        $item_row = $conn->query("SELECT item FROM stok WHERE id = '$stok_id'")->fetch_assoc();
        $item = $item_row['item'] ?? '-';
        $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', '$jumlah', 'update')");
        exit(json_encode(['success' => true]));
    }
}

//JANGAN DIHAPUS YAA IKI
// // Data stok dan riwayat
// $stok_result = $conn->query("SELECT * FROM stok ORDER BY id DESC");
// $riwayat_result = $conn->query("SELECT * FROM riwayat_stok ORDER BY created_at DESC LIMIT 10");

$stok_result = $conn->query("SELECT * FROM stok ORDER BY id DESC");

$filter_query = "SELECT * FROM riwayat_stok WHERE 1";
if (!empty($_GET['filter_item'])) {
    $filter_item = $_GET['filter_item'];
    $filter_query .= " AND item LIKE '%$filter_item%'";
}
if (!empty($_GET['filter_tanggal'])) {
    $filter_tanggal = $_GET['filter_tanggal'];
    $filter_query .= " AND DATE(created_at) = '$filter_tanggal'";
}
$total_riwayat = $conn->query($filter_query)->num_rows;
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$filter_query .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$riwayat_result = $conn->query($filter_query);

function buildExportLink($format) {
    $params = http_build_query($_GET);
    return "export_riwayat.php?$params&format=$format";
}
?>




<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin Fotokopi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Dashboard Admin Fotokopi</h2>
        <a href="logout.php" class="btn btn-danger mb-3">Logout</a>

        <h3>📋 Pesanan Masuk</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>File</th>
                    <th>Jenis Cetak</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($order = $result_orders->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $order['name'] ?></td>
                    <td><?= $order['file_name'] ?></td>
                    <td><?= $order['print_type'] ?></td>
                    <td><?= $order['status'] ?></td>
                    <td>
                        <form class="update-status-form">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <select name="status" class="form-control mb-2">
                                <option value="diproses" <?= $order['status'] == 'diproses' ? 'selected' : '' ?>>
                                    Diproses</option>
                                <option value="selesai" <?= $order['status'] == 'selesai' ? 'selected' : '' ?>>Selesai
                                </option>
                                <option value="diantar" <?= $order['status'] == 'diantar' ? 'selected' : '' ?>>Diantar
                                </option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Form Tambah Barang -->
        <div class="card p-3 mb-4 shadow-sm">
            <h5 class="mb-3">➕ Tambah Barang ke Stok</h5>
            <form id="form-tambah" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <input type="text" name="item" class="form-control" placeholder="Contoh: Kertas A4" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tambah</button>
                </div>
            </form>
        </div>

        <!-- Tabel Stok -->
        <h4>📦 Daftar Stok Barang</h4>
        <table class="table table-striped" id="stok-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Jumlah</th>
                    <th>Tambah</th>
                    <th>Hapus</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($stok = $stok_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $stok['item'] ?></td>
                    <td><?= $stok['jumlah'] ?></td>
                    <td>
                        <form class="form-update d-flex gap-2">
                            <input type="hidden" name="stok_id" value="<?= $stok['id'] ?>">
                            <input type="number" name="jumlah" class="form-control" placeholder="+Jumlah" required>
                            <button type="submit" class="btn btn-success btn-sm">+</button>
                        </form>
                    </td>
                    <td>
                        <form class="form-hapus">
                            <input type="hidden" name="stok_id" value="<?= $stok['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h4 class="mt-5">🕘 Riwayat Perubahan Stok</h4>
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <input type="text" name="filter_item" class="form-control" placeholder="Cari berdasarkan item..."
                    value="<?= $_GET['filter_item'] ?? '' ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="filter_tanggal" class="form-control"
                    value="<?= $_GET['filter_tanggal'] ?? '' ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="dashboard.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
            <div class="col-md-2">
                <div class="btn-group w-100">
                    <a href="<?= buildExportLink('pdf') ?>" class="btn btn-outline-danger">PDF</a>
                    <a href="<?= buildExportLink('excel') ?>" class="btn btn-outline-success">Excel</a>
                    <a href="<?= buildExportLink('word') ?>" class="btn btn-outline-primary">Word</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>Item</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                    <th>Admin</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($log = $riwayat_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $log['created_at'] ?></td>
                    <td><?= $log['item'] ?></td>
                    <td><?= $log['jumlah'] ?></td>
                    <td><span class="badge bg-secondary"><?= strtoupper($log['aksi']) ?></span></td>
                    <td><?= $log['admin'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php
            $total_pages = ceil($total_riwayat / $limit);
            for ($i = 1; $i <= $total_pages; $i++):
                $active = $i == $page ? 'active' : '';
                $params = $_GET;
                $params['page'] = $i;
                $url = '?' . http_build_query($params);
            ?>
                <li class="page-item <?= $active ?>">
                    <a class="page-link" href="<?= $url ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- ✅ AJAX Status Update -->
    <script>
    $('.update-status-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const order_id = form.find('input[name="order_id"]').val();
        const status = form.find('select[name="status"]').val();

        $.ajax({
            type: 'POST',
            url: 'update_status.php',
            data: {
                order_id: order_id,
                status: status
            },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.success) {
                    alert('Status berhasil diperbarui!');
                    // window.open('index.php?show_updated=true', '_blank');
                    // ✅ Menampilkan data langsung di halaman form (tanpa session)
                    // const query = new URLSearchParams(res.order).toString();
                    // window.open('index.php?show_updated=true&' + query, '_blank');

                } else {
                    alert('Gagal memuat data pesanan!');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengirim data!');
            }
        });
    });
    // Tambah Barang
    $('#form-tambah').on('submit', function(e) {
        e.preventDefault();
        $.post('dashboard.php', $(this).serialize() + '&tambah_barang=1', function(res) {
            location.reload();
        });
    });

    // Update Stok
    $('.form-update').on('submit', function(e) {
        e.preventDefault();
        $.post('dashboard.php', $(this).serialize() + '&update_stok=1', function(res) {
            location.reload();
        });
    });

    // Hapus Stok
    // $('.form-hapus').on('submit', function(e) {
    //     e.preventDefault();
    //     $.post('dashboard.php', $(this).serialize() + '&hapus_stok=1', function(res) {
    //         location.reload();
    //     });
    // });

    $('.form-hapus').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Yakin ingin menghapus barang ini?')) {
            $.post('dashboard.php', $(this).serialize() + '&hapus_stok=1', function(res) {
                location.reload();
            });
        }
    });

    $('.btn-group a').on('click', function() {
        $('body').append(
            `<div id="overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:#00000077;z-index:9999;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;">Mengekspor... Mohon tunggu</div>`
        );
        setTimeout(() => {
            $('#overlay').remove();
        }, 6000); // atau hilangkan setelah file berhasil
    });
    </script>
</body>

</html>