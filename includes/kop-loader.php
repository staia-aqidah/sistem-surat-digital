<?php
/**
 * kop-loader.php
 * Helper functions untuk kop, TTD, dan data kampus IAI.
 */

defined('ABSPATH') || exit;

// ============================================================
// LOAD CONFIG KAMPUS
// ============================================================

function ks_get_kampus(): array {
    static $config = null;
    if ($config !== null) return $config;
    $file   = KS_PATH . 'config/kampus.php';
    $config = file_exists($file) ? (include $file) : [];
    return $config;
}

function ks_pejabat(string $key): array {
    $k = ks_get_kampus();
    return $k['pejabat'][$key] ?? ['nama' => '_________________', 'nuptk' => '_________________', 'jabatan' => '_________________'];
}

// ============================================================
// HELPER: Dari jenjang + kode prodi, ambil data fakultas & prodi
// ============================================================

function ks_get_prodi_data(string $jenjang, string $prodi_kode): array {
    $k = ks_get_kampus();
    foreach ($k['jenjang'][$jenjang]['fakultas'] ?? [] as $fak_key => $fak) {
        if (isset($fak['prodi'][$prodi_kode])) {
            return [
                'prodi'         => $fak['prodi'][$prodi_kode],
                'prodi_kode'    => $prodi_kode,
                'prodi_label'   => $fak['prodi'][$prodi_kode]['label'],
                'fakultas'      => $fak,
                'fakultas_key'  => $fak_key,
                'fakultas_label'=> $fak['label'],
                'kaprodi'       => ks_pejabat($fak['prodi'][$prodi_kode]['kaprodi_key']),
                'dekan'         => ks_pejabat($fak['dekan_key']),
            ];
        }
    }
    return [];
}

// ============================================================
// DROPDOWN OPTIONS untuk form
// ============================================================

/** Kembalikan array options untuk select jenjang */
function ks_options_jenjang(): array {
    $opts = [];
    foreach (ks_get_kampus()['jenjang'] ?? [] as $key => $j) {
        $opts[$key] = $j['label'];
    }
    return $opts;
}

/** Kembalikan options prodi flattened untuk select (value = "S1|PAI") */
function ks_options_prodi(): array {
    $opts = [];
    foreach (ks_get_kampus()['jenjang'] as $jenjang_key => $jenjang) {
        foreach ($jenjang['fakultas'] as $fak_key => $fak) {
            $fak_label = $fak['label'];
            foreach ($fak['prodi'] as $prodi_kode => $prodi) {
                $value         = $jenjang_key . '|' . $prodi_kode;
                $opts[$value]  = "[{$jenjang_key}] {$prodi['label']} — {$fak_label}";
            }
        }
    }
    return $opts;
}

// ============================================================
// RENDER KOP
// ============================================================

function ks_kop(string $variant = 'image'): string {
    $kop_file = KS_PATH . 'kop/' . sanitize_file_name($variant) . '.php';
    if (!file_exists($kop_file)) $kop_file = KS_PATH . 'kop/image.php';
    $kampus = ks_get_kampus();
    ob_start();
    include $kop_file;
    return ob_get_clean();
}

// ============================================================
// HELPER: Ambil URL dan path absolut gambar TTD pejabat
// ============================================================

function ks_ttd_image_url(string $ttd_file): string {
    if (empty($ttd_file)) return '';
    $clean = basename($ttd_file); // keamanan: hanya nama file, no path traversal
    $path  = KS_PATH . 'assets/ttd/' . $clean;
    return file_exists($path) ? KS_URL . 'assets/ttd/' . $clean : '';
}

function ks_ttd_image_path(string $ttd_file): string {
    if (empty($ttd_file)) return '';
    $clean = basename($ttd_file);
    $path  = KS_PATH . 'assets/ttd/' . $clean;
    return file_exists($path) ? $path : '';
}

/**
 * Konversi gambar TTD ke base64 untuk Dompdf.
 * Dompdf tidak bisa load URL — harus embed base64.
 */
function ks_ttd_image_base64(string $ttd_file): string {
    $path = ks_ttd_image_path($ttd_file);
    if (!$path) return '';
    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
    $data = base64_encode(file_get_contents($path));
    return 'data:' . $mime . ';base64,' . $data;
}

/**
 * Render HTML gambar TTD — untuk browser (pakai URL) atau Dompdf (pakai base64).
 * $mode: 'url' untuk browser, 'base64' untuk Dompdf
 */
function ks_ttd_image_html(string $ttd_file, string $mode = 'url'): string {
    if (empty($ttd_file)) return '';
    $src = ($mode === 'base64')
        ? ks_ttd_image_base64($ttd_file)
        : ks_ttd_image_url($ttd_file);
    if (!$src) return '';
    // mix-blend-mode: multiply — membuat background putih JPG/PNG menyatu
    // dengan background putih kertas sehingga terlihat transparan
    return '<img src="' . esc_attr($src) . '" class="ks-ttd-img" alt="TTD">';
}

// ============================================================
// RENDER TTD
// ============================================================

