<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Keterangan Bebas Pustaka',
    'nomor_kode' => 'IAI-AA/S-Ket',
    'category' => 'mahasiswa',
    'info'     => 'Untuk keperluan administrasi perpustakaan dan pelayanan akademik.',
    'petunjuk' => 'Nomor surat akan dilengkapi oleh Tata Usaha saat ditandatangani.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['tembusan'],
    ],
];