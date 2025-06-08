<?php
ob_start(); // Cegah output HTML/notice header
// ✅ FILE: dashboard.php - Admin Dashboard untuk Memantau Pesanan & Stok
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

include 'db.php';

// $item_filter = $_GET['item'] ?? '';
// $date_filter = $_GET['tanggal'] ?? '';

// $query = "SELECT * FROM log_transaksi WHERE 1";
// if (!empty($item_filter)) {
//     $query .= " AND item LIKE '%$item_filter%'";
// }
// if (!empty($date_filter)) {
//     $query .= " AND DATE(tanggal) = '$date_filter'";
// }
// $query .= " ORDER BY tanggal DESC";
// $result = $conn->query($query);

$item_filter = $_GET['item'] ?? '';
$date_filter = $_GET['tanggal'] ?? '';

$query = "SELECT * FROM log_transaksi WHERE 1";
if (!empty($item_filter)) {
    $query .= " AND item LIKE '%$item_filter%'";
}
if (!empty($date_filter)) {
    $query .= " AND DATE(tanggal) = '$date_filter'";
}
$query .= " ORDER BY tanggal DESC";
$result = $conn->query($query);

// Data untuk grafik (jumlah per item)
$data_chart = $conn->query("SELECT item, SUM(qty) as total_qty FROM log_transaksi GROUP BY item ORDER BY item ASC");
$chart_labels = [];
$chart_data = [];
while ($row = $data_chart->fetch_assoc()) {
  $chart_labels[] = $row['item'];
  $chart_data[] = $row['total_qty'];
}


// // Data untuk grafik (jumlah per item) berdasarkan filter
// $filtered_chart = $conn->query("SELECT item, SUM(qty) as total_qty FROM log_transaksi WHERE 1"
//     . (!empty($item_filter) ? " AND item LIKE '%$item_filter%'" : "")
//     . (!empty($date_filter) ? " AND DATE(tanggal) = '$date_filter'" : "")
//     . " GROUP BY item ORDER BY item ASC");
// $chart_labels = [];
// $chart_data = [];
// while ($row = $filtered_chart->fetch_assoc()) {
//   $chart_labels[] = $row['item'];
//   $chart_data[] = $row['total_qty'];
// }

// Data untuk grafik (jumlah per item) berdasarkan filter
$filtered_chart = $conn->query("SELECT item, SUM(qty) as total_qty FROM log_transaksi WHERE 1"
    . (!empty($item_filter) ? " AND item LIKE '%$item_filter%'" : "")
    . (!empty($date_filter) ? " AND DATE(tanggal) = '$date_filter'" : "")
    . " GROUP BY item ORDER BY item ASC");
$chart_labels = [];
$chart_data = [];
while ($row = $filtered_chart->fetch_assoc()) {
  $chart_labels[] = $row['item'];
  $chart_data[] = $row['total_qty'];
}


// // Data untuk grafik scatter berdasarkan filter
// $scatter_query = "SELECT tanggal, qty FROM log_transaksi WHERE 1";
// if (!empty($item_filter)) {
//     $scatter_query .= " AND item LIKE '%$item_filter%'";
// }
// if (!empty($date_filter)) {
//     $scatter_query .= " AND DATE(tanggal) = '$date_filter'";
// }
// $scatter_query .= " ORDER BY tanggal ASC";
// $data_scatter = $conn->query($scatter_query);
// $scatter_data = [];
// while ($row = $data_scatter->fetch_assoc()) {
//   $scatter_data[] = [
//     'x' => $row['tanggal'],
//     'y' => (int)$row['qty']
//   ];
// }



// Data untuk grafik scatter berdasarkan filter
$scatter_query = "SELECT tanggal, qty FROM log_transaksi WHERE 1";
if (!empty($item_filter)) {
    $scatter_query .= " AND item LIKE '%$item_filter%'";
}
if (!empty($date_filter)) {
    $scatter_query .= " AND DATE(tanggal) = '$date_filter'";
}
$scatter_query .= " ORDER BY tanggal ASC";
$data_scatter = $conn->query($scatter_query);
$scatter_data = [];
while ($row = $data_scatter->fetch_assoc()) {
  $scatter_data[] = [
    'x' => $row['tanggal'],
    'y' => (int)$row['qty']
  ];
}

