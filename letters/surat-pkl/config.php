<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Pengantar PKL / Magang',
    'nomor_kode' => 'IAI-AA.S-PKL',
    'category' => 'mahasiswa',
    'info'     => 'Surat pengantar ke instansi atau lembaga tujuan PKL.',
    'petunjuk' => 'Pastikan nama dan alamat instansi tujuan sudah dikonfirmasi sebelum mencetak.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'],
        ['name'=>'instansi',       'label'=>'Nama Instansi / Lembaga Tujuan', 'type'=>'text',     'required'=>true, 'placeholder'=>'Contoh: Madrasah Ibtidaiyah Nurul Huda Jakarta'],
        ['name'=>'alamat_instansi','label'=>'Alamat Instansi',                 'type'=>'textarea', 'required'=>true, 'rows'=>2],
        ['name'=>'tanggal_mulai',  'label'=>'Tanggal Mulai PKL',               'type'=>'date',     'required'=>true],
        ['name'=>'tanggal_selesai','label'=>'Tanggal Selesai PKL',             'type'=>'date',     'required'=>true],
        ['name'=>'bidang',         'label'=>'Bidang / Divisi (opsional)',       'type'=>'text',     'required'=>false, 'placeholder'=>'Contoh: Bidang Kurikulum'],
        $f['tembusan'],
    ],
];
