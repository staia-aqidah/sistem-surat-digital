<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Berita Acara Ujian Skripsi / Tesis',
    'nomor_kode' => 'IAI-AA.BA-Ujian',
    'category' => 'mahasiswa',
    'info'     => 'Dokumen berita acara sidang skripsi (S1) atau tesis (S2).',
    'petunjuk' => 'Isi data sebelum ujian. Nilai dan tanda tangan dilengkapi saat ujian berlangsung.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'],
        ['name'=>'judul',          'label'=>'Judul Skripsi / Tesis',  'type'=>'textarea','required'=>true, 'rows'=>2],
        ['name'=>'tanggal_ujian',  'label'=>'Tanggal Ujian',           'type'=>'date',    'required'=>true],
        ['name'=>'waktu_mulai',    'label'=>'Pukul Mulai',             'type'=>'text',    'required'=>true, 'placeholder'=>'09.00 WIB'],
        ['name'=>'waktu_selesai',  'label'=>'Pukul Selesai',           'type'=>'text',    'required'=>true, 'placeholder'=>'10.30 WIB'],
        ['name'=>'ruang',          'label'=>'Ruang Ujian',             'type'=>'text',    'required'=>true, 'placeholder'=>'Ruang Sidang Lt. 2'],
        ['name'=>'penguji1',       'label'=>'Penguji 1 — Ketua',      'type'=>'text',    'required'=>true],
        ['name'=>'penguji2',       'label'=>'Penguji 2 — Anggota',    'type'=>'text',    'required'=>true],
        ['name'=>'pembimbing',     'label'=>'Dosen Pembimbing',        'type'=>'text',    'required'=>true],
    ],
];
