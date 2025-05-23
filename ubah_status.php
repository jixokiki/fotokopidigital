<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orderId = $_POST['order_id'] ?? 0;
  $newStatus = $_POST['status'] ?? '';

  if (!$orderId || !in_array($newStatus, ['pending', 'proses', 'selesai', 'batal'])) {
    die("Permintaan tidak valid.");
  }

  $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $newStatus, $orderId);
  if ($stmt->execute()) {
    echo "Status berhasil diubah.";
  } else {
    echo "Gagal mengubah status.";
  }
} else {
  // Tampilkan form ubah status (sementara, versi manual)
  $id = $_GET['id'] ?? 0;

  if (!$id || !is_numeric($id)) {
    die("ID tidak valid.");
  }

  $stmt = $conn->prepare("SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $data = $stmt->get_result()->fetch_assoc();

  if (!$data) {
    die("Data tidak ditemukan.");
  }
  ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ubah Status Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2>Ubah Status Pesanan</h2>
        <p>Nama: <strong><?= htmlspecialchars($data['name']) ?></strong></p>
        <p>File: <?= htmlspecialchars($data['file_name']) ?></p>
        <form method="POST">
            <input type="hidden" name="order_id" value="<?= $data['id'] ?>">
            <div class="mb-3">
                <label>Status Baru</label>
                <select name="status" class="form-select" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="pending" <?= $data['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="proses" <?= $data['status'] === 'proses' ? 'selected' : '' ?>>Proses</option>
                    <option value="selesai" <?= $data['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="batal" <?= $data['status'] === 'batal' ? 'selected' : '' ?>>Batal</option>
                </select>
            </div>
            <button class="btn btn-primary">Simpan</button>
        </form>
    </div>
</body>

</html>

<?php } ?>