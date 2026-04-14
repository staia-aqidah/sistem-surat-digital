<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Rekomendasi Beasiswa',
    'nomor_kode' => 'IAI-AA.S-Rek',
    'category' => 'mahasiswa',
    'info'     => 'Rekomendasi kampus untuk pengajuan berbagai jenis beasiswa.',
    'petunjuk' => 'Pastikan IPK sesuai transkrip akademik resmi terakhir.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'], $f['tahun_akademik'], $f['ipk'],
        ['name'=>'nama_beasiswa',         'label'=>'Nama Beasiswa yang Dituju',  'type'=>'text',     'required'=>true, 'placeholder'=>'Contoh: Beasiswa BAZNAS 2025'],
        ['name'=>'penyelenggara_beasiswa','label'=>'Penyelenggara Beasiswa',     'type'=>'text',     'required'=>true, 'placeholder'=>'Contoh: Badan Amil Zakat Nasional (BAZNAS)'],
        ['name'=>'prestasi',              'label'=>'Prestasi / Keunggulan (opsional)', 'type'=>'textarea', 'required'=>false, 'rows'=>3, 'placeholder'=>'Aktif di organisasi, juara kompetisi, dll.', 'help'=>'Akan dicantumkan dalam isi surat.'],
        $f['tembusan'],
    ],
];
