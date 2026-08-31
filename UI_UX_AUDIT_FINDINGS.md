# UI/UX AUDIT REPORT

**Tanggal**: 31 Agustus 2026  
**Project**: Stockly - Aplikasi Peminjaman Alat  
**Scope**: Admin & Petugas Halaman (Existing Features Only)

---

## TEMUAN AUDIT UI/UX

### SUMMARY MASALAH

| Kategori                        | Jumlah Masalah | Severity   |
| ------------------------------- | -------------- | ---------- |
| Status Badge & Visual Hierarchy | 8              | MEDIUM     |
| Button & Action UI              | 7              | MEDIUM     |
| Form UX                         | 6              | LOW-MEDIUM |
| Table UX                        | 5              | LOW        |
| Dashboard                       | 3              | MEDIUM     |
| Empty State                     | 3              | LOW        |
| Consistency                     | 10             | MEDIUM     |
| Accessibility                   | 5              | MEDIUM     |
| Responsive Design               | 4              | MEDIUM     |
| **TOTAL**                       | **51**         | -          |

---

## 1. STATUS BADGE & VISUAL HIERARCHY

### 1.1 Status Peminjaman Inkonsisten

| Masalah                                                                  | Halaman                                               | Current                              | Ideal          |
| ------------------------------------------------------------------------ | ----------------------------------------------------- | ------------------------------------ | -------------- |
| Color scheme berbeda di tiap halaman                                     | Admin Peminjaman, Admin Pengembalian, Petugas Laporan | Berbeda-beda                         | Konsisten      |
| Icon tidak ada pada badge                                                | Semua tabel                                           | Hanya teks                           | Teks + Icon    |
| Background/text contrast kurang                                          | Status "Diajukan"                                     | bg-yellow-100 text-yellow-808 (TYPO) | Perbaiki color |
| "telat" badge kurang prominent                                           | Admin Peminjaman                                      | bg-red-100 text-red-800              | Jelas sudah    |
| **Dampak**: User kebingungan dengan status yang sama ditampilkan berbeda |                                                       |                                      |

### 1.2 Status Kondisi Alat (Baik / Rusak Ringan / Rusak Berat)

| Masalah                                                                      | Lokasi               | Current      | Ideal                                    |
| ---------------------------------------------------------------------------- | -------------------- | ------------ | ---------------------------------------- |
| Hanya ada 2 kategori (Baik, Other)                                           | Admin Alat Index     | binary logic | Support: Baik, Rusak Ringan, Rusak Berat |
| Kondisi pengembalian di admin pengembalian punya 3 status tapi warna hanya 3 | Admin Pengembalian   | 3 status     | Sesuai 3 status                          |
| Tidak ada icon yang membedakan                                               | Pengembalian kondisi | Hanya teks   | Teks + Icon                              |

### 1.3 Role/User Badge

| Masalah                                            | Lokasi           |
| -------------------------------------------------- | ---------------- |
| Admin user list: warna untuk role kurang memorable | Admin User Index |
| Tidak ada icon untuk role indicator                | Admin User Index |

---

## 2. BUTTON & ACTION UI

### 2.1 Button Styling Inkonsisten

| Masalah                                                   | Contoh                                        |
| --------------------------------------------------------- | --------------------------------------------- |
| Primary button (.bg-blue-600) vs Secondary (.bg-gray-800) | Tidak ada clear primary/secondary distinction |
| Danger button (.bg-red-500) used untuk action berbeda     | "Hapus" dan tombol di Peminjaman Status       |
| Button size tidak konsisten                               | Small buttons di table vs normal di header    |
| Tombol "Setujui" (emerald-600) tidak standard             | Petugas Peminjaman                            |

### 2.2 Action Button Layout

| Masalah                                          | Lokasi                               | Dampak                 |
| ------------------------------------------------ | ------------------------------------ | ---------------------- |
| 2 tombol dalam 1 row terlalu rapat               | Petugas Peminjaman (Setujui + Tolak) | Sulit diklik di mobile |
| Status select dropdown + Hapus dalam flex column | Admin Peminjaman                     | Boros space            |
| Button text tidak konsisten                      | "Terima Kembali" vs "Setujui"        | Kebingungan pengguna   |

### 2.3 Confirmation Dialog

| Masalah                                  | Status                             |
| ---------------------------------------- | ---------------------------------- |
| Hanya menggunakan JavaScript `confirm()` | Browser default dialog             |
| Tidak ada visual warning                 | Destructive action tidak obvious   |
| Pesan tidak deskriptif                   | "Yakin ingin menghapus?" (generic) |

---

## 3. FORM UX

