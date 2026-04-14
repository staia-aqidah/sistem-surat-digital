<?php
/**
 * pdf-generator.php
 * Generate PDF menggunakan Dompdf — hasilnya konsisten di semua device.
 *
 * Cara install Dompdf:
 * 1. Download zip dari: https://github.com/dompdf/dompdf/releases
 *    Pilih file: dompdf-X.X.X.zip (bukan source code)
 * 2. Extract → rename folder menjadi "dompdf"
 * 3. Upload ke: wp-content/plugins/iai-surat/libs/dompdf/
 * 4. Pastikan ada file: libs/dompdf/autoload.inc.php
 */

defined('ABSPATH') || exit;

// Pastikan KS_Logger tersedia — load manual jika belum
if (!class_exists('KS_Logger')) {
    require_once KS_PATH . 'includes/logger.php';
}

function ks_generate_pdf(string $raw_token): void {
    $token   = sanitize_text_field($raw_token);
    $payload = get_transient('ks_surat_' . $token);

    if (!$payload) {
        KS_Logger::warn('PDF', 'Transient tidak ditemukan saat generate PDF', [
            'token'       => substr($token, 0, 8) . '...',
            'kemungkinan' => 'Link > 30 menit, atau transient dihapus cache plugin',
        ]);
        wp_die('<h2>&#x23F0; Link Kadaluarsa</h2><p>Silakan buat surat baru.</p><p><a href="javascript:history.back()">&#x2190; Kembali</a></p>');
    }

    // Cek Dompdf sudah diinstall
    $autoload = KS_PATH . 'libs/dompdf/autoload.inc.php';
    if (!file_exists($autoload)) {
        wp_die(
            '<h2>Dompdf Belum Diinstall</h2>' .
            '<p>Download Dompdf dari <a href="https://github.com/dompdf/dompdf/releases" target="_blank">GitHub</a>, ' .
            'extract, dan upload ke folder: <code>wp-content/plugins/iai-surat/libs/dompdf/</code></p>' .
            '<p>Pastikan ada file: <code>libs/dompdf/autoload.inc.php</code></p>'
        );
    }

    require_once $autoload;

    $jenis   = $payload['jenis'];
    $letter  = $payload['letter'];
    $data    = $payload['data'];
    $tanggal = date_i18n('d F Y');
    $kampus  = ks_get_kampus();

    $tmpl = KS_PATH . 'letters/' . $jenis . '/template.php';
    if (!file_exists($tmpl)) wp_die('Template tidak ditemukan.');

    // Render template surat
    ob_start();
    include $tmpl;
    $surat_html = ob_get_clean();

    // Pisahkan kop dari konten — kop full width, konten diberi padding
    // Kop selalu berupa div.ks-kop-image di awal template
    $kop_end_pos = strpos($surat_html, '</div>', strpos($surat_html, 'ks-kop-image'));
    if ($kop_end_pos !== false) {
        $kop_html     = substr($surat_html, 0, $kop_end_pos + 6);
        $isi_html     = substr($surat_html, $kop_end_pos + 6);
        $surat_html   = $kop_html . '<div class="ks-content-wrap">' . $isi_html . '</div>';
    }

    // Setup Dompdf — dipakai ulang untuk kedua render pass
    $options = new Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled',         false);
    $options->set('isRemoteEnabled',      false);
    $options->set('defaultFont',          'DejaVu Sans');
    $options->set('defaultMediaType',     'print');
    $options->setChroot(ABSPATH);

    // ── PASS 1: Render dengan CSS normal (untuk konten pendek/sedang) ──
    $html = ks_build_pdf_html($surat_html, $letter['name'], false);

    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $page_count = $dompdf->getCanvas()->get_page_count();

    // ── PASS 2: Jika lebih dari 1 halaman, render ulang dengan CSS rapat ──
    if ($page_count > 1) {
        $html = ks_build_pdf_html($surat_html, $letter['name'], true);

        $dompdf = new Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    }

    // Log hasil akhir
    KS_Logger::info('PDF', 'PDF berhasil digenerate dan distream', [
        'jenis'      => $jenis,
        'halaman'    => $page_count > 1 ? 'compact (2+ hal → dipadatkan)' : 'normal (1 halaman)',
        'token'      => substr($token, 0, 8) . '...',
    ]);

    // Stream PDF ke browser
    $nama_pengaju = $data['nama'] ?? $data['nama_dosen'] ?? '';
    $nama_pengaju = strtoupper(preg_replace('/\s+/', '-', trim($nama_pengaju)));
    $nama_surat   = sanitize_file_name($letter['name']);

    $filename = $nama_surat . '_' . $nama_pengaju . '.pdf';
    $filename = sanitize_file_name($filename);
    $dompdf->stream($filename, ['Attachment' => false]);
    exit;
}

