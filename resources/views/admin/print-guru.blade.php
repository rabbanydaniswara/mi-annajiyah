<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #0b3b1e; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #0b3b1e; font-size: 20px; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; color: #0b3b1e; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #999; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
        <p style="margin: 0; font-weight: bold;">Mode Pratinjau Cetak</p>
        <button onclick="window.print()" style="background: #0b3b1e; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">Cetak Sekarang (PDF)</button>
    </div>

    <div class="header">
        <h1>MI ANNAJIYAH</h1>
        <p>Jl. PLN No.80, Pondok Karya, Kec. Pondok Aren, Kota Tangerang Selatan, Banten</p>
        <h2 style="margin-top: 15px; text-decoration: underline;">{{ strtoupper($title) }}</h2>
        <p>Tanggal Cetak: {{ date('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Lengkap</th>
                <th>Jabatan</th>
                <th>Mata Pelajaran</th>
                <th>NIP</th>
                <th>No. Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $g)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $g->nama }}</strong></td>
                <td>{{ $g->jabatan ?: '-' }}</td>
                <td>{{ $g->mapel }}</td>
                <td>{{ $g->nip ?: '-' }}</td>
                <td>{{ $g->no_telp ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak secara otomatis melalui Sistem Informasi SPMB MI Annajiyah</p>
        <p>&copy; {{ date('Y') }} MI Annajiyah</p>
    </div>

    <script>
        if(window.location.search.includes('autoprint=1')) {
            window.print();
        }
    </script>
</body>
</html>
