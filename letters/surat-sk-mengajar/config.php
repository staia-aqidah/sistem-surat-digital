<?php
$f = include KS_PATH . 'config/fields-shared.php';

// Bangun options prodi untuk kolom mata kuliah
$kampus = include KS_PATH . 'config/kampus.php';
$opts_prodi = [];
foreach ($kampus['jenjang'] as $jk => $j) {
    foreach ($j['fakultas'] as $fak_key => $fak) {
        foreach ($fak['prodi'] as $kode => $prodi) {
            $opts_prodi["{$jk}|{$kode}"] = "[{$jk}] " . $prodi['label'];
        }
    }
}

return [
    'name'     => 'SK Mengajar Dosen',
    'nomor_kode' => 'IAI-AA.SK-MK',
    'category' => 'dosen',
    'info'     => 'Surat Keputusan penugasan mengajar dosen dalam satu semester.',
    'petunjuk' => 'Nomor SK dan tanda tangan dilengkapi oleh Tata Usaha.',
    'fields'   => [
        $f['nama_dosen'], $f['nuptk_dosen'], $f['jabatan_dosen'], $f['prodi_dosen'],
        ['name'=>'tahun_akademik', 'label'=>'Tahun Akademik',   'type'=>'text',   'required'=>true, 'placeholder'=>'Contoh: 2024/2025'],
        ['name'=>'semester_mengajar','label'=>'Semester',       'type'=>'select', 'required'=>true,
            'options'=>['Ganjil'=>'Semester Ganjil','Genap'=>'Semester Genap']],
        ['name'=>'mata_kuliah',    'label'=>'Mata Kuliah yang Diampu (satu per baris)',
            'type'=>'textarea', 'required'=>true, 'rows'=>5,
            'placeholder'=>"Pendidikan Agama Islam | 2 SKS | PAI Semester 3\nFiqih Muamalah | 3 SKS | HES Semester 4",
            'help'=>'Format: Nama MK | SKS | Prodi & Semester. Satu baris per mata kuliah.'],
        $f['tembusan'],
    ],
];
