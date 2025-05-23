// bot.js
const venom = require('venom-bot');

// venom
//   .create()
//   .then((client) => start(client))
//   .catch((erro) => {
//     console.log('Gagal start bot: ', erro);
//   });
venom.create({
    session: 'my-session', // Nama sesi
    headless: false, // Menjalankan browser dalam mode normal (non-headless)
    multiDevice: true, // Dukungan multi perangkat (untuk WhatsApp Web)
    folderNameToken: './tokens', // Tentukan folder untuk token sesi
})
  .then((client) => start(client))
  .catch((erro) => {
    console.log('Gagal start bot: ', erro);
  });


// function start(client) {
//   client.onMessage(async (message) => {
//     if (message.isGroupMsg === false) {
//       const msg = message.body.toLowerCase();

//       switch (msg) {
//         case '1':
//           await client.sendText(message.from, '📦 Info Pemesanan:\nKami buka setiap hari pukul 08.00 - 21.00. Untuk pemesanan silakan ketik *PESAN*.');
//           break;
//         case '2':
//           await client.sendText(message.from, '⏱ Status Pesanan:\nSilakan kirimkan nomor invoice atau nama Anda.');
//           break;
//         case '3':
//           await client.sendText(message.from, '🆘 Bantuan:\nSilakan hubungi admin di nomor +62857xxxxxxx untuk bantuan lebih lanjut.');
//           break;
//         case 'pesan':
//           await client.sendText(message.from, 'Silakan isi format pemesanan:\n\nNama:\nMenu:\nJumlah:\nAlamat:\n\nContoh:\nNama: Gilang\nMenu: Kopi Gula Aren\nJumlah: 2\nAlamat: Jl. Anggrek No. 7');
//           break;
//         default:
//           await client.sendText(message.from, 'Halo! 👋\nKetik angka berikut untuk melanjutkan:\n\n1. Info Pemesanan\n2. Status Pesanan\n3. Bantuan');
//           break;
//       }
//     }
//   });
// }

function start(client) {
  client.onMessage(async (message) => {
    if (message.isGroupMsg === false) {
      const msg = message.body.toLowerCase();

      if (msg.includes('nama:') && msg.includes('menu:') && msg.includes('jumlah:') && msg.includes('alamat:')) {
        // Proses data pemesanan
        let name = msg.match(/nama:\s*(.*)/i)[1];
        let menu = msg.match(/menu:\s*(.*)/i)[1];
        let jumlah = msg.match(/jumlah:\s*(.*)/i)[1];
        let alamat = msg.match(/alamat:\s*(.*)/i)[1];

        await client.sendText(message.from, `Terima kasih, ${name}! Pemesanan Anda:\nMenu: ${menu}\nJumlah: ${jumlah}\nAlamat: ${alamat}\nAkan segera diproses.`);
      } else {
        await client.sendText(message.from, 'Halo! 👋\nKetik angka berikut untuk melanjutkan:\n\n1. Info Pemesanan\n2. Status Pesanan\n3. Bantuan');
      }
    }
  });
}

