<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Dispensasi Mahasiswa',
    'nomor_kode' => 'IAI-AA.S-Disp',
    'category' => 'mahasiswa',
    'info'     => 'Izin tidak menghadiri kuliah karena kegiatan resmi kampus.',
    'petunjuk' => 'Hanya untuk ketidakhadiran akibat kegiatan resmi yang diakui institusi.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'],
        ['name'=>'nama_kegiatan',  'label'=>'Nama Kegiatan',          'type'=>'text', 'required'=>true, 'placeholder'=>'Contoh: Lomba Debat Nasional PTKIN 2025'],
        ['name'=>'penyelenggara',  'label'=>'Penyelenggara Kegiatan', 'type'=>'text', 'required'=>true],
        ['name'=>'tanggal_mulai',  'label'=>'Tanggal Mulai',          'type'=>'date', 'required'=>true],
        ['name'=>'tanggal_selesai','label'=>'Tanggal Selesai',        'type'=>'date', 'required'=>true],
        ['name'=>'ditujukan',      'label'=>'Ditujukan kepada (Nama Dosen)', 'type'=>'text', 'required'=>false, 'placeholder'=>'Kosongkan untuk semua dosen pengampu', 'help'=>'Isi nama dosen jika untuk mata kuliah tertentu.'],
        $f['tembusan'],
    ],
];
