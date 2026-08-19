# Sistem Manajemen Kos - CodeIgniter 4

Aplikasi CRUD sederhana untuk mengelola kos-kosan: **Kamar**, **Penyewa**, dan **Pembayaran**, lengkap dengan login admin.

---

## 1. Persiapan Environment

**Kebutuhan:**
- PHP >= 8.2 (dengan ekstensi: intl, mbstring, mysqli)
- Composer
- MySQL / MariaDB

**Langkah instalasi:**

```bash
# 1. Masuk ke folder project
cd kos-app

# 2. Install dependency lewat composer (menghasilkan folder vendor/)
composer install

# 3. Buat database MySQL bernama "kos_management" (lewat phpMyAdmin/CLI)
mysql -u root -p -e "CREATE DATABASE kos_management"

# 4. File .env sudah disiapkan, cek/sesuaikan bagian database jika perlu:
#    database.default.hostname = localhost
#    database.default.database = kos_management
#    database.default.username = root
#    database.default.password =

# 5. Jalankan migration untuk membuat semua tabel
php spark migrate

# 6. Jalankan seeder untuk membuat akun admin default
php spark db:seed UserSeeder

# 7. Jalankan server development
php spark serve
```

Buka `http://localhost:8080`, login dengan:
- **Username:** `admin`
- **Password:** `admin123`

---

## 2. Struktur Folder & Penjelasan

CodeIgniter 4 menganut pola **MVC (Model - View - Controller)**. Semua kode yang kita buat ada di folder `app/`.

```
kos-app/
├── app/
│   ├── Config/
│   │   ├── Routes.php          <- Daftar alamat URL & controller tujuannya
│   │   ├── Filters.php         <- Daftar "penjaga pintu" (middleware), termasuk filter auth
│   │   └── Database.php        <- Konfigurasi koneksi database
│   ├── Controllers/
│   │   ├── BaseController.php  <- Induk semua controller (load helper form & url)
│   │   ├── Auth.php            <- Login & logout
│   │   ├── Dashboard.php       <- Ringkasan statistik
│   │   ├── Kamar.php           <- CRUD data kamar
│   │   ├── Penyewa.php         <- CRUD data penyewa
│   │   └── Pembayaran.php      <- CRUD data pembayaran
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── KamarModel.php
│   │   ├── PenyewaModel.php
│   │   └── PembayaranModel.php
│   ├── Filters/
│   │   └── AuthFilter.php      <- Cek apakah user sudah login sebelum akses halaman
│   ├── Views/
│   │   ├── layout/main.php     <- Kerangka HTML (sidebar, navbar) yang dipakai berulang
│   │   ├── auth/login.php
│   │   ├── dashboard/index.php
│   │   ├── kamar/ (index, create, edit)
│   │   ├── penyewa/ (index, create, edit)
│   │   └── pembayaran/ (index, create, edit)
│   └── Database/
│       ├── Migrations/         <- "Cetakan" struktur tabel (versi terkontrol)
│       └── Seeds/               <- Data awal (akun admin default)
├── public/
│   └── index.php               <- Satu-satunya pintu masuk semua request (front controller)
├── system/                     <- Kode inti framework CodeIgniter 4 (jangan diedit)
└── .env                        <- Konfigurasi environment (database, base URL, dll)
```

### Alur request (bagaimana satu klik menjadi tampilan halaman)

```
Browser -> public/index.php -> Routes.php (menentukan Controller mana yang dipanggil)
        -> Filters (AuthFilter mengecek session login)
        -> Controller (mengambil/menyimpan data lewat Model)
        -> Model (berkomunikasi dengan database)
        -> View (menampilkan HTML hasil olahan Controller)
        -> Browser menampilkan halaman
```

---

## 3. Penjelasan Tiap Bagian

### a. Migration (`app/Database/Migrations/`)
Migration adalah cara membuat/mengubah struktur tabel database lewat **kode PHP**, bukan lewat klik manual di phpMyAdmin. Keuntungannya: struktur database bisa di-share lewat Git dan dijalankan ulang di komputer lain dengan satu perintah (`php spark migrate`).

