<?php
require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

$format = $_GET['format'] ?? 'pdf';
$filter_query = "SELECT * FROM riwayat_stok WHERE 1";

if (!empty($_GET['filter_item'])) {
    $item = $_GET['filter_item'];
    $filter_query .= " AND item LIKE '%$item%'";
}
if (!empty($_GET['filter_tanggal'])) {
    $tanggal = $_GET['filter_tanggal'];
    $filter_query .= " AND DATE(created_at) = '$tanggal'";
}

$filter_query .= " ORDER BY created_at DESC";
$result = $conn->query($filter_query);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$filename = 'riwayat_stok_' . date('Ymd_His');
$save_path = __DIR__ . "/exports/$filename.$format";
$public_url = "https://yourdomain.com/exports/$filename.$format";

if ($format === 'excel') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['Waktu', 'Item', 'Jumlah', 'Aksi', 'Admin'], NULL, 'A1');
    $i = 2;
    foreach ($data as $row) {
        $sheet->setCellValue("A$i", $row['created_at']);
        $sheet->setCellValue("B$i", $row['item']);
        $sheet->setCellValue("C$i", $row['jumlah']);
        $sheet->setCellValue("D$i", strtoupper($row['aksi']));
        $sheet->setCellValue("E$i", $row['admin']);
        $i++;
    }
    $writer = new Xlsx($spreadsheet);
    $writer->save($save_path);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=$filename.xlsx");
    readfile($save_path);

} elseif ($format === 'word') {
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();
    $header = $section->addHeader();
    $header->addImage('IMG_1156.PNG', ['width' => 60, 'height' => 60, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]);
    $header->addText('Laporan Riwayat Stok', ['bold' => true, 'size' => 16]);
    $table = $section->addTable();
    $table->addRow();
    foreach (['Waktu', 'Item', 'Jumlah', 'Aksi', 'Admin'] as $heading) {
        $table->addCell(2000)->addText($heading);
    }
    foreach ($data as $row) {
        $table->addRow();
        $table->addCell(2000)->addText($row['created_at']);
        $table->addCell(2000)->addText($row['item']);
        $table->addCell(2000)->addText($row['jumlah']);
        $table->addCell(2000)->addText(strtoupper($row['aksi']));
        $table->addCell(2000)->addText($row['admin']);
    }
    $section->addTextBreak();
    $section->addText('Digital Signature:', ['italic' => true]);
    if (file_exists('IMG_1156.PNG')) {
        $section->addImage('IMG_1156.PNG', ['width' => 80]);
    }
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($save_path);
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    header("Content-Disposition: attachment;filename=$filename.docx");
    readfile($save_path);

} else {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['Waktu', 'Item', 'Jumlah', 'Aksi', 'Admin'], NULL, 'A1');
    $i = 2;
    foreach ($data as $row) {
        $sheet->setCellValue("A$i", $row['created_at']);
        $sheet->setCellValue("B$i", $row['item']);
        $sheet->setCellValue("C$i", $row['jumlah']);
        $sheet->setCellValue("D$i", strtoupper($row['aksi']));
        $sheet->setCellValue("E$i", $row['admin']);
        $i++;
    }
    $writer = new Mpdf($spreadsheet);
    $writer->save($save_path);
    header('Content-Type: application/pdf');
    header("Content-Disposition: attachment;filename=$filename.pdf");
    readfile($save_path);
}

// Kirim via WhatsApp Web
$encoded_url = urlencode($public_url);
$wa_link = "https://api.whatsapp.com/send?phone=6285817298071&text=" . urlencode(
    "Halo admin PT. Kemas Kayu Indonesia 👋%0A%0ASaya telah mengekspor riwayat stok dan berikut adalah tautan file hasilnya:%0A$public_url%0A%0ATerima kasih 🙏"
);
echo "<script>window.open('$wa_link', '_blank');</script>";

exit();