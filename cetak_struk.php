<?php
require('libs/fpdf/fpdf.php');
include 'db.php';

$id = $_GET['id'] ?? 0;

// Validasi ID
if (!$id || !is_numeric($id)) {
  die("ID tidak valid.");
}

// Ambil data pesanan
$stmt = $conn->prepare("SELECT o.*, u.name, u.email, u.phone, u.address FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
  die("Data tidak ditemukan.");
}

// Format rupiah
function rupiah($angka) {
  return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Inisialisasi PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'STRUK PEMESANAN FOTOKOPI DIGITAL', 0, 1, 'C');
$pdf->Ln(5);

// Info pelanggan
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 10, 'Nama');
$pdf->Cell(80, 10, ': ' . $data['name'], 0, 1);
$pdf->Cell(40, 10, 'Email');
$pdf->Cell(80, 10, ': ' . $data['email'], 0, 1);
$pdf->Cell(40, 10, 'No HP');
$pdf->Cell(80, 10, ': ' . $data['phone'], 0, 1);
$pdf->Cell(40, 10, 'Alamat');
$pdf->MultiCell(150, 10, ': ' . $data['address'], 0, 1);
$pdf->Ln(5);

// Info pesanan
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Detail Pesanan', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, 'File', 0, 0);
$pdf->Cell(80, 10, ': ' . $data['file_name'], 0, 1);
$pdf->Cell(60, 10, 'Jenis Cetak', 0, 0);
$pdf->Cell(80, 10, ': ' . ucfirst($data['print_type']), 0, 1);
$pdf->Cell(60, 10, 'Ukuran Kertas', 0, 0);
$pdf->Cell(80, 10, ': ' . $data['paper_size'], 0, 1);
$pdf->Cell(60, 10, 'Jilid', 0, 0);
$pdf->Cell(80, 10, ': ' . ucfirst($data['binding']), 0, 1);
$pdf->Cell(60, 10, 'Pengiriman', 0, 0);
$pdf->Cell(80, 10, ': ' . ($data['delivery'] ? 'Ya' : 'Tidak'), 0, 1);
$pdf->Cell(60, 10, 'Metode Bayar', 0, 0);
$pdf->Cell(80, 10, ': ' . strtoupper($data['payment_method']), 0, 1);
$pdf->Cell(60, 10, 'Status', 0, 0);
$pdf->Cell(80, 10, ': ' . ucfirst($data['status']), 0, 1);
$pdf->Cell(60, 10, 'Tanggal Pesan', 0, 0);
$pdf->Cell(80, 10, ': ' . $data['created_at'], 0, 1);
$pdf->Ln(5);

// Total harga (jika ada field)
if (isset($data['total_price'])) {
  $pdf->SetFont('Arial', 'B', 14);
  $pdf->Cell(60, 10, 'Total Bayar', 0, 0);
  $pdf->Cell(80, 10, ': ' . rupiah($data['total_price']), 0, 1);
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Terima kasih telah menggunakan layanan kami.', 0, 1, 'C');

// Output PDF
$pdf->Output('I', 'Struk_Pesanan_' . $data['id'] . '.pdf');