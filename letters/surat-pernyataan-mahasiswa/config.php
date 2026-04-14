<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Pernyataan Mahasiswa',
    'nomor_kode' => 'IAI-AA.S-Pern',
    'category' => 'mahasiswa',
    'info'     => 'Pernyataan keaslian karya, integritas akademik, dll.',
    'petunjuk' => 'Setelah dicetak, tempelkan materai Rp10.000 pada kolom yang tersedia sebelum ditandatangani.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'],
        ['name'=>'jenis_pernyataan','label'=>'Jenis Pernyataan','type'=>'select','required'=>true,
            'options'=>[
                'keaslian-skripsi'  => 'Keaslian Karya Tugas Akhir / Skripsi',
                'bebas-plagiat'     => 'Bebas Plagiarisme',
                'tidak-pelanggaran' => 'Tidak Pernah Melakukan Pelanggaran Akademik',
                'sanggup-lulus'     => 'Kesanggupan Menyelesaikan Studi Tepat Waktu',
            ]],
        ['name'=>'judul_karya','label'=>'Judul Karya (jika pernyataan keaslian)','type'=>'textarea','required'=>false,'rows'=>2,'placeholder'=>'Kosongkan jika bukan pernyataan keaslian karya'],
        ['name'=>'alamat','label'=>'Alamat Tempat Tinggal','type'=>'textarea','required'=>true,'rows'=>2],
    ],
];