Contoh potongan dari `CreateKamar.php`:
```php
$this->forge->addField([
    'nomor_kamar' => ['type' => 'VARCHAR', 'constraint' => 10, 'unique' => true],
    'status'      => ['type' => 'ENUM', 'constraint' => ['kosong', 'terisi'], 'default' => 'kosong'],
]);
$this->forge->createTable('kamar');
```
`up()` dijalankan saat migration diterapkan, `down()` dijalankan saat migration dibatalkan (`php spark migrate:rollback`).

**Relasi antar tabel:**
- `penyewa.kamar_id` -> foreign key ke `kamar.id`
- `pembayaran.penyewa_id` -> foreign key ke `penyewa.id`

### b. Model (`app/Models/`)
Model adalah lapisan yang berkomunikasi langsung dengan tabel database. Setiap Model mewarisi class `CodeIgniter\Model` bawaan CI4, sehingga otomatis dapat method `find()`, `insert()`, `update()`, `delete()`, `findAll()`, dll — tanpa perlu menulis query SQL manual untuk operasi dasar.

Contoh (`KamarModel.php`):
```php
protected $table         = 'kamar';        // nama tabel yang diwakili
protected $allowedFields = [...];           // kolom yang boleh diisi lewat insert/update
protected $validationRules = [...];         // validasi otomatis dijalankan saat insert()/update()
```

Model juga bisa punya method custom, misalnya `getKamarKosong()` di `KamarModel` untuk mengambil kamar yang statusnya masih kosong — dipakai saat menampilkan pilihan kamar di form tambah penyewa.

`PenyewaModel` dan `PembayaranModel` punya method `getPenyewaWithKamar()` / `getPembayaranWithPenyewa()` yang melakukan **JOIN** antar tabel, supaya di halaman daftar bisa langsung menampilkan nama kamar / nama penyewa tanpa query terpisah untuk tiap baris (menghindari masalah performa N+1 query).

### c. Controller (`app/Controllers/`)
Controller adalah "otak" yang menerima request dari user, memanggil Model untuk ambil/simpan data, lalu mengirim data itu ke View. Pola CRUD yang dipakai konsisten di `Kamar.php`, `Penyewa.php`, `Pembayaran.php`:

| Method     | Fungsi                                             | Route                        |
|------------|-----------------------------------------------------|-------------------------------|
| `index()`  | Tampilkan semua data (READ)                         | GET `/kamar`                  |
| `create()` | Tampilkan form tambah data                           | GET `/kamar/create`           |
| `store()`  | Simpan data baru ke database (CREATE)                | POST `/kamar/store`           |
| `edit($id)`| Tampilkan form edit berisi data lama                 | GET `/kamar/edit/5`           |
| `update($id)` | Simpan perubahan data (UPDATE)                    | POST `/kamar/update/5`        |
| `delete($id)` | Hapus data (DELETE)                               | GET `/kamar/delete/5`         |

Logika bisnis tambahan juga ditaruh di sini, misalnya di `Penyewa.php`: ketika penyewa baru disimpan, status kamar terkait otomatis diubah jadi `terisi`; ketika penyewa dihapus, kamar otomatis kembali `kosong`.

### d. View (`app/Views/`)
View adalah file `.php` berisi HTML yang dicampur sedikit kode PHP untuk menampilkan data dari Controller. Semua halaman (kecuali login) memakai **template inheritance** CI4:

```php
<?= $this->extend('layout/main') ?>   // "warisi" kerangka HTML dari layout/main.php
<?= $this->section('content') ?>       // mulai area konten yang akan disisipkan
    ... isi halaman ...
<?= $this->endSection() ?>             // akhiri area konten
```
Ini membuat sidebar, navbar, dan pesan notifikasi (flash message) hanya perlu ditulis SEKALI di `layout/main.php`, lalu dipakai ulang oleh semua halaman. `esc()` dipakai untuk mencegah XSS saat menampilkan data dari database.

