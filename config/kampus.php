<?php
/**
 * config/kampus.php — Konfigurasi IAI Al-Aqidah Al-Hasyimiyyah Jakarta
 * =======================================================================
 * Satu file ini mengatur identitas di SEMUA surat sekaligus.
 * Edit bagian ini sesuai kebutuhan — perubahan berlaku otomatis.
 */

return [

    // -------------------------------------------------------
    // IDENTITAS UTAMA
    // -------------------------------------------------------
    'nama'       => 'Institut Agama Islam Al-Aqidah Al-Hasyimiyyah Jakarta',
    'singkatan'  => 'IAI Al-Aqidah',
    'kode'       => 'IAI-AA',           // dipakai di nomor surat
    'kota'       => 'Jakarta',
    'website'    => 'www.alaqidah.ac.id',
    'email'      => 'info@alaqidah.ac.id',
    'telepon'    => '0851-5887-3469',
    'alamat'     => 'Jl. Kayumanis Barat No. 99 Matraman, Jakarta Timur 13130',

    // -------------------------------------------------------
    // PEJABAT
    // Key dipakai di template: ks_ttd('rektor'), ks_ttd('warek1'), dst.
    // -------------------------------------------------------
    'pejabat' => [
        'rektor' => [
            'nama'    => 'Dr. Muslihan Habib, S.S., M.A',
            'nuptk'   => '1046750651131093',
            'jabatan' => 'Rektor IAI Al-Aqidah Al-Hasyimiyyah Jakarta',
        ],
        'warek1' => [
            'nama'    => 'Dr. Ilyas Ichsani, M.Hum',
            'nuptk'   => '6652759660130192',
            'jabatan' => 'Wakil Rektor I Bidang Akademik',
        ],
        'warek2' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Wakil Rektor II Bidang Keuangan & Umum',
        ],

        // --- Dekan ---
        'dekan_tarbiyah' => [
            'nama'    => 'Dr. Joko Nugroho, S.T., M.M',
            'nuptk'   => '7250752653130093',
            'jabatan' => 'Dekan Fakultas Tarbiyah',
            'ttd_file'=> 'ttd_joko.png'
        ],
        'dekan_dakwah' => [
            'nama'    => 'Sugiyono, S.Sos., M.IP',
            'nuptk'   => '0261762663130243',
            'jabatan' => 'Dekan Fakultas Dakwah dan Syariah',
            'ttd_file'=> 'ttd_sugiyono.png'
        ],

        // --- Kaprodi S1 Tarbiyah ---
        'kaprodi_pai_s1' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Ketua Program Studi PAI (S1)',
        ],
        'kaprodi_pgmi' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Ketua Program Studi PGMI',
        ],
        'kaprodi_piaud' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Ketua Program Studi PIAUD',
        ],

        // --- Kaprodi S1 Dakwah & Syariah ---
        'kaprodi_kpi' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Ketua Program Studi KPI',
        ],
        'kaprodi_hes' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Ketua Program Studi HES',
        ],
        'kaprodi_as' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Ketua Program Studi AS',
        ],

        // --- Direktur / Dekan Pascasarjana ---
        'dekan_pascasarjana' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Direktur Program Pascasarjana',
        ],

        // --- Kaprodi S2 ---
        'kaprodi_pai_s2' => [
            'nama'    => '______________________',
            'nuptk'   => '__________________',
            'jabatan' => 'Ketua Program Studi PAI (S2)',
        ],

        // --- Kepala LPP ---
        'kepala_lpp' => [
            'nama'    => 'Muh. Bukhari. S.Pd, M.Si',
            'nuptk'   => '__________________',
            'jabatan' => 'Kepala Lembaga Penelitian & Publikasi (LPP)',
        ],

        // --- Kepala PERPUSTAKAAN ---
        'kepala_perpustakaan' => [
            'nama'    => 'Dr. Pipin Yosepin, M.Sos',
            'nuptk'   => '8452749650230092',
            'jabatan' => 'Kepala Perpustakaan',
        ],
    ],

    // -------------------------------------------------------
    // STRUKTUR FAKULTAS & PROGRAM STUDI
    // Dipakai untuk dropdown di form dan label otomatis.
    // -------------------------------------------------------
    'jenjang' => [

        'S1' => [
            'label' => 'Strata 1 (S1)',
            'fakultas' => [

                'Tarbiyah' => [
                    'label'        => 'Fakultas Tarbiyah',
                    'dekan_key'    => 'dekan_tarbiyah',   // key pejabat di atas
                    'prodi' => [
                        'PAI'   => [
                            'label'        => 'Pendidikan Agama Islam (PAI)',
                            'kaprodi_key'  => 'kaprodi_pai_s1',
                        ],
                        'PGMI'  => [
                            'label'        => 'Pendidikan Guru Madrasah Ibtidaiyah (PGMI)',
                            'kaprodi_key'  => 'kaprodi_pgmi',
                        ],
                        'PIAUD' => [
                            'label'        => 'Pendidikan Islam Anak Usia Dini (PIAUD)',
                            'kaprodi_key'  => 'kaprodi_piaud',
                        ],
                    ],
                ],

                'Dakwah' => [
                    'label'        => 'Fakultas Dakwah dan Syariah',
                    'dekan_key'    => 'dekan_dakwah',
                    'prodi' => [
                        'KPI' => [
                            'label'        => 'Komunikasi dan Penyiaran Islam (KPI)',
                            'kaprodi_key'  => 'kaprodi_kpi',
                        ],
                        'HES' => [
                            'label'        => 'Hukum Ekonomi Syariah (HES)',
                            'kaprodi_key'  => 'kaprodi_hes',
                        ],
                        'AS'  => [
                            'label'        => 'Ahwal As-Syakhsyiah (AS)',
                            'kaprodi_key'  => 'kaprodi_as',
                        ],
                    ],
                ],

            ],
        ],

        'S2' => [
            'label' => 'Strata 2 (S2)',
            'fakultas' => [
                'Pascasarjana' => [
                    'label'        => 'Program Pascasarjana',
                    // 'dekan_key'    => 'dekan_pascasarjana',
                    'dekan_key' => 'dekan_tarbiyah',
                    'prodi' => [
                        'PAI-S2' => [
                            'label'        => 'Pendidikan Agama Islam (PAI) — S2',
                            'kaprodi_key'  => 'kaprodi_pai_s2',
                        ],
                    ],
                ],
            ],
        ],

    ],

];