### 3.1 Input Labeling

| Masalah                                    | Lokasi                                |
| ------------------------------------------ | ------------------------------------- |
| Required field indicator (\*) inconsistent | Ada di beberapa form tapi tidak semua |
| Label font-size terlalu kecil              | Create/Edit form (text-sm)            |
| Help text/description missing              | "Status Kondisi", "Deskripsi", dll    |

### 3.2 Error Validation Display

| Masalah                                      | Dampak                                  |
| -------------------------------------------- | --------------------------------------- |
| Error message hanya di bawah input           | Sulit terlihat jika form panjang        |
| Error color (@error) tidak standard          | text-red-500 tapi background tidak ada  |
| Form tidak menampilkan summary error di atas | User harus scroll untuk tahu error mana |

### 3.3 Date Input UX

| Masalah                                     | Lokasi                                    |
| ------------------------------------------- | ----------------------------------------- |
| Input date hanya tanggal                    | Admin Peminjaman Create                   |
| Tidak ada placeholder atau contoh format    | User bingung format apa                   |
| "Rencana Tanggal Kembali" label tidak jelas | Ambiguous apakah ini deadline atau actual |

---

## 4. TABLE UX

### 4.1 Table Header & Readability

| Masalah                                        | Lokasi                                      |
| ---------------------------------------------- | ------------------------------------------- |
| Header text terlalu kecil (text-xs uppercase)  | Semua tabel                                 |
| Kolom terlalu banyak (8 kolom di Pengembalian) | Admin Pengembalian                          |
| Header tidak sticky                            | User harus scroll up untuk lihat kolom name |
| Column alignment tidak konsisten               | Tanggal left-aligned, seharusnya konsisten  |

### 4.2 Table Row Spacing & Readability

| Masalah                                                        | Lokasi                   | Dampak          |
| -------------------------------------------------------------- | ------------------------ | --------------- |
| py-3 px-4 terlalu padat di baris yang kompleks                 | Table dengan nested list | Sulit dibaca    |
| Text color terlalu gelap (text-gray-700) pada background putih | Table body               | Kurang contrast |
| Hover state hanya subtle (hover:bg-gray-50)                    | Semua tabel              | Tidak obvious   |

### 4.3 Table Column Issues

| Masalah                                              | Lokasi                   |
| ---------------------------------------------------- | ------------------------ |
| Detail Alat column dengan nested ul/li terlalu rapat | Peminjaman, Pengembalian |
| Tanggal ditampilkan full dengan jam:menit            | Sulit dibaca (text-xs)   |
| Jumlah/Quantity di badge terlalu kecil               | Admin Pengembalian       |

### 4.4 Table Empty State

| Masalah                         | Lokasi                                 | Current            |
| ------------------------------- | -------------------------------------- | ------------------ |
| Empty state tidak user-friendly | Semua tabel                            | "Belum ada data X" |
| Tidak ada action/suggestion     | -                                      | Static text saja   |
| Colspan tidak selalu tepat      | Beberapa table colspan error potential |

---

## 5. DASHBOARD

### 5.1 Dashboard Content Issues

| Masalah                                                | Dampak                                 |
| ------------------------------------------------------ | -------------------------------------- |
| Hanya menampilkan "Log Aktivitas Terbaru"              | Dashboard tidak informatif             |
| Tidak ada statistik card (Total Alat, Peminjaman, dll) | Admin tidak bisa overview dengan cepat |
| Welcome alert terlalu besar (mb-6 p-4)                 | Menghabiskan real estate               |
| Log table tidak punya search/filter                    | Sulit cari aktivitas spesifik          |
| DateTime format pada log tidak jelas                   | "2026-06-01 10:00:00" terlalu verbatim |

---

## 6. EMPTY STATE

### 6.1 Empty State Messages

| Masalah                     | Lokasi      | Current            |
| --------------------------- | ----------- | ------------------ |
| Text terlalu generic        | Semua tabel | "Belum ada data X" |
| Tidak ada call-to-action    | -           | Hanya message      |
| Icon/illustration tidak ada | -           | Hanya text         |

Contoh:

- "Belum ada pengajuan peminjaman baru" → Jelas tapi bisa lebih helpful
- "Tidak ada alat yang sedang dipinjam saat ini" → Sama

---

## 7. CONSISTENCY ISSUES (GLOBAL)

### 7.1 Color Palette