### e. Routes (`app/Config/Routes.php`)
Routes memetakan URL ke Controller & method. Semua route yang butuh login (kamar, penyewa, pembayaran, dashboard) dibungkus dalam group dengan `filter => 'auth'`:
```php
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/kamar', 'Kamar::index');
    // ...
});
```
Artinya sebelum method `Kamar::index()` dijalankan, `AuthFilter` akan dicek dulu.

### f. Filter (`app/Filters/AuthFilter.php`)
Filter adalah kode yang berjalan **sebelum** (atau sesudah) Controller diakses — semacam satpam penjaga pintu. `AuthFilter` mengecek apakah session `logged_in` ada; kalau tidak ada, user dilempar paksa ke halaman login.

### g. Autentikasi (`Auth.php`)
- Password admin **tidak** disimpan polos, tapi di-hash pakai `password_hash()` (lihat `UserSeeder.php`) dan dicocokkan dengan `password_verify()` saat login — praktik standar keamanan, bukan menyimpan password asli.
- Setelah login sukses, data user (kecuali password) disimpan ke session PHP agar bisa diakses di halaman lain (contoh: menampilkan nama admin di navbar).

---

## 4. Tabel Database

| Tabel        | Kolom Penting                                                              |
|--------------|------------------------------------------------------------------------------|
| `users`      | username, password (hash), nama_lengkap                                      |
| `kamar`      | nomor_kamar, tipe_kamar, harga_bulanan, fasilitas, status (kosong/terisi)     |
| `penyewa`    | kamar_id (FK), nama, no_hp, email, alamat_asal, tanggal_masuk, status         |
| `pembayaran` | penyewa_id (FK), bulan, tahun, jumlah_bayar, tanggal_bayar, status, catatan   |
| `pengajuan`  | kamar_id (FK), nama, no_hp, email, pesan, status (baru/diproses/diterima/ditolak) |

---

## 5. Halaman Publik (Pengunjung)

Selain panel admin, sekarang tersedia **halaman depan (landing page)** yang bisa diakses siapa saja tanpa login, di alamat `/` (root URL).

**Yang bisa dilakukan pengunjung:**
- Melihat daftar kamar yang **berstatus kosong** — data ini diambil langsung dari tabel `kamar` yang sama dengan yang dikelola admin lewat menu "Data Kamar". Jadi begitu admin mengubah status kamar jadi "terisi", kamar itu otomatis hilang dari daftar publik — **tidak perlu sinkronisasi manual apa pun**.
- Mengisi form **"Ajukan Sewa"** pada kamar yang diminati (nama, no HP, email, pesan).

**Bagaimana data pengunjung sampai ke admin?**
1. Saat pengunjung submit form, data disimpan controller `PublicSite::ajukanSewa()` ke tabel baru `pengajuan` lewat `PengajuanModel`.
2. Admin melihat semua pengajuan yang masuk di menu sidebar **"Pengajuan Sewa"** (halaman ini dilindungi login, sama seperti menu lainnya).
3. Admin bisa mengubah status pengajuan (Baru -> Diproses -> Diterima/Ditolak) langsung dari dropdown di tabel, atau menghapusnya.
4. Sidebar admin & dashboard menampilkan badge angka merah untuk jumlah pengajuan yang masih berstatus "Baru", supaya admin tahu ada pengajuan yang perlu ditindaklanjuti.

**File baru yang terlibat:**
```
app/Controllers/PublicSite.php      <- Landing page + proses simpan form pengajuan
app/Controllers/Pengajuan.php       <- Admin: lihat & kelola pengajuan masuk
app/Models/PengajuanModel.php
app/Database/Migrations/2024-01-01-000005_CreatePengajuan.php
app/Views/layout/public.php         <- Layout khusus halaman publik (navbar simpel, tanpa sidebar admin)
app/Views/public/index.php          <- Isi landing page
app/Views/pengajuan/index.php       <- Tabel kelola pengajuan di admin
```

**Route terkait:**
```php
$routes->get('/', 'PublicSite::index');              // halaman depan (publik)
$routes->post('/ajukan-sewa', 'PublicSite::ajukanSewa'); // submit form (publik)

// di dalam group filter 'auth' (wajib login):
$routes->get('/pengajuan', 'Pengajuan::index');
$routes->post('/pengajuan/update-status/(:num)', 'Pengajuan::updateStatus/$1');
$routes->get('/pengajuan/delete/(:num)', 'Pengajuan::delete/$1');
```

