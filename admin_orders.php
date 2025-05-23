<?php
include 'db.php';

$status_filter = $_GET['status'] ?? 'semua';
$statuses = ['semua', 'pending', 'proses', 'selesai', 'batal'];

if ($status_filter === 'semua') {
  $sql = "SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
  $stmt = $conn->prepare($sql);
} else {
  $sql = "SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = ? ORDER BY o.created_at DESC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $status_filter);
}
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin - Monitoring Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">📦 Monitoring Pesanan</h2>

        <div class="mb-3">
            <form method="GET" class="d-flex align-items-center">
                <label class="me-2">Filter Status:</label>
                <select name="status" onchange="this.form.submit()" class="form-select w-auto">
                    <?php foreach ($statuses as $s): ?>
                    <option value="<?= $s ?>" <?= $s === $status_filter ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach ?>
                </select>
            </form>
        </div>

        <?php if (!empty($orders)): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>File</th>
                    <th>Cetak</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['name']) ?></td>
                    <td><?= htmlspecialchars($o['email']) ?></td>
                    <td><?= htmlspecialchars($o['file_name']) ?></td>
                    <td><?= $o['print_type'] . ' - ' . $o['paper_size'] ?></td>
                    <td><span class="badge bg-secondary"><?= ucfirst($o['status']) ?></span></td>
                    <td><?= $o['created_at'] ?></td>
                    <td>
                        <a href="cetak_struk.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-success mb-1">Struk</a>
                        <a href="ubah_status.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-warning mb-1">Status</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="alert alert-info">Belum ada pesanan.</div>
        <?php endif ?>
    </div>
</body>

</html>