# WARNA BUDAYA — Laravel Project Starter

Fondasi project Laravel untuk **WARNA BUDAYA: Peta dan Arsip Digital Warisan Budaya Indonesia**.

## Persyaratan

- PHP 8.2 atau lebih baru
- Composer 2
- MySQL 8.0+ (atau MariaDB versi yang kompatibel)
- Node.js/npm hanya diperlukan ketika nanti menambahkan proses build frontend

> Folder `vendor/` tidak disertakan. Composer akan mengunduh dependensi Laravel saat instalasi.

## Langkah menjalankan di Windows

1. Ekstrak ZIP ini.
2. Buka folder hasil ekstrak di VS Code (folder yang langsung berisi file `artisan` dan `composer.json`).
3. Buat database MySQL bernama `warna_budaya` melalui phpMyAdmin atau MySQL.
4. Buka terminal VS Code dan jalankan:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

5. Buka `.env`, pastikan konfigurasi database benar:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warna_budaya
DB_USERNAME=root
DB_PASSWORD=
```

6. Jalankan migrasi dan kategori awal:

```powershell
php artisan migrate
php artisan db:seed
```

7. Jalankan website:

```powershell
php artisan serve
```

Buka alamat yang ditampilkan, biasanya `http://127.0.0.1:8000`.

## Catatan

- Project ini adalah fondasi Laravel, belum seluruh fitur produk selesai.
- Halaman awal masih berupa landing page sederhana untuk memastikan Laravel berjalan.
- Belum ada login/admin panel, peta interaktif, pencarian database, maupun CRUD konten.
- Sebelum menampilkan arsip ke publik, backend wajib memfilter `publication_status = published` dan `verification_status = verified`.
- Media hanya boleh ditampilkan jika `permission_status = granted`.
- Jangan gunakan `php artisan migrate:fresh` pada database yang berisi data penting karena perintah itu menghapus tabel.