| Element            | Current                      | Status                             |
| ------------------ | ---------------------------- | ---------------------------------- |
| Primary Color      | blue-600                     | Konsisten                          |
| Danger/Destructive | red-500 / red-600            | Konsisten                          |
| Success            | emerald-600 / emerald-100    | Konsisten                          |
| Warning            | amber-600 / yellow-100       | INKONSISTEN (ada yellow dan amber) |
| Secondary/Neutral  | gray-300, gray-800, gray-900 | Inkonsisten (terlalu banyak shade) |

### 7.2 Border Radius

| Element | Current                         |
| ------- | ------------------------------- |
| Button  | rounded-lg (consistent)         |
| Input   | rounded-lg (consistent)         |
| Card    | rounded-lg (consistent)         |
| Image   | rounded-lg (consistent)         |
| Badge   | rounded-full (untuk pill style) |

**Issue**: `rounded-full` untuk badge berbeda dari `rounded-lg` untuk element lain → mixing paradigm

### 7.3 Spacing/Padding

| Element      | Current   | Konsisten?      |
| ------------ | --------- | --------------- |
| Card padding | p-6, p-5  | Tidak konsisten |
| Table cell   | py-3 px-4 | Konsisten       |
| Form group   | mb-4      | Konsisten       |
| Alert        | p-4       | Konsisten       |

### 7.4 Typography

| Element         | Current           | Issue                            |
| --------------- | ----------------- | -------------------------------- |
| Page Title (h1) | text-lg font-bold | Terlalu kecil untuk heading      |
| Section Title   | text-lg font-bold | Sama dengan page title           |
| Table Header    | text-sm uppercase | Terlalu kecil                    |
| Body text       | text-sm           | Terlalu kecil di beberapa tempat |

---

## 8. ACCESSIBILITY

### 8.1 Label & Form

| Masalah                                     | Dampak                                                       |
| ------------------------------------------- | ------------------------------------------------------------ |
| Input tidak selalu punya associated label   | Form create/edit punya label, tapi beberapa search box tidak |
| Placeholder digunakan sebagai label         | "-- Pilih Kategori --" di dropdown                           |
| Focus state tidak visible di beberapa input | Default browser focus mungkin kurang visible                 |

### 8.2 Button Accessibility

| Masalah                                      | Contoh                        |
| -------------------------------------------- | ----------------------------- |
| Button tidak punya aria-label jika icon only | Tombol status select dropdown |
| Link styled as button tanpa button semantics | "Reset" link di search        |

### 8.3 Image Alt Text

| Masalah                           | Lokasi                     |
| --------------------------------- | -------------------------- |
| Gambar alat punya alt text (good) | Admin Alat Index           |
| Fallback image punya alt          | no-image.svg (perlu check) |

### 8.4 Color Contrast

| Masalah                    | Lokasi                                   |
| -------------------------- | ---------------------------------------- |
| yellow-100 text-yellow-800 | Status badge (might have contrast issue) |
| gray-400 text              | Some text might be too light             |

### 8.5 Keyboard Navigation

| Masalah                                 | Lokasi    |
| --------------------------------------- | --------- |
| Form tidak test keyboard tab order      | All forms |
| Modal/dialog tidak ada (jika ada modal) | N/A       |

---

## 9. RESPONSIVE DESIGN

### 9.1 Sidebar & Navigation

| Masalah                                       | Lokasi                | Mobile               |
| --------------------------------------------- | --------------------- | -------------------- |
| Sidebar width mungkin terlalu lebar di mobile | layouts/app.blade.php | Need test            |
| Sidebar menu items tidak collapsible          | -                     | Poor on small screen |

### 9.2 Table Responsiveness

| Masalah                          | Lokasi             | Issue                        |
| -------------------------------- | ------------------ | ---------------------------- |
| overflow-x-auto ada (good)       | Table pages        | Butuh test horizontal scroll |
| Header tidak sticky saat scroll  | -                  | Bad UX                       |
| Kolom banyak (8 di Pengembalian) | Admin Pengembalian | Sulit scroll horizontal      |

### 9.3 Search/Filter Form

| Masalah                                      | Lokasi            | Mobile                    |
| -------------------------------------------- | ----------------- | ------------------------- |
| md:w-80 search box bisa terlalu sempit/lebar | All list pages    | Stack di mobile OK        |
| Button size mungkin terlalu kecil            | text-sm px-4 py-2 | Minimum 44px touch target |

### 9.4 Form Responsiveness

| Masalah                 | Lokasi                                                 |
| ----------------------- | ------------------------------------------------------ |
| Form grid layout        | grid-cols-1 md:grid-cols-2 OK                          |
| Button layout di footer | flex justify-end space-x-2 → mungkin overlap di mobile |

---

## 10. TYPOGRAPHY & VISUAL HIERARCHY

### 10.1 Font Size Scale

