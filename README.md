## config.php (Konfigurasi Database)
File ini berfungsi sebagai tempat penyimpanan data sensitif dan konfigurasi utama aplikasi.

Tujuan: Menyimpan kredensial database dalam variabel array asosiatif bernama `$config`.

### Isi:

`'host'`: Alamat server database (biasanya `localhost`).

`'username'`: Username untuk login ke MySQL (default XAMPP/WAMP biasanya `root`).

`'password'`: Password database (kosongkan jika menggunakan default XAMPP).

`'db_name'`: Nama database yang akan digunakan (`latihan_oop`).

## index.php (Gateway / Front Controller)

Ini adalah file utama yang akan dijalankan pertama kali saat user membuka website. File ini mengatur logika routing (pemetaan URL ke file).

Berikut adalah bedah alur kodenya:

### A. persiapan setup

```php
include "config.php";
include "class/Database.php";
include "class/Form.php";

session_start();
```
Dependencies: Memuat file konfigurasi dan class-class penting (`Database` dan `Form`) agar bisa digunakan di seluruh aplikasi.

Session:  `session_start()` memulai sesi pengguna, yang berguna untuk fitur login atau menyimpan data keranjang belanja sementara.

### B. Logika Routing (Penentu Halaman)

Bagian ini adalah "otak" dari navigasi website Anda:

1. Menangkap URL (`$path`): Kode memeriksa apakah ada informasi tambahan di URL (misalnya `index.php/artikel/tambah`). Jika tidak ada, sistem akan menggunakan rute default `/artikel/index`.

2. Memecah URL (`explode`):

```php
$segments = explode('/', trim($path, '/'));
```

Fungsi ini memecah alamat URL berdasarkan garis miring /.

Contoh URL: `/barang/edit`  

Hasil `$segments`: Array `['barang', 'edit']`

3. Menentukan Modul dan Halaman:

```php
$mod  = $segments[0] ?? 'artikel'; // Segmen pertama jadi nama folder (modul)
$page = $segments[1] ?? 'index';   // Segmen kedua jadi nama file`
```

`$mod`: Menentukan folder modul mana yang akan dibuka.

`$page`: Menentukan file aksi apa yang dibuka (misal: `index, create, store`).

### c. Menyusun Halaman (Templating)

```php
$file = "module/$mod/$page.php";

include "template/header.php";

if (file_exists($file)) {
    include $file;
} else {
    echo "<p>Modul tidak ditemukan: $mod/$page</p>";
}

