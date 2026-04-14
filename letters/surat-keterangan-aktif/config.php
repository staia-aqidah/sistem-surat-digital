<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Keterangan Aktif Kuliah',
    'nomor_kode' => 'IAI-AA/S-Ket',
    'category' => 'mahasiswa',
    'info'     => 'Untuk keperluan beasiswa, bank, dan administrasi lainnya.',
    'petunjuk' => 'Nomor surat akan dilengkapi oleh Tata Usaha saat ditandatangani.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'], $f['tahun_akademik'],
        $f['tembusan'],
    ],
];
