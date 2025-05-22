# 🍜 Website Mie Ayam

Website restoran mie ayam dengan sistem admin untuk mengelola menu dan review pelanggan.

## ✨ Fitur

**Customer (Frontend):**
- Tampilan menu dengan kategori
- Rating dan review
- Order via WhatsApp
- Responsive design

**Admin (Backend):**
- Dashboard statistik
- Kelola menu (tambah/edit/hapus)
- Upload foto menu
- Moderasi review

## 🛠️ Teknologi

- **Frontend**: HTML, CSS, Bootstrap 5, jQuery
- **Backend**: PHP, MySQL
- **Design**: Font Awesome, Google Fonts

## 📁 Struktur File

```
├── admin/          # Panel admin
├── assets/         # CSS, JS, gambar
├── config/         # Konfigurasi database
└── index.php       # Halaman utama
```

## ⚡ Instalasi

1. **Upload ke Server**
   - Copy semua file ke folder web server
   - Pastikan PHP dan MySQL sudah terinstall

2. **Set Permission**
   ```bash
   chmod 755 assets/images/menu/
   ```

3. **Konfigurasi Database**
   - Edit file `config/database.php`
   - Sesuaikan dengan setting database Anda

4. **Akses Website**
   - Frontend: `yoursite.com/`
   - Admin: `yoursite.com/admin/`

## 🔑 Login Admin

- Username: `admin`
- Password: `password`

## 🎨 Kustomisasi

- **Warna**: Edit file `assets/css/style.css`
- **Logo**: Ganti di folder `assets/images/`
- **Kontak**: Update melalui admin panel
- **WhatsApp**: Ubah nomor melalui pengaturan

## 📱 Responsive

✅ Mobile Phone  
✅ Tablet  
✅ Desktop  
✅ Large Screen  

## 🚀 Fitur Unggulan

- Order langsung ke WhatsApp
- Upload foto menu
- Filter menu by kategori
- Sistem rating bintang
- Panel admin user-friendly
- Animasi smooth
- SEO friendly

## 🔧 Troubleshooting

**Gambar tidak muncul:**
- Cek permission folder `assets/images/menu/`
- Maksimal ukuran file 5MB

**Login gagal:**
- Periksa koneksi database
- Clear browser cache

**WhatsApp tidak berfungsi:**
- Pastikan format nomor benar (+62xxx)

## 📞 Support

Untuk bantuan teknis atau customization, silakan hubungi developer.

---

**🎯 Perfect untuk:** Warung mie ayam, resto kecil-menengah, UMKM kuliner