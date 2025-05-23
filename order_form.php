<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $print_type = $_POST['print_type'];
  $paper_size = $_POST['paper_size'];
  $file_name = $_FILES['file']['name'];
  $file_tmp = $_FILES['file']['tmp_name'];
  $upload_dir = 'uploads/';
  
  // Simpan file
  $file_path = $upload_dir . basename($file_name);
  move_uploaded_file($file_tmp, $file_path);

  // Insert ke database
  $stmt = $conn->prepare("INSERT INTO orders (user_id, file_name, file_path, print_type, paper_size) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("issss", $user_id, $file_name, $file_path, $print_type, $paper_size);
  $stmt->execute();

  // Redirect setelah berhasil
  header("Location: order_success.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pemesanan Berkas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Form Pemesanan Berkas</h2>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="print_type" class="form-label">Jenis Cetak</label>
                <input type="text" id="print_type" name="print_type" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="paper_size" class="form-label">Ukuran Kertas</label>
                <input type="text" id="paper_size" name="paper_size" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">File Berkas</label>
                <input type="file" id="file" name="file" class="form-control" required>
            </div>
            <button class="btn btn-primary">Pesan</button>
        </form>
    </div>
</body>

</html>