Karena tabel `pengajuan` baru ditambahkan, jangan lupa jalankan migration lagi:
```bash
php spark migrate
```

---

## 6. Upload File (Foto Kamar, Bukti Bayar, Foto Identitas)

Tiga fitur upload file sudah ditambahkan:

| Fitur              | Ada di halaman                          | Kolom DB (tabel)              | Folder penyimpanan             |
|---------------------|------------------------------------------|--------------------------------|----------------------------------|
| Foto Kamar          | Tambah/Edit Kamar (admin)                 | `foto_kamar` (`kamar`)         | `public/uploads/kamar/`          |
| Bukti Pembayaran    | Tambah/Edit Pembayaran (admin)            | `bukti_bayar` (`pembayaran`)   | `public/uploads/pembayaran/`     |
| Foto Identitas      | Form "Ajukan Sewa" (halaman publik)       | `foto_identitas` (`pengajuan`) | `public/uploads/identitas/`      |

**Cara kerjanya (pola yang sama dipakai di ketiga fitur):**
1. File fisik disimpan di folder `public/uploads/<jenis>/` — folder ini ada di dalam `public/` supaya bisa diakses langsung lewat browser (misal `http://localhost:8080/uploads/kamar/abc123.jpg`).
2. Di database, kita **hanya menyimpan nama filenya saja** (bukan file utuh), contoh: `abc123.jpg`.
3. Saat upload, nama file di-random pakai `$file->getRandomName()` supaya tidak bentrok antar-file dan tidak mudah ditebak orang lain.
4. Validasi otomatis membatasi: harus berupa gambar (`is_image`), tipe file tertentu (`mime_in`), dan ukuran maksimal 2MB (`max_size`). Untuk bukti bayar, PDF juga diizinkan.
5. Semua upload bersifat **opsional** (`permit_empty`) — kalau tidak pilih file, data tetap bisa disimpan tanpa foto.
6. Saat data diedit dan admin upload file baru, file lama otomatis dihapus dari disk (lihat method `hapusFileLama()` di `Kamar.php` dan `Pembayaran.php`) supaya tidak menumpuk file yang tidak terpakai.
7. Saat data dihapus (kamar/pembayaran), file terkait juga ikut dihapus dari disk.

**Contoh potongan kode upload** (dari `KamarController`):
```php
private function uploadFotoKamar(): ?string
{
    $file = $this->request->getFile('foto_kamar');

    if (! $file || ! $file->isValid() || $file->hasMoved()) {
        return null; // tidak ada file yang diupload, lewati
    }

    $namaBaru = $file->getRandomName();          // hindari nama file bentrok
    $file->move(FCPATH . 'uploads/kamar', $namaBaru); // pindahkan ke public/uploads/kamar/

    return $namaBaru; // disimpan ke kolom foto_kamar di database
}
```

**Penting:** karena ada input file, form HTML wajib pakai atribut `enctype="multipart/form-data"` — semua form terkait (`kamar/create.php`, `kamar/edit.php`, `pembayaran/create.php`, `pembayaran/edit.php`, `public/index.php`) sudah disesuaikan.

Karena ada kolom baru, jalankan migration sekali lagi setelah update:
```bash
php spark migrate
```

---

## 7. Integrasi Pembayaran dengan Data Penyewa (Auto-fill)

Supaya admin tidak perlu ketik ulang data yang sebenarnya sudah ada di sistem, form Tambah/Edit Pembayaran sekarang **otomatis mengisi** beberapa field saat penyewa dipilih dari dropdown:

- **Info Kamar & Harga Sewa** — diambil dari data kamar milik penyewa tersebut (join `penyewa` + `kamar`)
- **Bulan & Tahun** — sistem menghitung otomatis bulan PERTAMA yang belum ada data pembayarannya untuk penyewa itu, dihitung mulai dari `tanggal_masuk` penyewa. Kalau semua bulan sampai bulan berjalan sudah lunas, sistem menyarankan bulan berikutnya.
- **Jumlah Bayar** — otomatis terisi sesuai `harga_bulanan` kamar penyewa (tetap bisa diubah manual kalau perlu, misalnya ada diskon).

