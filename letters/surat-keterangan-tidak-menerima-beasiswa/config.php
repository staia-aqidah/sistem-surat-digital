<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Keterangan Tidak Menerima Beasiswa',
    'nomor_kode' => 'IAI-AA/S-Ket',
    'category' => 'mahasiswa',
    'info'     => 'Untuk keperluan keterangan bahwa mahasiswa tidak menerima beasiswa lain.',
    'petunjuk' => 'Nomor surat akan dilengkapi oleh Tata Usaha saat ditandatangani.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'], $f['tahun_akademik'],
    ],
];
