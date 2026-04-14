<?php
return [
    'name'     => 'Surat Rekomendasi Lolos Butuh',
    'nomor_kode' => 'IAI-AA.S-LB',
    'category' => 'dosen',
    'info'     => 'Rekomendasi pimpinan untuk persetujuan pindah homebase dosen.',
    'petunjuk' => 'Isi data sesuai dokumen NUPTK resmi. Nomor surat diisi oleh Tata Usaha.',
    'fields'   => [
        // Data tujuan surat
        ['name'=>'tujuan_jabatan',    'label'=>'Tujuan Surat (Jabatan Penerima)',   'type'=>'text',     'required'=>true,  'placeholder'=>'Contoh: Rektor IAI Pangeran Darma Kusuma Indramayu'],
        ['name'=>'tujuan_cq',         'label'=>'Cq. (opsional)',                    'type'=>'text',     'required'=>false, 'placeholder'=>'Contoh: Wakil Rektor Satu ...'],
        ['name'=>'tujuan_kota',       'label'=>'Kota Tujuan',                       'type'=>'text',     'required'=>true,  'placeholder'=>'Contoh: Indramayu'],
        // Data dosen yang direkomendasikan
        ['name'=>'dosen_nama',        'label'=>'Nama Dosen (yang direkomendasikan)','type'=>'text',     'required'=>true],
        ['name'=>'dosen_ttl',         'label'=>'Tempat, Tanggal Lahir',             'type'=>'text',     'required'=>true,  'placeholder'=>'Contoh: Indramayu, 23 September 1984'],
        ['name'=>'dosen_nuptk',       'label'=>'NUPTK Dosen',                       'type'=>'text',     'required'=>true],
        ['name'=>'dosen_pangkat',     'label'=>'Pangkat / Golongan Dosen',          'type'=>'text',     'required'=>true,  'placeholder'=>'Contoh: Penata Muda Tk I/IIIb'],
        ['name'=>'dosen_pendidikan',  'label'=>'Pendidikan Terakhir',               'type'=>'text',     'required'=>true,  'placeholder'=>'Contoh: S2 Universitas Gadjah Mada'],
        ['name'=>'dosen_unitkerja',   'label'=>'Unit Kerja Dosen (asal)',            'type'=>'text',     'required'=>true],
        ['name'=>'dosen_tujuan',      'label'=>'Tujuan Pindah Homebase (Institusi)','type'=>'text',     'required'=>true],
        // Rujukan surat edaran
        ['name'=>'nomor_se',          'label'=>'Nomor Surat Edaran Rujukan (opsional)','type'=>'text',  'required'=>false, 'placeholder'=>'Contoh: B-8755/R/HM.03.1/11/2024'],
        ['name'=>'tanggal_se',        'label'=>'Tanggal Surat Edaran',              'type'=>'date',     'required'=>false],
        ['name'=>'perihal_se',        'label'=>'Perihal Surat Edaran',              'type'=>'text',     'required'=>false],
        // Tembusan
        ['name'=>'tembusan',          'label'=>'Tembusan (satu per baris)',          'type'=>'textarea', 'required'=>false, 'rows'=>5,
            'placeholder'=>"Yayasan Dakwah Syiarul Islam (YADAI)\nWakil Rektor I Bidang Akademik\nKetua Program Studi\nYang bersangkutan\nArsip"],
    ],
];
