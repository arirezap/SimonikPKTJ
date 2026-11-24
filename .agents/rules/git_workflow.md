---
trigger: always_on
description: Commit dan push perubahan selalu dilakukan oleh pengguna melalui GitHub Desktop.
---

## Alur Kerja Git (GitHub Desktop)

- **Commit & push adalah tugas pengguna**: Jangan pernah menjalankan perintah `git add`, `git commit`, atau `git push` sendiri, kecuali pengguna memintanya secara eksplisit.
- **Tanpa instruksi CLI Git di laporan akhir**: Jangan menuliskan blok perintah `git add` / `git commit` / `git push` pada ringkasan akhir tugas. Cukup sebutkan daftar berkas yang berubah sudah siap di-*commit* dan *push* melalui **GitHub Desktop** (lalu *deploy* di cPanel jika relevan).
- **Perintah baca-saja tetap diperbolehkan**: Perintah inspeksi seperti `git status` atau `git diff` boleh digunakan untuk memverifikasi perubahan sebelum selesai.
