<?php
session_start();
include "db.php";
?>

<?php
// Ambil data stok
$stok_result = $conn->query("SELECT * FROM stok ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan Cetak Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Tambahkan ini di atas script yang error -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    <style>
    :root {
        --primary: #4e73df;
        --primary-dark: #2e59d9;
        --light-bg: #f4f7fc;
        --text-muted: #6c757d;
        --text-dark: #343a40;
        --shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .section-header {
        background: linear-gradient(to right, var(--primary), #6e85d3);
        color: #fff;
        padding: 1rem 2rem;
        border-radius: 1rem 1rem 0 0;
        font-weight: bold;
    }



    body {
        background-color: var(--light-bg);
        color: var(--text-muted);
        font-family: 'Inter', sans-serif;
        background: #f4f7fc;

    }

    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .form-section {
        box-shadow: var(--shadow);
    }


    h2,
    h3 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: bold;
    }

    ::placeholder {
        color: var(--text-muted);
        font-size: 0.9rem;
    }


    h4 {
        font-weight: bold;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .form-section {
        transition: all 0.3s ease-in-out;
        max-width: 100%;
        background-color: white;
        padding: 2.5rem;
        border-radius: 1rem;
        box-shadow: var(--shadow);

        background: white;
        /* border-radius: 1rem; */
        /* padding: 2rem; */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        animation: fadeInUp 0.7s ease-in-out;
    }

    .form-section input,
    .form-section select {
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
    }

    .form-section input:focus,
    .form-section select:focus {
        border-color: #4e73df;
        outline: none;
        box-shadow: 0 0 0 0.1rem rgba(78, 115, 223, 0.25);
    }

    .form-section label {
        font-weight: 500;
        margin-bottom: 0.4rem;
    }

    label {
        font-size: 0.95rem;
        color: var(--text-dark);
    }

    .form-control {
        color: var(--text-dark);
        /* padding: 0.55rem 1rem; */
        border: 1.5px solid #e0e0e0;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        box-shadow: none;
        transition: all 0.25s ease-in-out;
    }

    .button-submit {
        padding: 0.8rem 1.6rem;
        background: var(--primary);
        border: none;
        border-radius: 0.6rem;
        color: white;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 14px rgba(78, 115, 223, 0.25);
    }

    .button-submit:hover {
        background: var(--primary-dark);
    }




    .btn-primary {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: 1rem;
        /* padding: 0.75rem 1.5rem; */
        transition: background 0.3s ease;

        border-radius: 0.75rem;

        text-transform: capitalize;
        padding: 0.6rem 1.25rem;
        box-shadow: 0 4px 14px rgba(78, 115, 223, 0.25);

        background-color: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
    }

    .dotlottie-player {
        flex-shrink: 0;
        /* margin-right: 0.5rem; */
        position: absolute;
        right: -20px;
        top: -10px;
        z-index: 10;
    }


    .card {
        /* border-radius: 0.75rem; */
        overflow: hidden;
        /* transition: transform 0.2s ease-in-out; */

        border-radius: 1rem;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        background-color: #ffffff;
    }

    .card-title {
        color: var(--text-dark);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
    }

    table th {
        background-color: var(--light-bg);
    }

    .table th,
    .table td {
        border: none;
        background-color: transparent;
    }

    .table th {
        color: var(--text-dark);
        font-weight: 600;
    }

    .card:hover {
        transform: scale(1.02) translateY(-4px);
        /* box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); */
        /* transform: translateY(-4px); */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        cursor: pointer;
    }



    /* .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    } */

    .card-body {
        padding: 1rem;
        font-size: 0.9rem;
    }


    input[name="search"] {
        /* padding: 0.75rem; */
        /* border-radius: 0.5rem; */
        /* border: 1px solid #ced4da; */
        font-size: 0.95rem;
        /* transition: all 0.3s; */


        border: 1.5px solid #d1d5db;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        /* font-size: 0.95rem; */
        background-color: #fff;
        transition: all 0.25s ease-in-out;
    }

    input[name="search"]:focus {
        border-color: #4e73df var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);

        /* border-color: var(--primary); */
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.15);
    }

    table {
        font-size: 0.95rem;
    }

    table th {
        background-color: #f1f4f9;
        font-weight: 600;
        text-align: left;
        padding: 0.75rem;
    }

    table td {
        padding: 0.75rem;
        vertical-align: middle;
    }

    button:active {
        transform: scale(0.97);
    }

    .form-section {
        animation: fadeInUp 0.7s ease-in-out;
    }

    .bg-white {
        background-color: var(--light-bg) !important;
    }

    .btn-outline-primary {
        border-color: var(--primary);
        color: var(--primary);
        border-width: 2px;
        font-weight: 600;
        padding: 0.6rem 1.2rem;

        border-radius: 0.75rem;
        /* background-color: #fff; */
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: var(--primary);
        color: white;
        background-color: #4e73df;
        /* color: white; */
    }

    input,
    select,
    .btn {
        transition: all 0.3s ease-in-out;
    }

    .btn,
    .button-submit {
        transition: all 0.3s ease;
    }

    .btn:active,
    .button-submit:active {
        transform: scale(0.98);
        opacity: 0.9;
    }

    /* Reset Bootstrap feel */
    input.form-control,
    select.form-control,
    textarea.form-control {
        background-color: #fff !important;
        border-radius: 0.75rem;
        border: 1.5px solid #d4d4d4;
        box-shadow: none;
        font-family: 'Inter', sans-serif;
        color: var(--text-dark);
        font-size: 0.95rem;

        /* border: 1.5px solid #e0e0e0; */
        /* border-radius: 0.75rem; */
        padding: 0.75rem 1rem;
        /* font-size: 0.95rem; */
        /* box-shadow: none; */
        transition: all 0.25s ease-in-out;
        background-color: white !important;
        cursor: pointer;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.15);
        background-color: #fdfdfd;
    }


    input.form-control:focus,
    select.form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.2);
    }

    input[type="text"],
    input[type="email"],
    select {
        appearance: none;
        -webkit-appearance: none;
        background-image: none !important;
    }

    input[name="search"] {
        border: 1.5px solid #d1d5db;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background-color: #fff;
        transition: all 0.25s ease-in-out;
    }

    input[name="search"]:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.15);
    }



    .stock-container {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        animation: fadeInUp 0.7s ease-in-out;
    }

    .stock-title {
        display: flex;
        align-items: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
    }

    .card-custom {
        border: none;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease-in-out;
    }

    .card-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .card-custom img {
        object-fit: cover;
        height: 180px;
        border-bottom: 1px solid #f0f0f0;
    }

    .card-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: #343a40;
    }

    .card-text {
        font-size: 0.95rem;
        color: #6c757d;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    @media (max-width: 576px) {

        h2,
        h3 {
            font-size: 1.25rem;
        }

        .form-section,
        .col-md-10 {
            padding: 1.25rem;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .form-section {
            padding: 1.5rem;
        }

        h2,
        h3 {
            font-size: 1.25rem;
        }

        .btn,
        .btn-primary {
            width: 100%;
        }

        .card img {
            height: 120px !important;
        }
    }


    @media (prefers-color-scheme: dark) {
        :root {
            --light-bg: #1e1e2f;
            --text-muted: #cccccc;
        }
    }

    .custom-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        font-weight: 500;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        animation: fadeInDown 0.5s ease;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    .alert-success {
        background-color: #e6f4ea;
        color: #256029;
        border: 1px solid #a7d7b3;
    }

    .alert-error {
        background-color: #fdecea;
        color: #b71c1c;
        border: 1px solid #f5c6cb;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-animate {
        animation: fadeInUp 0.5s ease-in-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>


</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 bg-white p-4 rounded shadow form-section position-relative">
                <?php if (isset($_GET['success'])): ?>
                <!-- <div class="custom-alert alert-success"> -->
                <div class="custom-alert alert-success" id="success-alert">
                    <dotlottie-player src="https://lottie.host/517ff1c9-6434-4372-aec4-62cbf0f63e9b/RPqJgq3gQP.lottie"
                        background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay>
                    </dotlottie-player>
                    ✅ Berhasil mengirimkan pesanan. Terima kasih!
                </div>
                <script>
                const alertAudio = new Audio(
                    'https://assets.mixkit.co/sfx/preview/mixkit-positive-interface-beep-221.mp3');
                alertAudio.play();
                setTimeout(() => {
                    document.getElementById('success-alert').style.display = 'none';
                }, 5000);
                </script>
                <?php endif; ?>
                <h2 class="mb-4">Form Pemesanan Cetak Dokumen</h2>
                <form action="upload.php" method="POST" enctype="multipart/form-data" class="row g-3">
                    <div class="col-md-6">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>No HP</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Alamat</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Jenis Cetak</label>
                        <select name="print_type" class="form-control">
                            <option value="hitam_putih">Hitam Putih</option>
                            <option value="warna">Warna</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Ukuran Kertas</label>
                        <select name="paper_size" class="form-control">
                            <option value="A4">A4</option>
                            <option value="F4">F4</option>
                            <option value="A3">A3</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Jenis Jilid</label>
                        <select name="binding" class="form-control">
                            <option value="tidak">Tidak</option>
                            <option value="spiral">Spiral</option>
                            <option value="jilid_lipat">Jilid Lipat</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Antar-Jemput</label><br>
                        <input type="checkbox" name="delivery" value="1"> Ya, saya ingin antar-jemput
                    </div>
                    <div class="col-md-6">
                        <label>Metode Pembayaran</label>
                        <select name="payment_method" class="form-control">
                            <option value="qris">QRIS</option>
                            <option value="e-wallet">E-Wallet</option>
                            <option value="cod">COD</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Upload File</label>
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <dotlottie-player
                                src="https://lottie.host/517ff1c9-6434-4372-aec4-62cbf0f63e9b/RPqJgq3gQP.lottie"
                                background="transparent" speed="1" style="width: 40px; height: 40px;" loop autoplay>
                            </dotlottie-player>
                            Kirim Pesanan
                        </button>
                    </div>
                    <!-- <div class="custom-alert alert-success">
                        <dotlottie-player
                            src="https://lottie.host/517ff1c9-6434-4372-aec4-62cbf0f63e9b/RPqJgq3gQP.lottie"
                            background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay>
                        </dotlottie-player>
                        ✅ Berhasil mengirimkan pesanan. Terima kasih!
                    </div> -->


                </form>
                <script>
                // Jika URL mengandung ?success=1, hapus parameter setelah 5 detik
                if (window.location.search.includes('success=1')) {
                    setTimeout(() => {
                        const url = new URL(window.location);
                        url.searchParams.delete('success');
                        window.history.replaceState({}, document.title, url
                            .pathname); // update URL tanpa reload
                    }, 5000); // sama seperti durasi auto-close alert
                }
                </script>

            </div>
            <div class="col-md-10 bg-white p-4 rounded shadow">
                <h2 class="mb-4">🔍 Berkas</h2>
                <p>cari file punya anda disini...</p>
                <!-- 🔍 Form Pencarian -->
                <form method="GET" class="row g-2 mb-4">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control"
                            placeholder="Masukkan Nama / Email / No HP..." value="<?= $_GET['search'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary w-100">Cari</button>
                    </div>
                </form>

                <?php
$o = null;

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $keyword = $_GET['search'];
    $sql = "SELECT * FROM orders 
            JOIN users ON orders.user_id = users.id 
            WHERE users.name LIKE '%$keyword%' 
               OR users.email LIKE '%$keyword%' 
               OR users.phone LIKE '%$keyword%' 
            ORDER BY orders.created_at DESC 
            LIMIT 1";

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $o = $result->fetch_assoc();
    }
} elseif (isset($_GET['show_updated']) && isset($_SESSION['updated_order'])) {
    $o = $_SESSION['updated_order'];
}