// Data untuk grafik scatter
$data_scatter = $conn->query("SELECT tanggal, qty FROM log_transaksi ORDER BY tanggal ASC");
$scatter_data = [];
while ($row = $data_scatter->fetch_assoc()) {
  $scatter_data[] = [
    'x' => $row['tanggal'],
    'y' => (int)$row['qty']
  ];
}

// Data untuk grafik line qty total harian
$line_chart = $conn->query("SELECT DATE(tanggal) as hari, SUM(qty) as total FROM log_transaksi GROUP BY DATE(tanggal) ORDER BY hari ASC");
$line_labels = [];
$line_data = [];
while ($row = $line_chart->fetch_assoc()) {
  $line_labels[] = $row['hari'];
  $line_data[] = $row['total'];
}

// Data untuk grafik line qty total harian berdasarkan filter
$line_query = "SELECT DATE(tanggal) as hari, SUM(qty) as total FROM log_transaksi WHERE 1";
if (!empty($item_filter)) {
    $line_query .= " AND item LIKE '%$item_filter%'";
}
if (!empty($date_filter)) {
    $line_query .= " AND DATE(tanggal) = '$date_filter'";
}
$line_query .= " GROUP BY DATE(tanggal) ORDER BY hari ASC";
$line_chart = $conn->query($line_query);
$line_labels = [];
$line_data = [];
while ($row = $line_chart->fetch_assoc()) {
  $line_labels[] = $row['hari'];
  $line_data[] = $row['total'];
}

// Total transaksi, total qty, total item unik
$total_qty = array_sum($chart_data);
$total_item = count($chart_labels);
$total_transaksi = $result->num_rows;

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
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // if (isset($_POST['tambah_barang'])) {
//         // $item = $_POST['item'];
//         // $jumlah = $_POST['jumlah'];
//         // $conn->query("INSERT INTO stok (item, jumlah) VALUES ('$item', '$jumlah')");
//         // $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', '$jumlah', 'tambah')");
//         if (isset($_POST['tambah_barang'])) {
//     $item = $_POST['item'];
//     $jumlah = $_POST['jumlah'];

//     // Proses Upload Gambar
//     $gambar_name = '';
//     if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
//         $gambar_name = time() . '_' . basename($_FILES['gambar']['name']);
//         $gambar_tmp = $_FILES['gambar']['tmp_name'];
//         $upload_dir = 'uploads/';
//         move_uploaded_file($gambar_tmp, $upload_dir . $gambar_name);
//     }

//     $conn->query("INSERT INTO stok (item, jumlah, gambar) VALUES ('$item', '$jumlah', '$gambar_name')");
//     $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', '$jumlah', 'tambah')");
//     exit(json_encode(['success' => true]));
// }

//         exit(json_encode(['success' => true]));
//     }

//     // if (isset($_POST['hapus_stok'])) {
//     //     $stok_id = $_POST['stok_id'];
//     //     $item_row = $conn->query("SELECT item FROM stok WHERE id = '$stok_id'")->fetch_assoc();
//     //     $item = $item_row['item'] ?? '-';
//     //     $conn->query("DELETE FROM stok WHERE id = '$stok_id'");
//     //     $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', 0, 'hapus')");
//     //     exit(json_encode(['success' => true]));
//     // }
//     if (isset($_POST['hapus_stok'])) {
//     $stok_id = $_POST['stok_id'];

//     // Ambil data stok termasuk gambar
//     $item_row = $conn->query("SELECT item, gambar FROM stok WHERE id = '$stok_id'")->fetch_assoc();
//     $item = $item_row['item'] ?? '-';
//     $gambar = $item_row['gambar'] ?? '';

//     // Hapus file gambar jika ada
//     if (!empty($gambar) && file_exists("uploads/$gambar")) {
//         unlink("uploads/$gambar");
//     }

//     // Hapus dari database
//     $conn->query("DELETE FROM stok WHERE id = '$stok_id'");
//     $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', 0, 'hapus')");
//     exit(json_encode(['success' => true]));
// }