Semua field ini **tetap bisa diedit manual** oleh admin — auto-fill hanya bertindak sebagai saran/starting point, bukan mengunci input.

**Bagaimana cara kerjanya (teknis):**
1. `PembayaranController::buildInfoPenyewaUntukJs()` menyiapkan data untuk SETIAP penyewa aktif: info kamar, harga, dan bulan/tahun yang disarankan — lalu dikirim ke view dalam bentuk JSON (`json_encode($infoPenyewa)`).
2. Di browser, JavaScript "mendengarkan" event `change` pada dropdown Penyewa. Begitu admin memilih penyewa, JS langsung mengisi field Kamar/Harga/Bulan/Tahun berdasarkan data JSON tadi — **tanpa reload halaman**.
3. Logika mencari "bulan pertama yang belum dibayar" ada di `PembayaranController::hitungPeriodeBelumBayar()`: mulai dari bulan `tanggal_masuk`, sistem mengecek satu-satu apakah bulan tsb sudah ada di data pembayaran penyewa itu, sampai ketemu yang belum ada.

**Contoh alur pemakaian:**
> Penyewa "Budi" masuk kos bulan Juni 2026, sudah bayar Juni & Juli. Saat admin buka form Tambah Pembayaran dan pilih "Budi", sistem otomatis menyarankan **Agustus 2026** beserta harga sewa kamarnya — admin tinggal cek tanggal bayar & upload bukti transfer.

### Mencegah Pembayaran Dobel (Duplikat)

Sistem sekarang **menolak** input pembayaran jika kombinasi **penyewa + bulan + tahun** yang sama sudah pernah tercatat sebelumnya — mencegah human error seperti tidak sengaja input pembayaran dua kali untuk bulan yang sama.

- Dicek lewat method `PembayaranModel::sudahBayarPeriodeIni()`.
- Saat **update/edit** data yang sudah ada, pengecekan otomatis mengecualikan record itu sendiri (pakai parameter `$excludeId`), jadi tidak akan false-positif menganggap data itu "bentrok dengan dirinya sendiri" — mirip dengan solusi yang sama dipakai untuk validasi nomor kamar di halaman Edit Kamar.
- Kalau terdeteksi duplikat, admin akan melihat pesan: *"Pembayaran untuk penyewa ini pada periode [Bulan] [Tahun] sudah pernah diinput sebelumnya."*

---

## 8. Desain Responsive (Tampilan di HP, Tablet, Laptop)

Seluruh tampilan — panel admin maupun halaman publik — sekarang menyesuaikan otomatis ke ukuran layar perangkat, memakai sistem grid & breakpoint bawaan **Bootstrap 5**.

### Breakpoint yang dipakai
Bootstrap membagi lebar layar jadi beberapa "titik ubah" (breakpoint). Yang paling sering dipakai di project ini:

| Breakpoint | Lebar layar   | Contoh perangkat        |
|------------|----------------|---------------------------|
| (default/xs) | < 576px      | HP, layar sempit          |
| `md`       | >= 768px       | Tablet, laptop kecil       |

Class seperti `col-md-3` artinya: *"di layar >= 768px, ambil 3 dari 12 kolom grid; di layar lebih kecil dari itu, ambil lebar penuh (100%) dan tersusun ke bawah"*. Prinsip inilah yang membuat kartu dashboard, form 2-kolom, dan daftar kamar otomatis rapi di HP tanpa kode tambahan.

### a. Sidebar Admin -> Jadi Menu Geser (Offcanvas) di HP
Ini perubahan paling signifikan. Sebelumnya sidebar selalu tampil kaku di kiri layar (lebar tetap 250px) — di HP ini bikin tampilan sempit dan konten ketutupan.

**Solusinya**, sekarang ada 2 versi menu yang isinya SAMA (dipakai bareng dari satu file `layout/partials/menu.php` supaya tidak perlu tulis ulang kode-nya 2 kali):

