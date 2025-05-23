<?php
include 'db.php';

$orders = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $stmt = $conn->prepare("SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id = u.id WHERE u.email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Pemesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Cek Riwayat Pemesanan</h2>
        <form method="POST" class="mb-4">
            <input type="email" name="email" class="form-control mb-2" placeholder="Masukkan Email Anda" required>
            <button class="btn btn-primary">Cari</button>
        </form>

        <?php if (!empty($orders)) : ?>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>File</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Struk</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><?= htmlspecialchars($o['name']) ?></td>
                    <td><?= htmlspecialchars($o['file_name']) ?></td>
                    <td><?= $o['print_type'] . ' - ' . $o['paper_size'] ?></td>
                    <td><?= ucfirst($o['status']) ?></td>
                    <td><?= $o['created_at'] ?></td>
                    <td><a href="cetak_struk.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline-secondary">Struk</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST') : ?>
        <div class="alert alert-warning">Tidak ada pesanan untuk email tersebut.</div>
        <?php endif; ?>
    </div>
</body>

</html>