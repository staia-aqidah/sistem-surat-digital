<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'     => 'Surat Izin Penelitian',
    'nomor_kode' => 'IAI-AA/S-Pm',
    'category' => 'mahasiswa',
    'info'     => 'Untuk penelitian skripsi/tesis di instansi atau lembaga luar.',
    'petunjuk' => 'Isi nama dan alamat instansi tujuan penelitian dengan lengkap dan benar.',
    'fields'   => [
        $f['nama'], $f['nim'], $f['prodi'], $f['semester'],
        ['name'=>'judul_penelitian',  'label'=>'Judul Penelitian',                'type'=>'textarea','required'=>true, 'rows'=>2, 'placeholder'=>'Judul skripsi/tesis/disertasi'],
        ['name'=>'instansi',          'label'=>'Nama Instansi Tujuan',             'type'=>'text',    'required'=>true, 'placeholder'=>'Contoh: MAN 1 Jakarta'],
        ['name'=>'alamat_instansi',   'label'=>'Alamat Instansi',                  'type'=>'textarea','required'=>true, 'rows'=>2],
        ['name'=>'tanggal_mulai',     'label'=>'Tanggal Mulai Penelitian',         'type'=>'date',    'required'=>true],
        ['name'=>'tanggal_selesai',   'label'=>'Tanggal Selesai Penelitian',       'type'=>'date',    'required'=>true],
        ['name'=>'nama_pembimbing',   'label'=>'Nama Dosen Pembimbing',            'type'=>'text',    'required'=>true, 'placeholder'=>'Beserta gelar'],
        $f['tembusan'],
    ],
];
