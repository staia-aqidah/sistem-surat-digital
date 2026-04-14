<?php
/**
 * letter-registry.php
 *
 * Tugasnya SATU: scan folder /letters/ dan kembalikan
 * semua jenis surat yang tersedia.
 *
 * Cara menambah jenis surat baru:
 *   1. Buat folder baru di: wp-content/plugins/kampus-surat/letters/nama-surat/
 *   2. Buat file config.php di dalam folder tersebut
 *   3. Buat file template.php di dalam folder tersebut
 *   4. Selesai — surat otomatis muncul di form
 */

defined('ABSPATH') || exit;

/**
 * Kembalikan semua jenis surat yang terdaftar.
 * Menggunakan static cache agar tidak baca disk berkali-kali.
 *
 * @return array [ 'slug' => [ 'name'=>..., 'category'=>..., 'fields'=>[...] ] ]
 */
function ks_get_all_letters(): array {
    static $letters = null;
    if ($letters !== null) return $letters;

    $letters     = [];
    $letters_dir = KS_PATH . 'letters/';

    if (!is_dir($letters_dir)) return $letters;

    foreach (scandir($letters_dir) as $folder) {
        // Lewati entry khusus
        if ($folder === '.' || $folder === '..') continue;

        $config_file = $letters_dir . $folder . '/config.php';

        // Wajib ada config.php dan template.php
        if (!file_exists($config_file)) continue;
        if (!file_exists($letters_dir . $folder . '/template.php')) continue;

        $config = include $config_file;

        if (is_array($config) && !empty($config['name'])) {
            $letters[$folder] = $config;
        }
    }

    // Urutkan: kategori dulu, lalu nama alfabet
    uasort($letters, function ($a, $b) {
        $cat_order = ['mahasiswa' => 0, 'dosen' => 1, 'umum' => 2];
        $cat_a = $cat_order[$a['category'] ?? 'umum'] ?? 99;
        $cat_b = $cat_order[$b['category'] ?? 'umum'] ?? 99;

        if ($cat_a !== $cat_b) return $cat_a - $cat_b;
        return strcmp($a['name'], $b['name']);
    });

    return $letters;
}

/**
 * Ambil satu jenis surat berdasarkan slug.
 *
 * @param  string      $slug  Nama folder surat
 * @return array|null
 */
function ks_get_letter(string $slug): ?array {
    // Validasi slug: hanya boleh huruf, angka, dan strip
    if (!preg_match('/^[a-z0-9\-]+$/', $slug)) return null;

    $letters = ks_get_all_letters();
    return $letters[$slug] ?? null;
}