if ($o):
$delivery = $o['delivery'] == '1' ? 'Ya' : 'Tidak';
$harga = isset($o['total_price']) ? number_format($o['total_price'], 0, ',', '.') : '-';
?>

                <hr>
                <h4 class="mt-4">
                    <?= isset($_GET['search']) ? '🔍 Hasil Pencarian' : '✅ Pesanan Terbaru yang Diperbarui' ?></h4>
                <table class="table table-bordered mt-3">
                    <tr>
                        <th>Nama</th>
                        <td><?= $o['name'] ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $o['email'] ?></td>
                    </tr>
                    <tr>
                        <th>File</th>
                        <td><?= $o['file_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Jenis Cetak</th>
                        <td><?= $o['print_type'] ?></td>
                    </tr>
                    <tr>
                        <th>Ukuran Kertas</th>
                        <td><?= $o['paper_size'] ?></td>
                    </tr>
                    <tr>
                        <th>Jilid</th>
                        <td><?= $o['binding'] ?></td>
                    </tr>
                    <tr>
                        <th>Antar</th>
                        <td><?= $delivery ?></td>
                    </tr>
                    <tr>
                        <th>Pembayaran</th>
                        <td><?= $o['payment_method'] ?></td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>Rp<?= $harga ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><?= $o['status'] ?></td>
                    </tr>
                    <tr>
                        <th>Waktu</th>
                        <td><?= $o['created_at'] ?></td>
                    </tr>
                </table>

                <?php if (!isset($_GET['search'])) unset($_SESSION['updated_order']); endif; ?>
                <div class="container py-5">
                    <div class=" stock-container bg-white p-4 rounded shadow mt-5 bg-white p-4 rounded shadow mt-5">
                        <h3 class="mb-4 stock-title">📦 Lihat Ketersediaan Barang Stok</h3>
                        <div class="mb-3">
                            <input type="text" id="filterInput" class="form-control"
                                placeholder="Filter berdasarkan nama/kategori...">
                        </div>
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="stokGrid">
                            <?php while ($stok = $stok_result->fetch_assoc()): ?>
                            <div class="col stok-item" data-item="<?= strtolower($stok['item']) ?>">
                                <div class="card h-100 shadow-sm border-0 card-animate">
                                    <?php if ($stok['gambar']): ?>
                                    <img src="uploads/<?= $stok['gambar'] ?>" class="card-img-top"
                                        alt="<?= $stok['item'] ?>" style="object-fit: cover; height: 150px;" />
                                    <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                        style="height: 150px;">
                                        <span class="text-muted">Tidak ada gambar</span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $stok['item'] ?></h5>
                                        <p class="card-text">Jumlah tersedia: <strong><?= $stok['jumlah'] ?></strong>
                                        </p>
                                        <button class="btn btn-primary w-100 btnPesan" data-item="<?= $stok['item'] ?>"
                                            data-jumlah="<?= $stok['jumlah'] ?>">
                                            Pesan
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <script>
                // 🔍 Filter
                $(document).on("keyup", "#filterInput", function() {
                    const val = $(this).val().toLowerCase();
                    $(".stok-item").each(function() {
                        const item = $(this).data("item").toLowerCase();
                        $(this).toggle(item.includes(val));
                    });
                });

                // // 🎯 Event pesan
                // $(document).on("click", ".btnPesan", function() {
                //     const item = $(this).data("item");
                //     const maxJumlah = parseInt($(this).data("jumlah"));

                //     const qty = prompt("Masukkan jumlah yang ingin dipesan untuk " + item + ":", 1);
                //     if (qty !== null && !isNaN(qty) && qty > 0) {
                //         if (qty > maxJumlah) {
                //             window.location.href = `penawaran.php?item=${item}&jumlah=${qty}&status=overstock`;
                //         } else {
                //             window.location.href = `penawaran.php?item=${item}&jumlah=${qty}&status=ok`;
                //         }
                //     } else {
                //         alert("Jumlah tidak valid.");
                //     }
                // });
                </script>
                <!-- <script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.querySelectorAll(".btnPesan").forEach(function(button) {
                        button.addEventListener("click", function() {
                            const item = this.dataset.item;
                            const stok = parseInt(this.dataset.jumlah);

                            Swal.fire({
                                title: 'Masukkan jumlah pesanan',
                                input: 'number',
                                inputLabel: `Stok tersedia: ${stok}`,
                                inputPlaceholder: 'Contoh: 10',
                                showCancelButton: true,
                                confirmButtonText: 'Lanjutkan',
                                cancelButtonText: 'Batal',
                                inputAttributes: {
                                    min: 1,
                                    max: stok,
                                    step: 1
                                }
                            }).then((result) => {
                                if (result.isConfirmed && result.value) {
                                    const jumlah = parseInt(result.value);
                                    const status = jumlah > stok ? 'gagal' : 'ok';
                                    window.location.href =
                                        `penawaran.php?item=${encodeURIComponent(item)}&jumlah=${jumlah}&status=${status}`;
                                }
                            });
                        });
                    });
                });
                </script> -->

                <!-- <script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.querySelectorAll(".btnPesan").forEach(function(button) {
                        button.addEventListener("click", function() {
                            const item = this.dataset.item;
                            const stok = parseInt(this.dataset.jumlah);

                            Swal.fire({
                                title: 'Masukkan jumlah pesanan',
                                input: 'number',
                                inputLabel: `Stok tersedia: ${stok}`,
                                inputPlaceholder: 'Contoh: 10',
                                showCancelButton: true,
                                confirmButtonText: 'Lanjutkan',
                                cancelButtonText: 'Batal',
                                inputAttributes: {
                                    min: 1,
                                    step: 1,
                                    max: stok > 0 ? stok :
                                        99999 // Biar gak error kalau stok 0
                                },
                                inputValidator: (value) => {
                                    if (!value || parseInt(value) <= 0) {
                                        return 'Masukkan jumlah valid (min. 1)';
                                    }
                                    return null;
                                }
                            }).then((result) => {
                                if (result.isConfirmed && result.value) {
                                    const jumlah = parseInt(result.value);
                                    let status = "ok";
                                    if (stok === 0 || jumlah > stok) {
                                        status =
                                            "penawaran"; // Untuk stok 0 atau overstock
                                    }

                                    const url =
                                        `penawaranover.php?item=${encodeURIComponent(item)}&jumlah=${jumlah}&status=${status}`;
                                    window.location.href = url;
                                }
                            });
                        });
                    });
                });
                </script> -->

                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.querySelectorAll(".btnPesan").forEach(function(button) {
                        button.addEventListener("click", function() {
                            const item = this.dataset.item;
                            const stok = parseInt(this.dataset.jumlah);

                            Swal.fire({
                                title: 'Masukkan jumlah pesanan',
                                input: 'number',
                                inputLabel: `Stok tersedia: ${stok}`,
                                inputPlaceholder: 'Contoh: 10',
                                showCancelButton: true,
                                confirmButtonText: 'Lanjutkan',
                                cancelButtonText: 'Batal',
                                inputAttributes: {
                                    min: 1,
                                    // max: stok,
                                    max: stok > 0 ? stok :
                                    99999, // Biar gak error kalau stok 0
                                    step: 1
                                }
                            }).then((result) => {
                                if (result.isConfirmed && result.value) {
                                    const jumlah = parseInt(result.value);
                                    let status = "ok";
                                    let file = "penawaran.php";
                                    if (stok === 0 || jumlah > stok) {
                                        status = "penawaran";
                                        file =
                                            "penawaranover.php"; // <-- arahkan ke file khusus penawaran
                                    }

                                    window.location.href =
                                        `${file}?item=${encodeURIComponent(item)}&jumlah=${jumlah}&status=${status}`;

                                }
                            });
                        });
                    });
                });
                </script>



            </div>
        </div>

</body>

</html>