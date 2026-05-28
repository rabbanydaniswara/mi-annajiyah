<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pendaftaran - {{ $siswa->nama }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; background: #f0f0f0; }
        .card { max-width: 800px; margin: 0 auto; background: #white; border: 2px solid #0b3b1e; border-radius: 15px; overflow: hidden; box-shadow: 0 0 20px rgba(0,0,0,0.1); position: relative; }
        .header { background: #0b3b1e; color: white; padding: 20px; display: flex; align-items: center; gap: 20px; border-bottom: 5px solid #f9c74f; }
        .logo { width: 80px; height: 80px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; padding: 5px; }
        .logo img { max-width: 100%; max-height: 100%; }
        .header-text h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header-text p { margin: 5px 0 0; font-size: 12px; opacity: 0.8; }
        .content { padding: 30px; display: grid; grid-template-columns: 1fr 200px; gap: 30px; }
        .info-group { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .label { font-size: 10px; font-weight: bold; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .value { font-size: 16px; font-weight: bold; color: #0b3b1e; }
        .photo-box { border: 2px dashed #ccc; width: 100%; height: 250px; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #aaa; text-align: center; border-radius: 10px; }
        .footer { padding: 20px 30px; background: #f9f9f9; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: flex-end; }
        .qr-code { width: 100px; height: 100px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #888; }
        .signature { text-align: center; }
        .signature-line { width: 150px; border-bottom: 1px solid #333; margin: 50px auto 5px; }
        .no-print { position: fixed; top: 20px; right: 20px; }
        .btn-print { background: #0b3b1e; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        @media print {
            body { background: white; padding: 0; }
            .card { border: 1px solid #000; box-shadow: none; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">CETAK KARTU</button>
    </div>

    <div class="card">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('logo.png') }}" alt="Logo">
            </div>
            <div class="header-text">
                <h1>Kartu Pendaftaran Santri Baru</h1>
                <p>MI ANNAJIYAH - TAHUN PELAJARAN {{ $siswa->tahun_ajaran ?: date('Y').'/'.(date('Y') + 1) }}</p>
                <p>Jl. Raya Parung Panjang, Ciomas, Kec. Tenjo, Kabupaten Bogor</p>
            </div>
        </div>

        <div class="content">
            <div class="details">
                <div class="info-group">
                    <div class="label">Nomor Pendaftaran</div>
                    <div class="value">{{ $siswa->nomor_pendaftaran ?: '-' }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Nama Lengkap</div>
                    <div class="value">{{ $siswa->nama }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Tempat, Tanggal Lahir</div>
                    <div class="value">{{ $siswa->tempat_lahir }}, {{ \Carbon\Carbon::parse($siswa->tanggal_lahir)->translatedFormat('d F Y') }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Jenis Kelamin</div>
                    <div class="value">{{ $siswa->jenis_kelamin }}</div>
                </div>
                <div class="info-group">
                    <div class="label">NISN / NIS</div>
                    <div class="value">{{ $siswa->nisn ?? '-' }} / {{ $siswa->nis ?? '-' }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Asal Sekolah</div>
                    <div class="value">{{ $siswa->asal_sekolah }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Nama Orang Tua / Wali</div>
                    <div class="value">{{ $siswa->nama_ortu }}</div>
                </div>
                <div class="info-group">
                    <div class="label">Nomor WhatsApp</div>
                    <div class="value">{{ $siswa->no_wa }}</div>
                </div>
            </div>

            <div class="sidebar">
                <div class="photo-box">
                    <div style="font-size: 10px;">PAS FOTO<br>3 X 4</div>
                </div>
                <p style="font-size: 10px; color: #888; text-align: center; margin-top: 10px;">Silakan tempel foto 3x4 jika belum diunggah secara digital.</p>
            </div>
        </div>

        <div class="footer">
            <div class="qr-info">
                <div class="qr-code">SCAN VALIDASI</div>
                <p style="font-size: 10px; margin-top: 5px;">Dicetak pada: {{ now()->translatedFormat('d/m/Y H:i') }}</p>
            </div>
            <div class="signature">
                <p style="font-size: 12px; margin: 0;">Panitia PPDB,</p>
                <div class="signature-line"></div>
                <p style="font-size: 10px; font-weight: bold; margin: 0;">MI ANNAJIYAH</p>
            </div>
        </div>
    </div>

    <script>
        // Auto trigger print if needed
        // window.onload = () => window.print();
    </script>
</body>
</html>