include "template/footer.php";
```

1. Konstruksi Path: Kode menyusun alamat file dinamis. Contohnya menjadi: `module/artikel/index.php`.

2. Layout Konsisten: `header.php` (bagian atas web) dan `footer.php` (bagian bawah web) selalu dimuat di setiap halaman, sehingga tampilan web konsisten.

3. Dynamic Loading: Kode mengecek (`file_exists`), apakah file modul yang diminta user ada?

* Jika ada: Konten file tersebut dimuat di antara header dan footer.

* Jika tidak ada: Menampilkan pesan error sederhana bahwa modul tidak ditemukan.

## header.php (Bagian Atas Halaman)

File ini bertugas membuka struktur halaman web.

* Deklarasi HTML: Membuka tag `<html>`, `<head>`, dan `<body>`.

* Memuat CSS: Baris `<link rel="stylesheet"...>` menghubungkan halaman dengan file desain (`style.css`) yang berada di folder `/lab11_php_oop/assets/css/`.

* Wadah Utama (`Container`):

```php
<div class="container">
```

Perhatikan bahwa `div` ini dibuka tetapi belum ditutup. Ini sengaja, karena "isi" halaman akan dimuat setelah baris ini, sehingga isi halaman akan berada di dalam kotak `container`.

* Navigasi (`nav`): Membuat menu "Home" dan "Artikel" agar pengguna bisa berpindah halaman. Linknya mengarah ke rute yang ditangani oleh `index.php` Anda sebelumnya.

## footer.php (bagian bawah halaman)

File ini bertugas menutup struktur halaman yang dibuka oleh header.

* menutup wadah:

  ```html
  </div>
  ```

Tag penutup `</div>` ini adalah pasangan dari `<div class="container">` yang ada di `header.php`. Ini menandakan batas akhir area konten utama.

* Footer Info: Menampilkan informasi hak cipta (Copyright) di bagian paling bawah.
* Menutup HTML: Menutup tag `</body>` dan `</html>`, menandakan bahwa kode HTML halaman tersebut telah selesai.

## tambah.php (Create - Menambah Data)

File ini menangani proses pembuatan artikel baru menggunakan bantuan class Form (library tambahan yang Anda sertakan di awal).

* Logika Form Helper: Kode tidak menulis tag HTML `<form>` secara manual. Sebaliknya, ia menggunakan objek `$form`:
    * `$form = new Form(...)`: Membuka form baru
    * `$form->addField(...)`: Menambahkan input judul dan textarea isi secara otomatis.
    * `$form->displayForm()`: Mencetak kode HTML form ke layar.
* Logika Penyimpanan (`if ($_POST)`): Blok ini hanya berjalan ketika tombol "Simpan" ditekan (metode POST):
    1. Membuat koneksi database baru (`new Database`).
    2. `$db->insert(...)`: Mengirim data `judul` dan `isi` ke tabel `artikel`.
    3. `header(...)`: Jika berhasil, user langsung dialihkan (redirect) kembali ke halaman daftar artikel.

## index.php (Read - Menampilkan Daftar)
File ini adalah halaman utama modul artikel yang berfungsi menampilkan tabel semua data yang ada di database.
* mengambil data
    * `$db->query("SELECT * FROM artikel")`: Meminta semua data dari tabel artikel.
* Tombol Tambah: Menampilkan link menuju halaman `tambah.php` untuk membuat artikel baru.
* Looping (Perulangan):

  ```php
  while ($row = $data->fetch_assoc()):
  ```
Kode ini mengambil data baris demi baris dari database. Selama datanya masih ada, kode HTML di dalamnya (baris tabel `<tr>`) akan terus dicetak berulang-ulang untuk menampilkan Judul dan Isi artikel.

## ubah.php (Update - Mengedit Data)

File ini sedikit lebih kompleks karena harus mengambil data lama terlebih dahulu sebelum mengubahnya. Perhatikan bahwa file ini menggunakan HTML manual, berbeda dengan `tambah.php` yang menggunakan class `Form`.

* pengecekan id:
  ```php
  $id = $_GET['id'] ?? null;
  ```
  Mengambil ID dari URL (contoh: `ubah.php?id=5`). Jika tidak ada ID, sistem akan berhenti dan menampilkan pesan error.

  * Mengambil Data Lama: `$artikel = $db->get(...)`: Mengambil data artikel spesifik berdasarkan ID tadi agar kolom input bisa diisi (pre-fill) dengan data lama.
 
  * Form Edit: Perhatikan atribut `value` pada input:
      * `value="<?= $artikel['judul']; ?>"`: Ini membuat kolom judul otomatis terisi dengan judul yang lama, sehingga user tinggal mengeditnya.
* Logika Update `(if ($_POST)`): Saat tombol "Update Artikel" ditekan:
    1. `$db->update(...)`: Mengupdate data di database berdasarkan ID yang sedang diedit.
    2. `header(...)`: Mengalihkan user kembali ke halaman index setelah selesai.
 
## database.php (Pengelola Koneksi & Data)

Class ini berfungsi sebagai pembungkus (wrapper) untuk fungsi-fungsi database MySQLi. Tujuannya agar Anda tidak perlu menulis kode koneksi atau query SQL yang panjang berulang-ulang di setiap halaman.

* Otomatis Terkoneksi (`__construct` & `getConfig`) Saat Anda memanggil `new Database()`, kode ini otomatis:
    1. Membaca file `config.php` (yang Anda kirim paling awal).
    2. Mengambil username, password, dan nama database.
    3. Membuka koneksi ke database MySQL. Jika gagal, akan muncul pesan "Connection failed".

* Fungsi `query($sql)` Ini adalah fungsi dasar untuk menjalankan perintah SQL mentah secara manual.
    * Contoh penggunaan: `$db->query("SELECT * FROM artikel")`.
* Fungsi `get($table, $where)` Fungsi praktis untuk mengambil satu baris data saja.
    * Otomatis menyusun query `SELECT * FROM table WHERE ...`.
    * Berguna saat Anda ingin mengedit data (mengambil data lama berdasarkan ID).

 * Fungsi `insert($table, $data)` Fungsi pintar untuk menambah data baru tanpa menulis `SQL INSERT INTO...` secara manual.

 * Fungsi update `($table, $data, $where)` Fungsi untuk mengubah data yang sudah ada
    * Sama seperti insert, ia menerima Array data baru.
    * Ia akan mengubah array tersebut menjadi format `kolom='isi'` untuk perintah SQL `UPDATE`.

## form.php (Pembuat Formulir Otomatis)
Class ini adalah Form Generator. Fungsinya untuk membuat kode HTML `<form>` secara otomatis menggunakan PHP, sehingga Anda tidak perlu mengetik tag HTML `<input>`, `<label>`, atau `<table>` satu per satu.

* Menampung Data (`addField`) Fungsi ini merekam kolom apa saja yang ingin ditampilkan di form.
    * Anda bisa menentukan: Nama input, Label (tulisan di samping input), Tipe input (text, password, textarea, dll), dan Opsi (untuk radio/checkbox/select).
    * emua definisi ini disimpan dulu dalam array `$fields`.

* Mencetak Tampilan (`displayForm`) Setelah semua field ditambahkan, fungsi ini dipanggil untuk "mencetak" form ke layar browser.
    * Layout Tabel: Class ini menyusun form menggunakan tabel (<table>) agar label di kiri dan input di kanan terlihat rapi dan lurus.
    * Switch Case: Kode mendeteksi tipe input:
        * Jika `textarea`, buat kotak teks besar.
        * Jika `select`, buat menu dropdown (looping opsi).
        * Jika `radio/checkbox`, buat pilihan bulatan/centang.
        * Jika `password`, buat input yang teksnya tersembunyi.
        * Jika tidak ditentukan, defaultnya adalah `text` biasa.
      
