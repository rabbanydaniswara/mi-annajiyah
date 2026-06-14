# Dokumentasi Proyek SPMB Annajiyah

Dokumentasi ini dibuat dari audit kode pada 2026-05-28 untuk aplikasi Laravel SPMB/PPDB MI Annajiyah.

Dokumen root yang perlu dibaca lebih dulu:

- `../README.md`: pintu masuk dokumentasi proyek.
- `../TODO.md`: tracker aktif pekerjaan, perubahan, dan keputusan terbaru.
- `../AGENTS.md`: panduan ringkas untuk AI agent lain.

Isi dokumen:

- `PROJECT_AUDIT.md`: temuan risiko, dampak, lokasi file, dan rekomendasi perbaikan.
- `PROJECT_STRUCTURE.md`: peta struktur aplikasi, modul, route, model, dan alur data.
- `PROJECT_RULES.md`: aturan teknis yang sebaiknya dipatuhi saat mengubah proyek.
- `DEVELOPMENT_GUIDELINES.md`: panduan setup, verifikasi, testing, deployment, dan backlog prioritas.
- `REMEDIATION_TRACKER.md`: task tracker bertahap untuk perbaikan audit dan kesiapan hosting.
- `APP_IMPROVEMENT_ROADMAP.md`: roadmap pengembangan aplikasi sebelum hosting, termasuk PPDB, admin, UX publik, konten, dan hardening lanjutan.
- `HOSTING_READINESS.md`: target runtime hosting, checklist paket hosting, dan strategi deploy asset.
- `ROLE_MATRIX.md`: matriks hak akses admin dan operator.
- `CONTENT_MEDIA_CHECKLIST.md`: checklist konten dan media publik sebelum publish.
- `BACKUP_GUIDE.md`: panduan backup dan recovery lokal sebelum migration/perubahan besar.
- `PERFORMANCE_MEDIA_PRESERVATION_PLAN.md`: rencana optimasi website agar ringan serta aturan menjaga data/gambar baseline agar tidak terhapus saat perubahan berikutnya.
- `COMPREHENSIVE_DEPLOYMENT_AUDIT_PLAN_2026-06-08.md`: tracker audit menyeluruh dari baseline kode sampai staging, production cutover, rollback, dan monitoring pascadeploy.
- `FINAL_PRE_HOSTING_AUDIT_2026-06-08.md`: hasil audit lokal final, temuan P0-P2, bukti verifikasi, dan urutan remediation sebelum hosting.

Catatan penting: audit final lokal sebelum hosting aktif mulai 2026-06-08. Belum ada deployment atau pemindahan file ke hosting. Gunakan `COMPREHENSIVE_DEPLOYMENT_AUDIT_PLAN_2026-06-08.md` sebagai tracker utama dan `TODO.md` sebagai ringkasan status harian.
