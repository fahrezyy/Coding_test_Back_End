# 🚀 Inventaris - Product Management & Inventory API

Aplikasi Back-End RESTful API dan Web Dashboard profesional untuk pengelolaan produk inventaris. Dibangun menggunakan **PHP Native** dan **SQLite**, dilengkapi dengan antarmuka web modern bergaya SaaS (*Software as a Service*) menggunakan **Tailwind CSS**.

---

## 🛠️ Tech Stack & Tools
- **Bahasa Pemrograman:** PHP (>= 8.0)
- **Database:** SQLite (Relational Database, file lokal `database.sqlite`)
- **Styling & UI:** Tailwind CSS & Font Awesome (Dark Mode SaaS Theme)
- **Keamanan:** Password Hashing (`password_hash`), Prepared Statements (Anti SQL Injection), & Bearer Token Authentication
- **Testing Framework:** PHPUnit (Automated Unit Testing)
- **Tools Testing API:** Browser, Postman, atau cURL

---

## 🌟 Fitur Utama & Kriteria Penilaian
1. **Autentikasi Aman Berbasis Database:** Proses login terintegrasi dengan tabel `users` di SQLite menggunakan fungsi enkripsi standar industri (*password hashing*).
2. **Full CRUD Produk:** 
   * Tambah produk baru dengan informasi lengkap (Nama, Deskripsi, Harga, Kategori, Status Ketersediaan).
   * Lihat daftar seluruh produk.
   * Perbarui (*Update*) data produk berdasarkan ID.
   * Hapus (*Delete*) produk berdasarkan ID.
3. **Pencarian & Filter Tingkat Lanjut:** 
   * Pencarian fleksibel berdasarkan nama produk atau kategori.
   * Filter produk berdasarkan status ketersediaan (`tersedia` / `habis`).
4. **Fitur Tambahan (Bonus Nilai Plus):**
   * **Automated Unit Testing:** Dilengkapi pengujian otomatis menggunakan **PHPUnit**.
   * **Dashboard Statistik Real-Time:** Endpoint dan tampilan kartu ringkasan untuk memantau total produk, stok tersedia, dan stok habis.
   * **Middleware Validasi Input:** Menolak data kosong atau tidak valid dengan respons HTTP `422 Unprocessable Entity`.
   * **Toast Notification & UI Modern:** Mengganti pop-up standar browser dengan notifikasi interaktif yang elegan.

---

## 📂 Struktur Direktori Proyek
```text
product-api/
├── tests/              # File pengujian otomatis (Unit Testing PHPUnit)
│   └── ProductTest.php
├── vendor/             # Dependensi Composer (PHPUnit)
├── database.php        # Konfigurasi koneksi PDO SQLite & Auto-Seeder Tabel
├── database.sqlite     # File database lokal (Tabel products & users)
├── index.php           # Single Entry Point (RESTful API Backend + Web Dashboard UI)
├── phpunit.xml         # Konfigurasi PHPUnit
├── composer.json       # Manajemen paket Composer
└── README.md           # Dokumentasi Proyek
```

---

## 🚀 Panduan Menjalankan Aplikasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/product-api.git
   cd product-api
   ```

2. **Instal Dependensi (Opsional untuk Testing)**
   ```bash
   composer install
   ```

3. **Jalankan Server Lokal PHP**
   ```bash
   php -S localhost:8000
   ```

4. **Akses Aplikasi Melalui Browser**
   * Buka browser dan kunjungi: `http://localhost:8000`
   * Masuk menggunakan kredensial default database:
     * **Email:** `admin@test.com`
     * **Password:** `password123`

---

## 🧪 Jalankan Automated Unit Testing
Untuk memverifikasi fungsionalitas dan logika aplikasi secara otomatis menggunakan **PHPUnit**, jalankan perintah berikut di terminal:
```bash
./vendor/bin/phpunit
```

---

## 📡 Dokumentasi Endpoint RESTful API

### 1. Login (Verifikasi Akun dari Database)
* **URL:** `POST /api/login`
* **Body (JSON):**
  ```json
  {
      "email": "admin@test.com",
      "password": "password123"
  }
  ```

### 2. Lihat Semua Produk (Disertai Search & Filter)
* **URL:** `GET /api/products`
* **Query Parameters (Opsional):**
  * `search`: Kata kunci pencarian nama atau kategori (`?search=laptop`)
  * `status`: Filter berdasarkan ketersediaan (`?status=tersedia` atau `?status=habis`)

### 3. Tambah Produk Baru *(Membutuhkan Auth)*
* **URL:** `POST /api/products`
* **Header:** `Authorization: Bearer secret-token-123`
* **Body (JSON):**
  ```json
  {
      "name": "Mechanical Keyboard",
      "description": "Keyboard RGB dengan switch blue",
      "price": 650000,
      "category": "Elektronik",
      "status": "tersedia"
  }
  ```

### 4. Edit Produk Berdasarkan ID *(Membutuhkan Auth)*
* **URL:** `PUT /api/products/{id}`
* **Header:** `Authorization: Bearer secret-token-123`
* **Body (JSON):** Sama seperti penambahan produk.

### 5. Hapus Produk Berdasarkan ID *(Membutuhkan Auth)*
* **URL:** `DELETE /api/products/{id}`
* **Header:** `Authorization: Bearer secret-token-123`

### 6. Statistik Produk
* **URL:** `GET /api/products/stats`

---

## 🔒 Aspek Keamanan
- **SQL Injection Prevention:** Seluruh query ke database SQLite menggunakan *Prepared Statements* dengan parameter binding (`PDO`).
- **Password Security:** Password akun admin disimpan di dalam database menggunakan algoritma *hashing* aman (`password_hash()`) dan diverifikasi menggunakan `password_verify()`.
- **Persistent Session Control:** Menggunakan `localStorage` pada browser untuk menyimpan token autentikasi secara aman selama sesi aktif, yang juga dapat dibersihkan secara instan melalui fungsi *Logout*.