1. **Sidebar biasa** — hanya tampil di layar >= 768px, pakai class `d-none d-md-flex` (artinya: disembunyikan secara default, baru "muncul sebagai flex-container" mulai breakpoint md).
2. **Offcanvas (menu geser dari kiri)** — komponen bawaan Bootstrap yang otomatis dipakai di layar kecil. Muncul saat tombol hamburger (☰) di pojok kiri atas ditekan, lalu bisa ditutup lagi dengan tombol X atau klik di luar area menu.

Potongan kode kuncinya di `app/Views/layout/main.php`:
```html
<!-- Sidebar desktop: disembunyikan di HP -->
<nav class="sidebar p-3 d-none d-md-flex flex-column">
    ...
</nav>

<!-- Menu mobile: baru muncul kalau tombol hamburger ditekan -->
<div class="offcanvas offcanvas-start text-bg-dark" id="sidebarMobile">
    ...
</div>

<!-- Tombol hamburger: HANYA tampil di layar kecil -->
<button class="btn btn-outline-secondary d-md-none" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
    <i class="bi bi-list"></i>
</button>
```
`d-md-none` artinya kebalikan dari `d-none d-md-flex`: tombol ini tampil secara default (di HP), lalu disembunyikan mulai layar >= 768px (karena di layar besar sidebar sudah selalu terlihat, jadi tombol hamburger tidak diperlukan lagi).

### b. Tabel Data (Kamar, Penyewa, Pembayaran, Pengajuan)
Tabel data cenderung lebar (banyak kolom) dan sulit dipaksa muat di layar HP tanpa merusak keterbacaan. Solusinya, setiap tabel dibungkus:
```html
<div class="table-responsive">
    <table class="table ...">...</table>
</div>
```
`table-responsive` membuat tabel bisa **di-scroll ke samping (horizontal)** khusus di layar sempit, tanpa merusak tata letak halaman secara keseluruhan.

### c. Form Tambah/Edit
- Semua form input yang berpasangan (misalnya Bulan & Tahun di form Pembayaran) memakai `row` + `col-md-6`, sehingga otomatis bersebelahan di layar besar, dan bertumpuk ke bawah di HP.
- Padding kartu form pakai `p-3 p-md-4` — sedikit lebih rapat di HP (`p-3`), lebih lega di layar besar (`p-4`).

### d. Halaman Publik (Landing Page)
- Ukuran judul hero (`<h1>`) dibuat lebih kecil di HP dan membesar otomatis di layar >= 768px, supaya tidak "meluber" di layar sempit.
- Kartu kamar (`col-md-4`) otomatis jadi 1 kolom penuh di HP, 3 kolom sejajar di layar besar.
- Navbar publik dibuat `flex-wrap`, supaya tombol "Login Admin" turun ke bawah rapi kalau ruang tidak cukup, bukan malah terpotong.

### e. Kartu Ringkasan Dashboard
Kartu statistik (Total Kamar, Kamar Kosong, dst) pakai `col-md-3` — otomatis 4 kartu sejajar di layar besar, dan bertumpuk 1 kolom penuh di HP untuk kenyamanan baca.

### Cara Mengetesnya
1. Buka aplikasi di browser (`php spark serve`).
2. Tekan `F12` (DevTools) -> klik ikon perangkat mobile (Toggle Device Toolbar) -> pilih ukuran HP, misalnya "iPhone SE" atau "Galaxy S8".
3. Perhatikan: sidebar admin berubah jadi tombol hamburger, tabel bisa digeser ke samping, form & kartu tersusun rapi 1 kolom.

---

## 9. Ide Pengembangan Lanjutan
- Tambah role/hak akses (admin vs staff)
- Export data pembayaran ke Excel/PDF
- Notifikasi otomatis untuk penyewa yang belum bayar
- Grafik pemasukan bulanan di dashboard
- Galeri multi-foto per kamar (saat ini baru 1 foto per kamar)

---

Kalau ada error atau bagian yang masih membingungkan, tanyakan saja — saya bisa jelaskan lebih detail per baris kode.
