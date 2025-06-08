<?php
// penawaran.php
include 'db.php';

// Ambil data dari URL
$item = $_GET['item'] ?? '';
$jumlah = intval($_GET['jumlah'] ?? 0);
$status = $_GET['status'] ?? '';

// Cek stok dari database
$stmt = $conn->prepare("SELECT * FROM stok WHERE item = ?");
$stmt->bind_param("s", $item);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
  die("Item tidak ditemukan.");
}

$sisaStok = intval($data['jumlah']);
$hargaSatuan = 10500000; // Contoh harga satuan
$total = $hargaSatuan * $jumlah;
$diskon = $total * 0.1;
$totalSetelahDiskon = $total - $diskon;

// Jika status OK dan jumlah cukup, kurangi stok
if ($status === 'ok') {
  if ($jumlah <= $sisaStok) {
    $stokBaru = $sisaStok - $jumlah;
    $stmt = $conn->prepare("UPDATE stok SET jumlah = ? WHERE item = ?");
    $stmt->bind_param("is", $stokBaru, $item);
    $stmt->execute();
    // Auto log transaksi
    $log = $conn->prepare("INSERT INTO log_transaksi (item, qty, status, tanggal) VALUES (?, ?, ?, NOW())");
    $log->bind_param("sis", $item, $jumlah, $status);
    $log->execute();
  } else {
    die("Stok tidak mencukupi.");
  }
}


// $item = $_GET['item'] ?? '';
// $jumlah = $_GET['jumlah'] ?? 0;
// $status = $_GET['status'] ?? '';

// Simulasi generate file PDF (atau gunakan TCPDF/Dompdf)
$pdfFile = "generated/penawaran-$item-$jumlah.pdf";

// // Jika status penawaran (stok 0 atau over)
// if ($status === 'penawaran') {
//     echo "<h3>📄 Stok kosong. Buat penawaran khusus?</h3>";
//     echo "<p>Item: <strong>$item</strong></p>";
//     echo "<p>Jumlah diminta: <strong>$jumlah</strong></p>";

//     echo "<a class='btn btn-success' target='_blank' 
//             href='https://api.whatsapp.com/send?phone=6285817298071&text=" .
//         urlencode("Saya ingin mengajukan penawaran untuk item *$item* sebanyak *$jumlah*. Mohon diproses.") .
//         "'>
//             Ajukan Penawaran via WhatsApp
//           </a>";

//     echo "<p class='mt-3'><a href='$pdfFile' target='_blank'>📎 Lihat Penawaran (PDF)</a></p>";
//     exit;
// }
// Tampilkan halaman penawaran
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Penawaran Harga - <?= htmlspecialchars($item) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Library yang dibutuhkan oleh jsPDF html plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <style>
    body {
        padding: 40px;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f9f9f9;
    }

    .penawaran {
        background: white;
        padding: 30px;
        border-radius: 10px;
        /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); */

        max-width: 800px;
        margin: 0 auto;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .judul {
        font-weight: bold;
        font-size: 1.5rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .penawaran {
            page-break-after: avoid;
        }
    }
    </style>
</head>

<body>
    <script>
    Swal.fire({
        title: 'Memuat halaman penawaran...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
        timer: 1500
    });
    </script>
    <div class="penawaran" id="penawaranContent">
        <h2 class="judul mb-4">📄 Surat Penawaran Harga</h2>
        <p>PT. WAE MANDIRI KARYA</p>
        <p>JL. BKKBN No. 7 RT. 02 RW.07, Mustika Jaya, Bekasi 17158</p>
        <p>Email : pt.waemandirikarya@gmail.com | Phone : 0812 6888 8969</p>
        <hr>
        <p><strong>Kepada:</strong> PT. STAINLESS STEEL PRIMAVALVE MAJU BERSAMA</p>
        <p><strong>Re:</strong> <?= htmlspecialchars($item) ?></p>
        <p><strong>Tanggal:</strong> <?= date('d M Y') ?> | <strong>Nomor PNR:</strong> PNR006-WMK-05-2025-SPV</p>

        <table class="table table-bordered mt-4">
            <thead class="table-light">
                <tr>
                    <th>Nama Barang</th>
                    <th>Qty</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($item) ?></td>
                    <td><?= $jumlah ?></td>
                    <td>Rp<?= number_format($hargaSatuan, 0, ',', '.') ?></td>
                    <td>Rp<?= number_format($total, 0, ',', '.') ?></td>
                </tr>
            </tbody>
        </table>

        <p><strong>Total:</strong> Rp<?= number_format($total, 0, ',', '.') ?></p>
        <p><strong>Diskon 10%:</strong> Rp<?= number_format($diskon, 0, ',', '.') ?></p>
        <p><strong>Jumlah Setelah Diskon:</strong> Rp<?= number_format($totalSetelahDiskon, 0, ',', '.') ?></p>

        <p class="mt-4"><strong>Keterangan:</strong></p>
        <ul>
            <li>Harga di atas belum termasuk PPN 11%</li>
            <li>Penawaran ini berlaku selama 30 hari</li>
        </ul>
        <!-- <div id="qrcode" class="my-4"></div> -->
        <div class="text-center mt-4">
            <!-- <div id="qrcode"></div> -->
            <div id="qrcode" class="my-4"></div>
        </div>

        <div class="mt-4 d-flex gap-3">
            <button class="btn btn-success" onclick="downloadPDF()">📥 Unduh PDF</button>
            <button class="btn btn-secondary" onclick="window.print()">🖨️ Cetak Halaman</button>
        </div>

        <p class="mt-5">Hormat kami,</p>
        <p><strong>FIDELYA NAZHIMAH</strong><br>85215949622</p>
    </div>
    <script>
    // Generate QR Code
    const qrcode = new QRCode(document.getElementById("qrcode"), {
        text: window.location.href,
        width: 150,
        height: 150
    });

    // Export PDF
    // async function downloadPDF() {
    //     const {
    //         jsPDF
    //     } = window.jspdf;
    //     const doc = new jsPDF();
    //     await doc.html(document.getElementById('penawaranContent'), {
    //         callback: function(doc) {
    //             doc.save("Penawaran_<?= $item ?>.pdf");
    //         },
    //         x: 10,
    //         y: 10
    //     });
    // }

    async function downloadPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF("p", "mm", "a4");

        const element = document.getElementById("penawaranContent");

        // Scroll ke atas agar tidak ada animasi terganggu
        window.scrollTo(0, 0);

        await html2canvas(element, {
            scale: 2, // kualitas lebih tinggi
            useCORS: true
        }).then(canvas => {
            const imgData = canvas.toDataURL("image/png");
            const imgProps = doc.getImageProperties(imgData);
            const pdfWidth = doc.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            doc.save("Penawaran_<?= $item ?>.pdf");
        }).catch(error => {
            console.error("❌ Gagal export PDF:", error);
            Swal.fire("Gagal mengunduh", "Terjadi kesalahan saat membuat PDF.", "error");
        });
    }
    </script>
</body>

</html>