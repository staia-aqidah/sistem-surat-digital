<?php
/**
 * config/fields-shared.php
 * -------------------------
 * Kumpulan definisi field yang sering dipakai berulang.
 * Di-include dari config.php masing-masing surat.
 *
 * Cara pakai di config.php surat:
 *   $f = include KS_PATH . 'config/fields-shared.php';
 *   ...
 *   'fields' => [ $f['nama'], $f['nim'], $f['prodi'], ... ]
 */

$kampus = include __DIR__ . '/kampus.php';

// Bangun options prodi flat: "S1|PAI" => "[S1] PAI — Fak. Tarbiyah"
$opts_prodi = [];
foreach ($kampus['jenjang'] as $jk => $j) {
    foreach ($j['fakultas'] as $fak_key => $fak) {
        foreach ($fak['prodi'] as $kode => $prodi) {
            $opts_prodi["{$jk}|{$kode}"] = "[{$jk}] " . $prodi['label'] . " — " . $fak['label'];
        }
    }
}

$opts_semester = ['1','2','3','4','5','6','7','8','9','10','11','12'];

return [

    // MAHASISWA
    'nama' => [
        'name' => 'nama', 'label' => 'Nama Lengkap Mahasiswa',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Sesuai Kartu Tanda Mahasiswa',
    ],
    'nim' => [
        'name' => 'nim', 'label' => 'NIM (Nomor Induk Mahasiswa)',
        'type' => 'text', 'required' => true,
    ],
    'prodi' => [
        'name' => 'prodi', 'label' => 'Program Studi',
        'type' => 'select', 'required' => true,
        'options' => $opts_prodi,
        'help' => 'Pilih jenjang dan program studi Anda.',
    ],
    'semester' => [
        'name' => 'semester', 'label' => 'Semester',
        'type' => 'select', 'required' => true,
        'options' => $opts_semester,
    ],
    'tahun_akademik' => [
        'name' => 'tahun_akademik', 'label' => 'Tahun Akademik',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Contoh: 2025/2026',
    ],
    'ipk' => [
        'name' => 'ipk', 'label' => 'IPK (Indeks Prestasi Kumulatif)',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Contoh: 3.75',
        'help' => 'Sesuai transkrip akademik terakhir (skala 4.00).',
    ],

    // DOSEN
    'nama_dosen' => [
        'name' => 'nama_dosen', 'label' => 'Nama Lengkap Dosen (beserta gelar)',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Contoh: Dr. Ahmad Fauzi, M.Pd.',
    ],
    'nuptk_dosen' => [
        'name' => 'nuptk_dosen', 'label' => 'NUPTK / NIDN Dosen',
        'type' => 'text', 'required' => true,
    ],
    'jabatan_dosen' => [
        'name' => 'jabatan_dosen', 'label' => 'Jabatan / Pangkat',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Contoh: Lektor / Dosen Tetap',
    ],
    'jabatan_struktur' => [
        'name' => 'jabatan_struktur', 'label' => 'Jabatan Struktur Dosen',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Contoh: Ketua Prodi Pendidikan Agama Islam',
    ],
    'jabatan_akademik' => [
        'name' => 'jabatan_akademik', 'label' => 'Jabatan Akademik Dosen',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Contoh: Penata Tk I / Lektor',
    ],
    'prodi_dosen' => [
        'name' => 'prodi_dosen', 'label' => 'Program Studi / Unit Kerja',
        'type' => 'select', 'required' => true,
        'options' => $opts_prodi,
    ],

    // KEPERLUAN
    'keperluan' => [
        'name' => 'keperluan', 'label' => 'Keperluan / Tujuan Surat',
        'type' => 'text', 'required' => true,
        'placeholder' => 'Contoh: Pengajuan Beasiswa Baznas 2025',
    ],

    // TEMBUSAN
    'tembusan' => [
        'name' => 'tembusan', 'label' => 'Tembusan (satu per baris, bagian ini OPSIONAL)',
        'type' => 'textarea', 'required' => false, 'rows' => 4,
        'placeholder' => "Wakil Rektor I Bidang Akademik\nKetua Program Studi\nArsip",
        'help' => 'Nomor urut otomatis. Kosongkan jika tidak ada tembusan.',
    ],
];