function ks_ttd(string $pejabat_key, string $an = ''): string {
    $p      = ks_pejabat($pejabat_key);
    $kampus = ks_get_kampus();
    return ks_ttd_render(
        $p['jabatan'], $an, $p['nama'],
        $p['nuptk'] ?? $p['nip'] ?? '',
        $kampus['kota'] ?? 'Jakarta',
        $p['ttd_file'] ?? ''
    );
}

function ks_ttd_custom(string $jabatan, string $an = '', string $nama = '', string $nuptk = '', string $kota = '', string $ttd_file = ''): string {
    $kampus = ks_get_kampus();
    return ks_ttd_render($jabatan, $an, $nama, $nuptk, $kota ?: ($kampus['kota'] ?? 'Jakarta'), $ttd_file);
}

function ks_ttd_render(string $jabatan, string $an, string $nama, string $nuptk, string $kota, string $ttd_file = ''): string {
    $ttd_html = ks_ttd_image_html($ttd_file, 'url');
    ob_start(); ?>
    <div class="ks-ttd-block">
        <p><?= esc_html($kota) ?>, ___________________</p>
        <?php if ($an): ?><p><?= esc_html($an) ?>,</p><?php endif; ?>
        <p style="white-space:normal;word-break:break-word;"><?= esc_html($jabatan) ?>,</p>
        <div class="ks-ruang-ttd">
            <?php if ($ttd_html): ?>
                <?= $ttd_html ?>
            <?php endif; ?>
        </div>
        <span class="ks-ttd-nama"><?= esc_html($nama) ?></span>
        <?php if ($nuptk): ?><p class="ks-ttd-nip">NUPTK. <?= esc_html($nuptk) ?></p><?php endif; ?>
    </div>
    <?php return ob_get_clean();
}

function ks_ttd_dua(string $kiri_key, string $kanan_key): string {
    $kiri   = ks_pejabat($kiri_key);
    $kanan  = ks_pejabat($kanan_key);
    $kampus = ks_get_kampus();
    $kota   = $kampus['kota'] ?? 'Jakarta';
    ob_start(); ?>
    <div class="ks-ttd-dua">
        <div class="ks-ttd-item">
            <p><?= esc_html($kota) ?>, ___________________</p>
            <p style="white-space:normal;word-break:break-word;"><?= esc_html($kiri['jabatan']) ?>,</p>
            <div class="ks-ruang-ttd">
                <?= ks_ttd_image_html($kiri['ttd_file'] ?? '', 'url') ?>
            </div>
            <span class="ks-ttd-nama"><?= esc_html($kiri['nama']) ?></span>
            <p class="ks-ttd-nip">NUPTK. <?= esc_html($kiri['nuptk'] ?? '') ?></p>
        </div>
        <div class="ks-ttd-item">
            <p><?= esc_html($kota) ?>, ___________________</p>
            <p style="white-space:normal;word-break:break-word;"><?= esc_html($kanan['jabatan']) ?>,</p>
            <div class="ks-ruang-ttd">
                <?= ks_ttd_image_html($kanan['ttd_file'] ?? '', 'url') ?>
            </div>
            <span class="ks-ttd-nama"><?= esc_html($kanan['nama']) ?></span>
            <p class="ks-ttd-nip">NUPTK. <?= esc_html($kanan['nuptk'] ?? '') ?></p>
        </div>
    </div>
    <div class="ks-clear"></div>
    <?php return ob_get_clean();
}

// ============================================================
// BLOK NOMOR / HAL / LAMP (header surat standar IAI)
// ============================================================

function ks_header_surat(string $hal, string $kode_surat = '', string $lamp = '-'): string {
    $tahun  = date('Y');
    $bulan  = ks_bulan_romawi();
    // Format: _____ / IAI-AA.S-Ket / _____ / 2025
    // Bagian _____ diisi oleh Tata Usaha saat menandatangani
    $nomor_template = $kode_surat
        ? '_____ / ' . esc_html($kode_surat) . ' / _____ / ' . $tahun
        : '';
    ob_start(); ?>
    <table class="ks-header-surat">
        <tr>
            <td>Nomor</td><td>:</td>
            <td><?= $nomor_template ?></td>
        </tr>
        <tr><td>Hal</td><td>:</td><td><strong><?= esc_html($hal) ?></strong></td></tr>
        <tr><td>Lamp</td><td>:</td><td><?= esc_html($lamp) ?></td></tr>
    </table>
    <?php return ob_get_clean();
}

function ks_bulan_romawi(): string {
    return ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][(int)date('n') - 1];
}

// ============================================================
// FOOTER
// ============================================================

function ks_footer(): string {
    $k = ks_get_kampus();
    return '<div class="ks-footer-surat">Dokumen ini dicetak melalui Sistem Surat Digital ' .
        esc_html($k['nama'] ?? 'IAI Al-Aqidah Al-Hasyimiyyah') .
        ' pada ' . date_i18n('d F Y') . '. Sah setelah ditandatangani dan dicap basah oleh pejabat berwenang.</div>';
}