/**
 * Bangun HTML lengkap yang kompatibel dengan Dompdf.
 * Dompdf tidak support flexbox, grid, atau CSS modern.
 * TTD menggunakan table, layout pakai float/margin klasik.
 */
function ks_build_pdf_html(string $surat_html, string $judul, bool $compact = false): string {
    /*
     * $compact = false → CSS normal  (konten pendek/sedang — tampilan tidak berubah)
     * $compact = true  → CSS rapat   (konten panjang — dikecilkan agar muat 1 halaman)
     * Nilai kompak dipilih otomatis oleh ks_generate_pdf() setelah cek page_count.
     */

    // Kop: pakai path absolut server agar Dompdf bisa baca
    $kop_path = '';
    foreach (['jpg','jpeg','png','webp'] as $ext) {
        $path = KS_PATH . 'assets/kop-surat.' . $ext;
        if (file_exists($path)) {
            $kop_path = $path;
            break;
        }
    }

    // Ganti URL kop di HTML dengan path absolut
    if ($kop_path) {
        $kop_url = KS_URL . 'assets/kop-surat.' . pathinfo($kop_path, PATHINFO_EXTENSION);
        $surat_html = str_replace($kop_url, $kop_path, $surat_html);
    }

    // Ganti URL gambar TTD dengan base64 — Dompdf tidak bisa load URL eksternal
    // Scan semua pejabat yang punya ttd_file, konversi ke base64
    $kampus_cfg = ks_get_kampus();
    foreach ($kampus_cfg['pejabat'] ?? [] as $pejabat) {
        if (empty($pejabat['ttd_file'])) continue;
        $ttd_url  = KS_URL . 'assets/ttd/' . basename($pejabat['ttd_file']);
        $ttd_b64  = ks_ttd_image_base64($pejabat['ttd_file']);
        if ($ttd_b64 && strpos($surat_html, $ttd_url) !== false) {
            $surat_html = str_replace($ttd_url, $ttd_b64, $surat_html);
        }
    }

    // CSS berbeda berdasarkan mode compact
    $fs_body   = $compact ? '10.5pt' : '11.5pt';
    $lh_body   = $compact ? '1.4'    : '1.5';
    $fs_tabel  = $compact ? '10pt'   : '11pt';
    $lh_para   = $compact ? '1.5'    : '1.7';
    $mb_para   = $compact ? '4px'    : '6px';
    $mt_header = $compact ? '6px 0 5px' : '10px 0 8px';
    $mt_ttd    = $compact ? '10px'   : '16px';
    $h_ruang   = $compact ? '50px'   : '60px';
    $pad_wrap  = $compact ? '6mm 25mm 12mm 25mm' : '8mm 25mm 15mm 25mm';

    return '<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>' . esc_html($judul) . '</title>
<style>
@page {
    size: A4 portrait;
    margin: 0;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: "Times New Roman", Times, serif;
    font-size: ' . $fs_body . ';
    color: #000;
    line-height: ' . $lh_body . ';
    width: 210mm;
}

/* Kop — full width 210mm, flush ke atas kiri */
.ks-kop-image {
    width: 210mm;
    margin: 0;
    display: block;
}
.ks-kop-image img {
    width: 210mm;
    height: auto;
    display: block;
}

/* Wrapper konten — padding kiri/kanan sebagai margin surat */
.ks-content-wrap {
    padding: ' . $pad_wrap . ';
}

/* Header Nomor/Hal/Lamp */
.ks-header-surat {
    width: 100%;
    border-collapse: collapse;
    font-size: 11pt;
    margin: ' . $mt_header . ';
}
.ks-header-surat td { padding: 1px 4px; vertical-align: top; }
.ks-header-surat td:first-child { width: 55px; }
.ks-header-surat td:nth-child(2) { width: 10px; }

/* Kepada */
.ks-kepada { margin: 10px 0; font-size: 11pt; line-height: 1.7; }
.ks-kepada p { margin: 0; }

/* Judul */
.ks-judul {
    text-align: center; font-size: 12.5pt; font-weight: bold;
    text-decoration: underline; text-transform: uppercase;
    letter-spacing: 1px; margin: 14px 0 3px;
}
.ks-nomor-tengah { text-align: center; font-size: 11pt; margin: 0 0 14px; }

/* Body */
.ks-body p { text-align: justify; line-height: ' . $lh_para . '; margin: 0 0 ' . $mb_para . '; }

/* Tabel data */
.ks-tabel-data {
    width: 95%;
    margin: 2px 0 8px 14px;
    border-collapse: collapse;
    font-size: 11pt;
}
.ks-tabel-data td { padding: 1px 4px; vertical-align: top; }
.ks-tabel-data td:first-child { width: 155px; }
.ks-tabel-data td:nth-child(2) { width: 12px; }

/* Tabel formal */
.ks-tabel-formal {
    width: 100%;
    border-collapse: collapse;
    font-size: ' . $fs_tabel . ';
    margin: 6px 0;
}
.ks-tabel-formal th {
    border: 1px solid #000; padding: 3px 6px;
    background-color: #f5f5f5; text-align: center; font-weight: bold;
}
.ks-tabel-formal td { border: 1px solid #000; padding: 3px 6px; vertical-align: top; }
.ks-tabel-formal .center { text-align: center; }

/* TTD satu — float kanan, lebar cukup untuk jabatan panjang */
.ks-ttd-block {
    float: right;
    width: 250px;
    text-align: center;
    font-size: 11pt;
    margin-top: ' . $mt_ttd . ';
}
.ks-ttd-block p { margin: 1px 0; font-size: 11pt; }
.ks-ruang-ttd { height: ' . $h_ruang . '; width: 100%; display: block; text-align: center; }
.ks-ttd-img {
    height: ' . ($compact ? '60px' : '75px') . ';
    width: auto;
    max-width: 220px;
    display: block;
    margin: 0 auto;
}
.ks-ttd-nama { font-weight: bold; text-decoration: underline; font-size: 11pt; display: block; }
.ks-ttd-nip { font-size: 10pt; }

/* TTD dua kolom — pakai table, Dompdf support penuh */
.ks-ttd-dua { width: 100%; margin-top: ' . $mt_ttd . '; display: table; }
.ks-ttd-item { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
.ks-ttd-item p { margin: 1px 0; }

/* Tembusan */
.ks-tembusan { margin-top: 12px; font-size: 11pt; clear: both; }
.ks-tembusan p { margin: 0 0 2px; }
.ks-tembusan ol { margin: 0; padding-left: 24px; line-height: 1.7; }

/* Materai */
.ks-materai {
    border: 1px dashed #999; width: 85px; height: 85px;
    text-align: center; font-size: 8pt; color: #aaa;
    padding-top: 28px; margin: 3px auto;
}

/* Utility */
.ks-clear { clear: both; }
.ks-center { text-align: center; }
.ks-bold { font-weight: bold; }
.ks-italic { font-style: italic; }
.ks-underline { text-decoration: underline; }
.ks-indent { margin-left: 24px; }

/* Footer */
.ks-footer-surat {
    clear: both;
    margin-top: 8px;
    border-top: 0.5px solid #ccc;
    padding-top: 3px;
    font-size: 6.5pt;
    color: #888;
    font-family: Arial, Helvetica, sans-serif;
    text-align: center;
}
</style>
</head>
<body>
' . $surat_html . '
</body>
</html>';
}
