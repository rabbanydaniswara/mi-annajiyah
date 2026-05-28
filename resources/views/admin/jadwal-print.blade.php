<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Pembelajaran MI Annajiyah</title>
    <style>
        @page { size: A4; margin: 20mm; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1a1a1a; line-height: 1.6; margin: 0; padding: 0; }
        
        .kop-surat { display: flex; align-items: center; border-bottom: 4px double #1a4d2e; padding-bottom: 15px; margin-bottom: 30px; }
        .logo { width: 80px; height: 80px; background: #1a4d2e; border-radius: 15px; display: flex; items-center; justify-content: center; color: white; font-weight: bold; font-size: 24px; margin-right: 20px; }
        .instansi-info h2 { margin: 0; font-size: 20px; color: #1a4d2e; letter-spacing: 1px; }
        .instansi-info p { margin: 2px 0; font-size: 12px; color: #555; }

        .title-box { text-align: center; margin-bottom: 30px; }
        .title-box h1 { margin: 0; font-size: 18px; text-transform: uppercase; text-decoration: underline; letter-spacing: 2px; }
        .title-box p { margin: 5px 0; font-weight: bold; font-size: 14px; color: #444; }

        table { width: 100%; border-collapse: collapse; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        th { background: #1a4d2e; color: white; border: 1px solid #1a4d2e; padding: 10px 8px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        td { border: 1px solid #ddd; padding: 10px 8px; font-size: 12px; }
        
        .day-header { background: #f0f7f2; font-weight: 900; color: #1a4d2e; font-size: 13px; border-left: 6px solid #1a4d2e !important; }
        .time-col { font-family: 'Courier New', Courier, monospace; font-weight: bold; color: #333; white-space: nowrap; width: 120px; }
        .subject-col { font-weight: bold; color: #000; font-size: 13px; }
        .guru-col { color: #555; font-style: italic; }
        .kelas-badge { display: inline-block; padding: 2px 8px; background: #eee; border-radius: 4px; font-weight: bold; font-size: 11px; }

        .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
        .ttd { text-align: center; width: 250px; }
        .ttd p { margin: 0; font-size: 13px; }
        .ttd-space { height: 70px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            table { box-shadow: none; }
            th { -webkit-print-color-adjust: exact; }
            .day-header { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="position: fixed; top: 0; left: 0; right: 0; background: #fff3cd; padding: 15px; text-align: center; border-bottom: 2px solid #ffeeba; z-index: 1000; font-size: 14px;">
        <i class="fas fa-print"></i> <b>SISTEM CETAK JADWAL</b> - Tekan <b>Ctrl+P</b> jika dialog cetak tidak muncul.
        <a href="javascript:window.history.back()" style="margin-left: 20px; color: #856404; text-decoration: none; font-weight: bold;">[ &larr; KEMBALI ]</a>
    </div>

    <div style="margin-top: 60px;">
        <div class="kop-surat">
            <div class="logo">MI</div>
            <div class="instansi-info">
                <h2>YAYASAN PENDIDIKAN ISLAM AN-NAJIYAH</h2>
                <p>MADRASAH IBTIDAIYAH (MI) ANNAJIYAH</p>
                <p>Jl. Kertamukti No. 1, Tangerang Selatan | Telp: (021) 1234567</p>
                <p>Email: info@mi-annajiyah.sch.id | Web: mi-annajiyah.sch.id</p>
            </div>
        </div>

        <div class="title-box">
            <h1>JADWAL PELAJARAN SEMESTER GANJIL</h1>
            <p>TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y')+1 }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 130px;">Waktu / Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengajar</th>
                    <th style="width: 60px; text-align: center;">Kelas</th>
                    <th>Ruangan</th>
                </tr>
            </thead>
            <tbody>
                @php $lastDay = ''; @endphp
                @foreach($jadwal as $j)
                    @if($lastDay != $j->hari)
                        <tr>
                            <td colspan="5" class="day-header">{{ strtoupper($j->hari) }}</td>
                        </tr>
                        @php $lastDay = $j->hari; @endphp
                    @endif
                    <tr>
                        <td class="time-col">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}</td>
                        <td class="subject-col">{{ $j->mapel }}</td>
                        <td class="guru-col">{{ $j->guru?->nama ?? '-' }}</td>
                        <td style="text-align: center;"><span class="kelas-badge">{{ $j->kelas }}</span></td>
                        <td>{{ $j->ruangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div class="ttd">
                <p>Tangerang Selatan, {{ now()->translatedFormat('d F Y') }}</p>
                <p>Kepala Madrasah,</p>
                <div class="ttd-space"></div>
                <p><b><u>Putri Nurlailawati, S.Ak</u></b></p>
                <p>NIP. ..............................</p>
            </div>
        </div>
    </div>
</body>
</html>