| Level         | Current | Issue                      |
| ------------- | ------- | -------------------------- |
| Page Title    | text-lg | Too small for main heading |
| Section Title | text-lg | Same as page title         |
| Table Header  | text-sm | Too small                  |
| Body          | text-sm | Consistent                 |
| Small text    | text-xs | Too small, hard to read    |

### 10.2 Font Weight Hierarchy

| Element | Current       |
| ------- | ------------- |
| Heading | font-bold     |
| Label   | font-semibold |
| Normal  | default       |

**Issue**: Only 2 levels (bold, semibold) → not enough hierarchy

---

## 11. DETAIL MASALAH PER HALAMAN

### ADMIN DASHBOARD

- ✗ Hanya log activities, tidak ada statistik
- ✗ Welcome alert terlalu prominent
- ✗ Tidak ada action shortcuts
- ⚠ Datetime format di log tidak user-friendly

### ADMIN USER INDEX

- ✗ Role badge warna tidak memorable
- ✗ Tidak ada icon role
- ⚠ Table header text-sm terlalu kecil
- ⚠ Empty state generic

### ADMIN KATEGORI INDEX

- ✓ Sederhana, OK
- ⚠ Table header small
- ⚠ No counter badge di card header

### ADMIN ALAT INDEX

- ✓ Image handling OK (dengan fallback)
- ✗ Status kondisi hanya 2 kategori
- ⚠ Table header small
- ⚠ Gambar thumbnail terlalu small (w-12 h-12)

### ADMIN ALAT CREATE/EDIT

- ✓ Form structure OK
- ✗ Required indicator inconsistent
- ⚠ Help text missing
- ✗ Image upload UX perlu improvement (preview size)

### ADMIN PEMINJAMAN INDEX

- ✗ Status badge color typo (text-yellow-808)
- ✗ Button layout terlalu rapat (status select + hapus)
- ⚠ Table header small
- ✗ Tanggal format terlalu verbose

### ADMIN PENGEMBALIAN INDEX

- ✗ Kondisi badge color scheme perlu improvement
- ✗ Kolom terlalu banyak (8 columns)
- ⚠ Table overflow di mobile sulit
- ✗ Petugas name tidak penting untuk admin?

### PETUGAS PEMINJAMAN INDEX

- ✗ Tombol Setujui + Tolak terlalu rapat
- ✓ Status display OK
- ⚠ Button text size text-xs terlalu kecil
- ⚠ Confirmation dialog perlu improvement

### PETUGAS PENGEMBALIAN INDEX

- ✓ Layout OK
- ✗ Status badge color standard
- ⚠ Button "Terima Kembali" perlu improvement
- ⚠ Confirmation message kurang deskriptif

### PETUGAS LAPORAN INDEX

- ✓ Filter form OK
- ✗ Status badge sizing berbeda
- ⚠ Table number prefix tidak perlu align right
- ⚠ Print button perlu UX test

---

## 12. PERFORMANCE & CODE QUALITY

### Potential Issues

- ✓ Eager loading OK (verified from code)
- ⚠ CSS class utility banyak (Tailwind) - OK for utility framework
- ⚠ Inline style minimal (good)
- ⚠ Blade template readable (good)

---

## PRIORITAS FIXING

### IMMEDIATE (High Priority)

1. ✗ Fix status badge color typo (text-yellow-808 → text-yellow-800)
2. ✗ Add icons to status badges
3. ✗ Make status badge consistent across all pages
4. ✗ Improve button layout di Petugas Peminjaman

### HIGH (Medium Priority)

5. ✗ Improve empty state messages + add CTA
6. ✗ Add simple dashboard statistics
7. ✗ Improve table header visibility (sticky or larger)
8. ✗ Improve form validation error display
9. ✗ Make button sizing consistent + accessible (min 44px)

### MEDIUM (Polish)

10. ✗ Typography hierarchy improvement
11. ✗ Add help text di form
12. ✗ Datetime display formatting
13. ✗ Table cell padding improvement
14. ✗ Responsive test & improvement

### LOW (Nice to have)

15. ✗ Role badges improvement
16. ✗ Color palette optimization
17. ✗ Microinteraction polish

---

## NEXT STEPS

**TAHAP 2 → 21 akan dilakukan secara bertahap:**

1. Status Badge & Consistency
2. Button & Action UI
3. Dashboard Enhancement
4. Table UX Improvement
5. Form UX Improvement
6. Empty State UX
7. dan seterusnya...

Setiap tahap akan dilakukan dengan:

- Identify komponen/file yang perlu diubah
- Implement improvement
- Verify tidak ada regression
- Test secara visual
