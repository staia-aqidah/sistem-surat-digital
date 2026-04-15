<?php
$f = include KS_PATH . 'config/fields-shared.php';
return [
    'name'       => 'Surat Tugas Dosen (LPPM)',
    'nomor_kode' => 'IAI-AA.LPPM/ST',
    'category'   => 'dosen',
    'info'       => 'Surat tugas dosen terkait untuk Pengabdian Masyarakat, ditandatangani Kepala LPPM.',
    'petunjuk'   => 'Penandatangan default adalah Kepala LPPM. Pilih pejabat lain jika diperlukan.',
    'fields'     => [
        // Data dosen
        $f['nama_dosen'],
        $f['nuptk_dosen'],
        $f['jabatan_dosen'],
        $f['prodi_dosen'],

        // Jenis tugas
        [
            'name'        => 'jenis_tugas',
            'label'       => 'Jenis Tugas',
            'type'        => 'select',
            'required'    => true,
            'options'     => [
                'mengajar'          => 'Mengajar / Pengajaran',
                'seminar'           => 'Seminar / Konferensi',
                'pengabdian'        => 'Pengabdian kepada Masyarakat',
                'pelatihan'         => 'Pelatihan / Workshop',
                'lainnya'           => 'Lainnya',
            ],
        ],
        [
            'name'        => 'nama_kegiatan',
            'label'       => 'Nama Kegiatan / Mata Kuliah',
            'type'        => 'text',
            'required'    => true,
            'placeholder' => 'Contoh: Pelatihan Metode Pembelajaran Aktif 2026',
        ],
        [
            'name'        => 'tempat',
            'label'       => 'Tempat Pelaksanaan',
            'type'        => 'text',
            'required'    => true,
            'placeholder' => 'Contoh: Aula Kampus IAI Al-Aqidah Jakarta',
        ],
        [
            'name'     => 'tanggal_mulai',
            'label'    => 'Tanggal Mulai',
            'type'     => 'date',
            'required' => true,
        ],
        [
            'name'     => 'tanggal_selesai',
            'label'    => 'Tanggal Selesai',
            'type'     => 'date',
            'required' => true,
        ],
        [
            'name'        => 'keterangan',
            'label'       => 'Keterangan Tambahan (opsional)',
            'type'        => 'textarea',
            'required'    => false,
            'rows'        => 3,
            'placeholder' => 'Informasi tambahan yang perlu dicantumkan dalam surat...',
        ],

        // ── PILIHAN PENANDATANGAN ──────────────────────────────
        // Default: Kepala LPP
        // Opsi lain: Rektor, Wakil Rektor I, Wakil Rektor II
        [
            'name'     => 'penandatangan',
            'label'    => 'Penandatangan Surat',
            'type'     => 'select',
            'required' => true,
            'help'     => 'Default: Kepala LPPM. Pilih pejabat lain jika surat perlu ditandatangani lebih tinggi.',
            'options'  => [
                'kepala_lppm' => 'Kepala LPPM — Moh. Amir Choiri, S.Sos, M.A (Default)',
                'rektor'     => 'Rektor — Dr. TGH. Muslihan Habib, SS, MA',
                'warek1'     => 'Wakil — Dr. Ilyas Ichsani, M.Hum',
            ],
        ],

        $f['tembusan'],
    ],
];