//     if (isset($_POST['update_stok'])) {
//         $stok_id = $_POST['stok_id'];
//         $jumlah = $_POST['jumlah'];
//         $conn->query("UPDATE stok SET jumlah = jumlah + $jumlah WHERE id = '$stok_id'");
//         $item_row = $conn->query("SELECT item FROM stok WHERE id = '$stok_id'")->fetch_assoc();
//         $item = $item_row['item'] ?? '-';
//         $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi) VALUES ('$item', '$jumlah', 'update')");
//         exit(json_encode(['success' => true]));
//     }
// // }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_barang'])) {
        ob_clean(); // 💡 Bersihkan buffer output sebelumnya
        $item = $_POST['item'];
        $jumlah = $_POST['jumlah'];

        $gambar_name = '';
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
            $gambar_name = time() . '_' . preg_replace("/[^A-Za-z0-9.\-_]/", '', basename($_FILES['gambar']['name']));
            $gambar_tmp = $_FILES['gambar']['tmp_name'];
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            move_uploaded_file($gambar_tmp, $upload_dir . $gambar_name);
        }

        $conn->query("INSERT INTO stok (item, jumlah, gambar) VALUES ('$item', '$jumlah', '$gambar_name')");
        // $admin = $_SESSION['admin'] ?? 'SYSTEM';

        // $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi, admin) VALUES ('$item', '$jumlah', 'tambah', '$admin')");
        $admin = $_SESSION['admin'] ?? 'SYSTEM';
$conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi, admin) VALUES ('$item', '$jumlah', 'tambah', '$admin')");


        echo json_encode(['success' => true, 'filename' => $gambar_name]);
        exit;
    }


    if (isset($_POST['hapus_stok'])) {
        $stok_id = $_POST['stok_id'];

        $item_row = $conn->query("SELECT item, gambar FROM stok WHERE id = '$stok_id'")->fetch_assoc();
        $item = $item_row['item'] ?? '-';
        $gambar = $item_row['gambar'] ?? '';

        if (!empty($gambar) && file_exists("uploads/$gambar")) {
            unlink("uploads/$gambar");
        }

        $conn->query("DELETE FROM stok WHERE id = '$stok_id'");
        // $admin = $_SESSION['admin'] ?? 'SYSTEM';


        // $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi, admin) VALUES ('$item', 0, 'hapus')");
        $admin = $_SESSION['admin'] ?? 'SYSTEM';
$conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi, admin) VALUES ('$item', 0, 'hapus', '$admin')");


        exit(json_encode(['success' => true]));
    }

    if (isset($_POST['update_stok'])) {
        $stok_id = $_POST['stok_id'];
        $jumlah = $_POST['jumlah'];

        $conn->query("UPDATE stok SET jumlah = jumlah + $jumlah WHERE id = '$stok_id'");
        $item_row = $conn->query("SELECT item FROM stok WHERE id = '$stok_id'")->fetch_assoc();
        $item = $item_row['item'] ?? '-';
        // $admin = $_SESSION['admin'] ?? 'SYSTEM';


        // $conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi,admin) VALUES ('$item', '$jumlah', 'update', 'admin')");
        $admin = $_SESSION['admin'] ?? 'SYSTEM';
$conn->query("INSERT INTO riwayat_stok (item, jumlah, aksi, admin) VALUES ('$item', '$jumlah', 'update', '$admin')");


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
    <!-- Tambahkan di <head> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin Fotokopi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script> -->
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    <style>
    body {
        background: #f8f9fa;
        overflow-x: hidden;
    }

    .card {
        border-radius: 1rem;
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .btn-custom {
        transition: transform 0.2s;
    }

    .btn-custom:hover {
        transform: scale(1.05);
    }

    .fade-in {
        animation: fadeInUp 0.5s ease-in-out;
    }

    .table tbody tr:hover {
        background-color: #f1f1f1;
    }

    .spinner-border {
        width: 2rem;
        height: 2rem;
    }

    .table th,
    .table td {
        border-color: #dee2e6 !important;
    }

    .table tbody tr:hover {
        background: #fdfdfd;
        transition: 0.3s ease-in-out;
    }

    dotlottie-player {
        max-width: 100%;
        display: inline-block;
        vertical-align: middle;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .summary-cards {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .summary-cards .card {
        flex: 1 1 200px;
        background: #ffffff;
        border-left: 5px solid #0d6efd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    </style>
</head>

<body class="bg-light">
    <div class="container">
        <h2 class="mb-4">📈 Log Transaksi Penawaran</h2>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="item" class="form-control" placeholder="Cari item..."
                    value="<?= htmlspecialchars($item_filter) ?>">
            </div>
            <div class="col-md-4">
                <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($date_filter) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
            </div>
        </form>

        <div class="summary-cards">
            <div class="card p-3">
                <h6>Total Transaksi</h6>
                <h4><?= $total_transaksi ?></h4>
            </div>
            <div class="card p-3">
                <h6>Total Qty</h6>
                <h4><?= $total_qty ?></h4>
            </div>
            <div class="card p-3">
                <h6>Jumlah Item Unik</h6>
                <h4><?= $total_item ?></h4>
            </div>
        </div>

        <div class="dashboard-grid">
            <div>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($log = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $log['tanggal'] ?></td>
                            <td><?= $log['item'] ?></td>
                            <td><?= $log['qty'] ?></td>
                            <td><span class="badge bg-info text-dark"><?= strtoupper($log['status']) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div>
                <h5 class="mb-3">📊 Total Pemesanan per Item</h5>
                <canvas id="chartTransaksi" height="200"></canvas>
                <h5 class="mt-5">📉 Traffic Scatter Plot</h5>
                <canvas id="scatterChart" height="200"></canvas>
                <h5 class="mt-5">📆 Trend Harian</h5>
                <canvas id="lineChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <!-- <div class="container py-5"> -->
    <div class="container py-5 animate__animated animate__fadeIn"></div>
    <!-- <h2 class="mb-4">Dashboard Admin Fotokopi</h2>
    <a href="logout.php" class="btn btn-danger mb-3">Logout</a> -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-4">📋 Dashboard Admin Fotokopi</h2>
        <a href="logout.php" class="btn btn-danger btn-custom">
            <i class="fas fa-sign-out-alt me-2 "></i>Logout
        </a>
    </div>

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
                        <button type="submit" class="btn btn-primary btn-sm">
                            <!-- <lottie-player src="https://assets5.lottiefiles.com/packages/lf20_iwmd6pyr.json"
                                background="transparent" speed="1" style="width: 50px; height: 50px;" loop autoplay>
                            </lottie-player> -->
                            <lottie-player
                                src="https://lottie.host/8b9472c7-b028-4b5e-aeb4-41a70904c4d1/OpZnPmsPSl.json"
                                background="transparent" speed="1" style="width: 50px; height: 50px;" loop autoplay>
                            </lottie-player>
                            Update Status
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <!-- Form Tambah Barang -->
    <div class="card p-3 mb-4 shadow-sm fade-in">
        <h5 class="mb-3">➕ Tambah Barang ke Stok</h5>
        <!-- <form id="form-tambah" class="row g-2 align-items-end"> -->
        <form id="form-tambah" class="row g-3 align-items-end" method="POST" enctype="multipart/form-data">

            <!-- <div class="col-md-6">
                <input type="text" name="item" class="form-control" placeholder="Contoh: Kertas A4" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required>
            </div>
            <div class="col-md-4">
                <input type="file" name="gambar" class="form-control" accept="image/*" required>
            </div>
            <div class="col-12">
                <img id="preview-gambar" src="#" alt="Preview Gambar" style="max-height: 120px; display: none;"
                    class="mt-2 img-thumbnail">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tambah</button>
            </div> -->
            <div class="col-md-4">
                <label for="item" class="form-label">Nama Barang</label>
                <input type="text" name="item" class="form-control" placeholder="Contoh: Kertas A4" required />
            </div>
            <div class="col-md-3">
                <label for="jumlah" class="form-label">Jumlah</label>
                <input type="number" name="jumlah" class="form-control" placeholder="Jumlah" required />
            </div>
            <div class="col-md-3">
                <label for="gambar" class="form-label">Upload Gambar</label>
                <input type="file" name="gambar" class="form-control" accept="image/*" required />
            </div>
            <div class="col-md-2">
                <!-- <button type="submit" class="btn btn-primary w-100 btn-custom">
                    <i class="fas fa-plus"></i> Tambah
                </button> -->
                <button type="submit" class="btn btn-success btn-sm p-0 border-0 bg-transparent">
                    <!-- <lottie-player src="https://lottie.host/64e746f8-55be-4578-a8c5-cf17e1cb0d08/MEGcldJkeS.json"
                        background="transparent" speed="1" style="width: 40px; height: 40px;" loop autoplay>
                    </lottie-player> -->
                    <dotlottie-player src="https://lottie.host/d5f7761f-3fbe-4940-8743-f8e16bf99e8a/Udo0JfgfGN.lottie"
                        background="transparent" speed="1"
                        style="width: 100px; height: 100px; position: absolute; top: 40px; max-width: 100%; display: inline-block; display: flex; align-items: center;"
                        loop autoplay>
                    </dotlottie-player>
                </button>

            </div>
            <div class="col-12">
                <img id="preview-gambar" src="#" alt="Preview Gambar" style="max-height: 120px; display: none;"
                    class="img-thumbnail mt-3" />
            </div>
        </form>

    </div>

    <!-- Tabel Stok -->
    <div class="fade-in"></div>
    <h4>📦 Daftar Stok Barang</h4>
    <table class="table table-striped table-hover align-middle" id="stok-table">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Gambar</th>
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
                <td>
                    <?php if ($stok['gambar']): ?>
                    <img src="uploads/<?= $stok['gambar'] ?>" alt="<?= $stok['item'] ?>" width="50">
                    <?php else: ?>
                    <span class="text-muted">Tidak ada gambar</span>
                    <?php endif; ?>
                </td>
                <td><?= $stok['item'] ?></td>
                <td><?= $stok['jumlah'] ?></td>
                <td>
                    <form class="form-update d-flex gap-2">
                        <input type="hidden" name="stok_id" value="<?= $stok['id'] ?>">
                        <input type="number" name="jumlah" class="form-control" placeholder="+Jumlah" required>
                        <!-- <button type="submit" class="btn btn-success btn-sm">+</button> -->
                        <button type="submit" class="btn btn-success btn-sm p-0 border-0 bg-transparent">
                            <!-- <lottie-player src="https://assets6.lottiefiles.com/packages/lf20_kq5rGs.json"
                                background="transparent" speed="1" style="width: 40px; height: 40px;" loop autoplay>
                            </lottie-player> -->
                            <dotlottie-player
                                src="https://lottie.host/d5f7761f-3fbe-4940-8743-f8e16bf99e8a/Udo0JfgfGN.lottie"
                                background="transparent" speed="1"
                                style="width: 70px; height: 70px; max-width: 100%; display: inline-block; display: flex; align-items: center; "
                                loop autoplay>
                            </dotlottie-player>
                        </button>

                    </form>

                </td>
                <td>
                    <form class="form-hapus">
                        <input type="hidden" name="stok_id" value="<?= $stok['id'] ?>">
                        <!-- <button type="submit" class="btn btn-danger btn-sm">🗑️</button> -->
                        <button type="submit" class="btn btn-danger btn-sm p-0 border-0 bg-transparent">
                            <!-- <lottie-player src="https://assets4.lottiefiles.com/packages/lf20_dltzgbrv.json"
                                background="transparent" speed="1" style="width: 40px; height: 40px;" loop autoplay>
                            </lottie-player> -->

                            <dotlottie-player
                                src="https://lottie.host/991adeea-0602-4621-9646-0a736aa7744d/ojpxGEK7aN.lottie"
                                background="transparent" speed="1"
                                style="width: 70px; height: 70px; max-width: 100%; display: inline-block; display: flex; align-items: center;"
                                loop autoplay>
                            </dotlottie-player>
                        </button>

                    </form>

                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    </div>
    <!-- Loading Spinner -->
    <div id="upload-loader"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #00000088; z-index: 9999; align-items: center; justify-content: center;"
        class="text-center">
        <lottie-player src="https://assets10.lottiefiles.com/packages/lf20_n1im7pqs.json" background="transparent"
            speed="1" style="width: 150px; height: 150px;" loop autoplay>
        </lottie-player>
        <div class="spinner-border text-light" role="status"></div>
    </div>

    <h4 class="mt-5">🕘 Riwayat Perubahan Stok</h4>
    <form method="GET" class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="text" name="filter_item" class="form-control" placeholder="Cari berdasarkan item..."
                value="<?= $_GET['filter_item'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="filter_tanggal" class="form-control" value="<?= $_GET['filter_tanggal'] ?? '' ?>">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
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
                        // alert('Status berhasil diperbarui!');
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Berhasil!',
                        //     text: 'Barang berhasil diperbarui ke stok.',
                        //     timer: 1500,
                        //     showConfirmButton: false
                        // });
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: 'Stok diperbarui',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1200,
                            timerProgressBar: true
                        });



                        // window.open('index.php?show_updated=true', '_blank');
                        // ✅ Menampilkan data langsung di halaman form (tanpa session)
                        // const query = new URLSearchParams(res.order).toString();
                        // window.open('index.php?show_updated=true&' + query, '_blank');

                    } else {
                        // alert('Gagal memuat data pesanan!');
                        Swal.fire({
                            icon: 'error',
                            title: 'Not Success',
                            text: 'Gagal memuat data pesanan!',
                            timer: 1500,
                            showConfirmButton: false
                        });

                    }
                },
                error: function() {
                    // alert('Terjadi kesalahan saat mengirim data!');
                    Swal.fire({
                        icon: 'error',
                        title: 'Opps!!',
                        text: 'Terjadi kesalahan saat mengirim data!!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
        // // Tambah Barang
        // $('#form-tambah').on('submit', function(e) {
        //     e.preventDefault();
        //     // $.post('dashboard.php', $(this).serialize() + '&tambah_barang=1', function(res) {
        //     //     location.reload();
        //     // });
        //     $('#form-tambah').on('submit', function(e) {
        //         e.preventDefault();
        //         const formData = new FormData(this);
        //         formData.append('tambah_barang', 1);

        //         $.ajax({
        //             type: 'POST',
        //             url: 'dashboard.php',
        //             data: formData,
        //             processData: false,
        //             contentType: false,
        //             success: function(res) {
        //                 location.reload();
        //             },
        //             error: function() {
        //                 alert('❌ Gagal menambahkan barang!');
        //             }
        //         });
        //     });

        // });

        $('#form-tambah').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('tambah_barang', 1);

            // $('#upload-loader').show(); // tampilkan loading
            $('#upload-loader').fadeIn();

            // $.ajax({
            //     type: 'POST',
            //     url: 'dashboard.php',
            //     data: formData,
            //     processData: false,
            //     contentType: false,
            //     success: function(res) {
            //         $('#upload-loader').hide(); // sembunyikan loading
            //         location.reload();
            //     },
            //     error: function() {
            //         $('#upload-loader').hide(); // sembunyikan loading
            //         alert('❌ Gagal menambahkan barang!');
            //     }
            // });
            $.ajax({
                type: 'POST',
                url: 'dashboard.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    try {
                        const json = JSON.parse(res);
                        if (json.success) {
                            // $('#upload-loader').hide();
                            $('#upload-loader').fadeOut();
                            location.reload();
                        } else {
                            alert('❌ Upload gagal, respons tidak valid!');
                            // $('#upload-loader').hide();
                            $('#upload-loader').fadeOut();
                        }
                    } catch (e) {
                        alert('❌ Upload gagal, server tidak merespons dengan benar!');
                        // $('#upload-loader').hide();
                        $('#upload-loader').fadeOut();
                    }
                },
                error: function() {
                    // $('#upload-loader').hide();
                    alert('❌ Gagal menghubungi server!');
                    $('#upload-loader').fadeOut();
                }
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

        // Preview gambar sebelum submit
        $('input[name="gambar"]').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // $('#preview-gambar').attr('src', e.target.result).show();
                    $('#preview-gambar').attr('src', e.target.result).fadeIn();
                }
                reader.readAsDataURL(file);
            } else {
                // $('#preview-gambar').hide();
                $('#preview-gambar').fadeOut();
            }
        });
    });
    new Chart(document.getElementById('chartTransaksi'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Total Pemesanan',
                data: <?= json_encode($chart_data) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('scatterChart'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Qty Over Time',
                data: <?= json_encode($scatter_data) ?>,
                backgroundColor: 'rgba(255, 159, 64, 0.6)'
            }]
        },
        options: {
            responsive: true,
            parsing: {
                xAxisKey: 'x',
                yAxisKey: 'y'
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'minute',
                        tooltipFormat: 'yyyy-MM-dd HH:mm:ss'
                    },
                    title: {
                        display: true,
                        text: 'Waktu'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Qty'
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($line_labels) ?>,
            datasets: [{
                label: 'Total Qty per Hari',
                data: <?= json_encode($line_data) ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.3)',
                borderColor: 'rgba(153, 102, 255, 1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

    <!-- <div id="upload-loader"
        style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:#00000077;display:flex;align-items:center;justify-content:center;">
        <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status"></div>
    </div> -->
    <?php ob_end_flush(); ?>

</body>
<!-- <div id="upload-loader"
    style="display:none; position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:#00000077;display:flex;align-items:center;justify-content:center;">
    <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status"></div>
</div> -->


</html>