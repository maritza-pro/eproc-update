
# 🧬 Panduan Gaya Kode

Kalau kamu suka ngoding sembarangan, silakan tutup file ini sekarang juga.  
Tapi kalau mau proyek kamu gak bikin sakit mata, ikuti aturan main berikut. Otomatisasi itu ada buat dipakai, bukan buat diabaikan.

---

## 🧱 Struktur Proyek

- **Backend**: Laravel `^12.0` (semoga kamu gak downgrade ke Laravel 5 ya)
- **Frontend**: Vite, Tailwind CSS, dan semua buzzword modern lainnya
- **Realtime & Performa**: Laravel Octane, Horizon, Laravel-S (biar gak lemot kayak siput)
- **UI Framework**: Filament `^3.3` (udah keren, jangan diacak-acak)

---

## 🎯 Prinsip Penulisan Kode

- ✅ Kode jelas, bukan tebak-tebakan
- ✅ Otomatisasi, bukan kerja rodi
- ✅ Ikuti konvensi Laravel & Filament, bukan gaya bebas
- ✅ Maintainable, bukan sekadar "yang penting jalan"

---

## 🧹 Gaya PHP (Laravel Pint)

> Kalau kamu masih ngetik manual indentasi dan spasi... selamat datang di tahun 2025.  
> Jalankan:
>
> ```bash
> php vendor/bin/pint
> ```

### 🧷 Preset Dasar

- **Preset**: `laravel` (ya iya lah)
- **Kecuali**: `tests/` (biar lebih cepat, bukan karena males)

### 🔧 Aturan Penting

- **Array pendek**: `[]`, bukan `array()`, kita bukan pakai PHP 4
- **Urutan class**: trait dulu, lalu properti, lalu metode (biar gak kayak campur aduk sayur lodeh)
- **Import urut abjad**: bukan urut mood
- **String pakai kutip tunggal**: hemat karakter bro
- **Semua method & property wajib visibility**: bukan "terserah"

---

## 🧠 Analisis Statis (PHPStan + Larastan)

> Jalankan:
>
> ```bash
> ./vendor/bin/phpstan analyse
> ```

- **Tool**: PHPStan + Larastan (biar error ketahuan sebelum deploy ke production lalu dimarahin)
- **Level**: paling galak
- **Cek**:
  - Tipe data
  - Variabel nyasar
  - Property yang entah dari mana

---

## 🛠️ Transformasi Kode (Rector)

> Jalankan:
>
> ```bash
> vendor/bin/rector process
> ```

- Biar kamu gak perlu refactor manual satu-satu kayak budak digital
- Sudah diatur via `rector.php`
- Gunakan `driftingly/rector-laravel` karena kita orang Laravel, bukan native PHP purist

---

## 🎨 Gaya JS & Vue (Prettier + ESLint)

> Jalankan:
>
> ```bash
> npm run lint
> ```

### Prettier

- Udah ada konfignya (`prettier.config.cjs`), tinggal pakai. Gak usah protes.

### ESLint

- Otomatis fix saat commit. Jadi gak ada alasan "lupa format"
- Dukungan penuh untuk Vue 3 dan JS modern. Gak ada alasan pakai jQuery.

---

## ✅ Otomatisasi Commit

### Husky + Lint-Staged

- Hook git jalan sebelum commit, bukan buat dipajang
- Semua file `.php`, `.js`, `.vue` diformat secara otomatis (kecuali kamu disable karena keras kepala)

```json
"lint-staged": {
  "**/*.php": ["php vendor/bin/pint"],
  "**/*.{js,vue,ts}": ["npx eslint --fix"]
}
```

---

## 🔠 Konvensi Penamaan

| Elemen        | Gaya               |
| ------------- | ------------------ |
| PHP Class     | `PascalCase`       |
| PHP Method    | `camelCase`        |
| PHP Property  | `camelCase`        |
| PHP Constant  | `UPPER_SNAKE_CASE` |
| JS Variable   | `camelCase`        |
| Vue Component | `PascalCase`       |

(Biar kamu gak bikin variable `a1`, `b2`, atau `xyz` karena "lagi buru-buru")

---

## ✏️ Konfigurasi Editor

- Indentasi: **4 spasi**, bukan tab, bukan 2
- Line ending: **LF** — karena kita gak hidup di Windows 98
- Charset: **UTF-8**
- Biar semua editor sepakat, dan gak rusak pas dibuka pake Notepad++ 😬

---

## 📚 Ringkasan

Fokus ke logika dan fitur.  
Biarin mesin yang bantu kamu ngoding lebih rapi — kecuali kamu masih seneng ngoding gaya batu.
