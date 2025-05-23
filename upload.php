<?php
// ✅ 5. FILE: upload.php - Logika Upload dan Simpan Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  include 'db.php';

  $name = $_POST['name'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
  $print_type = $_POST['print_type'];
  $paper_size = $_POST['paper_size'];
  $binding = $_POST['binding'];
  $delivery = $_POST['delivery'];
  $payment_method = $_POST['payment_method'];
  $total_price = $_POST['total_price'];

  $file = $_FILES['file'];
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($file['name']);
  move_uploaded_file($file['tmp_name'], $target_file);

  // Simpan ke tabel users
  $conn->query("INSERT INTO users (name, email, phone, address) VALUES ('$name', '$email', '$phone', '$address')");
  $user_id = $conn->insert_id;

  // Simpan ke tabel orders
  $stmt = $conn->prepare("INSERT INTO orders (user_id, file_name, file_path, print_type, paper_size, binding, delivery, payment_method, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssssisd", $user_id, $file['name'], $target_file, $print_type, $paper_size, $binding, $delivery, $payment_method, $total_price);
  $stmt->execute();

  echo "<script>alert('Pesanan berhasil dikirim!'); window.location='index.php';</script>";
}
?>