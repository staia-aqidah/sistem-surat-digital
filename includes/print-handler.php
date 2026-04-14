<?php
defined('ABSPATH') || exit;
require_once KS_PATH . 'includes/kop-loader.php';

// Pastikan KS_Logger tersedia
if (!class_exists('KS_Logger')) {
    require_once KS_PATH . 'includes/logger.php';
}

function ks_handle_form_submit(): void {
    $jenis  = sanitize_key($_POST['ks_jenis'] ?? '');
    $letter = ks_get_letter($jenis);
    if (!$letter) wp_die('Jenis surat tidak valid.');

    $data = [];
    foreach ($_POST['ks_fields'] ?? [] as $k => $v) {
        $data[sanitize_key($k)] = sanitize_textarea_field($v);
    }
    foreach ($letter['fields'] as $f) {
        if (($f['required'] ?? false) && empty($data[$f['name']])) {
            wp_die('Field "<strong>' . esc_html($f['label']) . '</strong>" wajib diisi. <a href="javascript:history.back()">&#x2190; Kembali</a>');
        }
    }

    $token = wp_generate_uuid4();
    set_transient('ks_surat_' . $token, [
        'jenis'  => $jenis,
        'letter' => $letter,
        'data'   => $data,
    ], 30 * MINUTE_IN_SECONDS);

    KS_Logger::info('SUBMIT', 'Transient disimpan, redirect ke preview', [
        'jenis'      => $jenis,
        'token'      => substr($token, 0, 8) . '...',
        'expires_in' => '30 menit',
    ]);

    wp_redirect(home_url('/?ks_print=' . $token));
    exit;
}

function ks_render_print_page(string $raw_token): void {
    $token   = sanitize_text_field($raw_token);
    $payload = get_transient('ks_surat_' . $token);

    if (!$payload) {
        KS_Logger::warn('PRINT', 'Transient tidak ditemukan / expired', [
            'token'       => substr($token, 0, 8) . '...',
            'kemungkinan' => 'Link > 30 menit, atau transient dihapus cache plugin',
        ]); ?>
        <!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Kadaluarsa</title>
        <style>body{font-family:sans-serif;text-align:center;padding:60px;background:#f5f5f5}.box{background:#fff;border-radius:12px;padding:40px;display:inline-block}h2{color:#c62828}a{color:#155724}</style></head><body>
        <div class="box"><h2>&#x23F0; Link Kadaluarsa</h2><p>Silakan buat surat baru &mdash; link hanya berlaku 30 menit.</p><p><a href="javascript:history.back()">&#x2190; Kembali</a></p></div></body></html>
    <?php return; }

    $jenis   = $payload['jenis'];
    $letter  = $payload['letter'];
    $data    = $payload['data'];
    $tanggal = date_i18n('d F Y');
    $kampus  = ks_get_kampus();

    $tmpl = KS_PATH . 'letters/' . $jenis . '/template.php';
    if (!file_exists($tmpl)) wp_die('Template tidak ditemukan.');

    ob_start();
    include $tmpl;
    $surat_html = ob_get_clean();
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title>Cetak: <?= esc_html($letter['name']) ?></title>
        <style>
        /* ============================================================
           PRINT — zoom statis + margin mm + TTD tanpa float
        ============================================================ */
        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm 20mm 18mm 20mm;
            }

            * { box-sizing: border-box; }

            .ks-toolbar,
            .ks-notice,
            .ks-scale-info,
            .ks-scroll-hint { display: none !important; }

            body {
                margin: 0; padding: 0;
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .ks-paper-wrap {
                display: block !important;
                overflow: visible !important;
                padding: 0 !important;
            }

            .ks-paper {
                box-shadow: none !important;
                width: 170mm !important;
                max-width: 170mm !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                /* zoom 0.95 — hampir tidak terasa, cukup untuk mencegah footer ke hal 2 */
                zoom: 0.95 !important;
            }

            /* Kop full width — keluar dari batas paper */
            .ks-kop-image {
                margin-left: -20mm !important;
                margin-right: -20mm !important;
                margin-top: -15mm !important;
                width: calc(100% + 40mm) !important;
            }
            .ks-kop-image img { width: 100% !important; height: auto !important; }

            /* Rapatkan spacing isi surat */
            .ks-body p {
                line-height: 1.7 !important;
                margin-bottom: 5px !important;
            }
            .ks-tabel-data td { padding: 1px 4px !important; }
            .ks-header-surat { margin: 10px 0 8px !important; }

            /* TTD — hapus float, pakai block + margin auto agar ke kanan */
            .ks-clear { display: none !important; }
            .ks-ttd-block {
                float: none !important;
                width: 240px !important;
                margin: 14px 0 0 auto !important;
                text-align: center !important;
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
            }
            .ks-ttd-block p { margin: 1px 0 !important; }
            .ks-ruang-ttd { height: 55px !important; }
            .ks-ttd-nama {
                font-size: 11pt !important;
                white-space: normal !important;
                word-break: break-word !important;
                overflow: visible !important;
                text-overflow: unset !important;
            }

            /* TTD dua kolom */
            .ks-ttd-dua {
                display: table !important;
                width: 100% !important;
                margin-top: 14px !important;
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
            }
            .ks-ttd-dua .ks-ttd-item {
                display: table-cell !important;
                width: 50% !important;
                text-align: center !important;
            }

            /* Tembusan dan footer */
            .ks-tembusan {
                margin-top: 10px !important;
                page-break-inside: avoid !important;
            }
            .ks-footer-surat {
                margin-top: 6px !important;
                padding-top: 2px !important;
                font-size: 6.5pt !important;
                line-height: 1.2 !important;
                border-top: 0.3px solid #ccc !important;
                page-break-inside: avoid !important;
            }

            .ks-tabel-formal { page-break-inside: avoid !important; }
        }

                /* ============================================================
           SCREEN
        ============================================================ */
        @media screen {
            *, *::before, *::after { box-sizing: border-box; }

            body {
                margin: 0; padding: 0;
                background: #4a6741;
                font-family: 'Segoe UI', Arial, sans-serif;
            }

            .ks-toolbar {
                position: fixed; top: 0; left: 0; right: 0; height: 56px;
                background: #2d4a27; color: #fff;
                display: flex; align-items: center; justify-content: space-between;
                padding: 0 24px; z-index: 999;
                box-shadow: 0 2px 8px rgba(0,0,0,.4);
            }
            .ks-toolbar-title   { font-size: 15px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .ks-toolbar-actions { display: flex; gap: 10px; align-items: center; flex-shrink: 0; }
            .ks-btn-print {
                background: #5a8f4e; color: #fff; border: none;
                padding: 9px 22px; border-radius: 6px;
                font-size: 14px; font-weight: 700; cursor: pointer;
            }
            .ks-btn-print:hover { background: #3d6b32; }
            .ks-btn-pdf {
                background: #1565c0; color: #fff;
                padding: 9px 22px; border-radius: 6px;
                font-size: 14px; font-weight: 700;
                text-decoration: none; display: inline-block;
            }
            .ks-btn-pdf:hover { background: #0d47a1; color: #fff; }
            .ks-btn-back {
                color: rgba(255,255,255,.85);
                border: 1px solid rgba(255,255,255,.4);
                background: transparent; padding: 7px 14px;
                border-radius: 6px; font-size: 13px;
                cursor: pointer; text-decoration: none;
            }

            .ks-notice {
                max-width: 794px; margin: 72px auto 8px;
                background: #f1f8e9; border-left: 4px solid #7cb342;
                border-radius: 0 6px 6px 0;
                padding: 12px 16px; font-size: 13px; color: #33691e; line-height: 1.6;
            }
            .ks-scale-info {
                max-width: 794px; margin: 0 auto 6px;
                font-size: 12px; text-align: right;
                font-family: Arial, sans-serif;
                padding-right: 4px; min-height: 18px;
            }
            .ks-scroll-hint {
                display: none;
                max-width: 794px; margin: 0 auto 6px;
                font-size: 12px; color: #fff9; text-align: center;
                font-family: Arial, sans-serif; padding: 4px 0;
            }

            .ks-paper-wrap {
                overflow-x: auto;
                overflow-y: visible;
                padding-bottom: 48px;
                display: flex;
                justify-content: center;
            }

            .ks-paper {
                width: 794px;
                min-width: 794px;
                min-height: 1123px;
                background: #fff;
                padding: 28px 52px 36px;
                box-shadow: 0 6px 32px rgba(0,0,0,.25);
                flex-shrink: 0;
                transform-origin: top center;
            }
        }

        /* ============================================================
           ISI SURAT (screen + print)
        ============================================================ */
        .ks-paper { font-family: "Times New Roman", Times, serif; font-size: 12pt; color: #000; line-height: 1.6; }

        .ks-kop-image { margin-bottom: 0; }
        .ks-kop-image img { width: 100%; height: auto; display: block; }

        .ks-header-surat { margin: 14px 0 12px; border-collapse: collapse; font-size: 11.5pt; }
        .ks-header-surat td { padding: 1px 4px; vertical-align: top; }
        .ks-header-surat td:first-child { width: 55px; }
        .ks-header-surat td:nth-child(2) { width: 10px; }

        .ks-kepada { margin: 14px 0; font-size: 11.5pt; line-height: 1.9; }
        .ks-kepada p { margin: 0; }

        .ks-judul { text-align: center; font-size: 13pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; letter-spacing: 1px; margin: 18px 0 4px; }
        .ks-nomor-tengah { text-align: center; font-size: 11pt; margin: 0 0 18px; }

        .ks-body p { text-align: justify; line-height: 1.9; margin: 0 0 8px; }
        .ks-tabel-data { width: 95%; margin: 3px 0 10px 18px; border-collapse: collapse; font-size: 11.5pt; }
        .ks-tabel-data td { padding: 1px 4px; vertical-align: top; }
        .ks-tabel-data td:first-child { width: 160px; }
        .ks-tabel-data td:nth-child(2) { width: 12px; }

        .ks-tabel-formal { width: 100%; border-collapse: collapse; font-size: 11pt; margin: 8px 0; }
        .ks-tabel-formal th { border: 1px solid #000; padding: 4px 7px; background: #f5f5f5; text-align: center; font-weight: bold; }
        .ks-tabel-formal td { border: 1px solid #000; padding: 4px 7px; vertical-align: top; }
        .ks-tabel-formal .center { text-align: center; }

        /* TTD screen */
        .ks-ttd-block { margin-top: 24px; float: right; width: 260px; text-align: center; font-size: 11pt; }
        .ks-ttd-block p { margin: 2px 0; white-space: nowrap; }
        .ks-ruang-ttd { height: 100px; width: 100%; display: flex; align-items: flex-end; justify-content: center; }
        .ks-ttd-img {
            display: block;
            height: 90px;
            width: auto;
            max-width: 230px;
            object-fit: contain;
        }
        .ks-ttd-nama { font-weight: bold; text-decoration: underline; font-size: clamp(8pt, 2.8vw, 11.5pt); display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ks-ttd-nip { font-size: 10.5pt; white-space: nowrap; }
        .ks-ttd-dua { display: flex; justify-content: space-between; margin-top: 24px; font-size: 11pt; }
        .ks-ttd-dua .ks-ttd-item { width: 46%; text-align: center; }
        .ks-ttd-dua .ks-ttd-item p { margin: 2px 0; white-space: nowrap; }
        .ks-ttd-dua .ks-ttd-item .ks-ttd-nama { font-size: clamp(8pt, 2.2vw, 11.5pt); }

        .ks-tembusan { margin-top: 20px; font-size: 11pt; clear: both; }
        .ks-tembusan p { margin: 0 0 2px; }
        .ks-tembusan ol { margin: 0; padding-left: 26px; line-height: 1.8; }

        .ks-materai { border: 1px dashed #999; width: 90px; height: 90px; margin: 4px auto; display: flex; align-items: center; justify-content: center; font-size: 8pt; color: #aaa; text-align: center; line-height: 1.3; }

        .ks-clear { clear: both; }
        .ks-center { text-align: center; }
        .ks-bold { font-weight: bold; }
        .ks-italic { font-style: italic; }
        .ks-underline { text-decoration: underline; }
        .ks-indent { margin-left: 28px; }

        .ks-footer-surat { clear: both; margin-top: 16px; border-top: 1px solid #ccc; padding-top: 4px; font-size: 8pt; color: #888; font-family: Arial, sans-serif; text-align: center; }
        </style>
    </head>
    <body>
        <nav class="ks-toolbar">
            <span class="ks-toolbar-title">&#x1F4C4; <?= esc_html($letter['name']) ?></span>
            <div class="ks-toolbar-actions">
                <a href="javascript:history.back()" class="ks-btn-back">&#x2190; Kembali</a>
                <a href="<?= esc_url(home_url('/?ks_pdf=' . $token)) ?>" class="ks-btn-pdf" target="_blank">&#x2B07; Download PDF</a>
                <button onclick="window.print()" class="ks-btn-print">&#x1F5A8; Cetak Langsung</button>
            </div>
        </nav>

        <div class="ks-notice">
            <strong>&#x1F4CC; Cara Mendapatkan Surat:</strong>
            Klik <strong>"Download PDF"</strong> untuk mendapatkan file PDF yang konsisten di semua device.
            Atau klik <strong>"Cetak Langsung"</strong> untuk cetak via browser.
            Bawa cetakan ke <strong>Tata Usaha</strong> untuk tanda tangan basah dan cap stempel.
        </div>

        <div class="ks-scroll-hint" id="ks-scroll-hint">&#x1F4F1; Geser kanan-kiri untuk melihat surat &mdash; hasil cetak tetap A4 penuh</div>
        <div class="ks-scale-info" id="ks-scale-info"></div>

        <div class="ks-paper-wrap" id="ks-paper-wrap">
            <main class="ks-paper" id="ks-paper"><?= $surat_html ?></main>
        </div>

        <script>
        (function () {
            var PAPER_W_PX = 794;
            var paper   = document.getElementById('ks-paper');
            var wrap    = document.getElementById('ks-paper-wrap');
            var infoBox = document.getElementById('ks-scale-info');
            var hintBox = document.getElementById('ks-scroll-hint');

            function isMobile() {
                return window.innerWidth < PAPER_W_PX + 32;
            }

            /* Preview layar — scale down jika konten panjang */
            function initScreenScale() {
                var MM_TO_PX   = 96 / 25.4;
                var TARGET_H   = 262 * MM_TO_PX; /* ~990px */

                paper.style.transform    = '';
                paper.style.marginBottom = '';
                var h    = paper.scrollHeight;
                var zoom = Math.min(1, TARGET_H / h);
                zoom     = Math.max(0.5, parseFloat(zoom.toFixed(3)));

                if (zoom < 1) {
                    paper.style.transform       = 'scale(' + zoom + ')';
                    paper.style.transformOrigin = 'top center';
                    paper.style.marginBottom    = '-' + Math.ceil(h * (1 - zoom)) + 'px';
                    infoBox.textContent = 'Konten panjang — diperkecil ' + Math.round(zoom * 100) + '% di layar (cetak tetap A4)';
                    infoBox.style.color = '#e65100';
                } else {
                    infoBox.textContent = 'Surat muat 1 halaman A4';
                    infoBox.style.color = '#aaa';
                }
            }

            function adjustWrap() {
                if (isMobile()) {
                    wrap.style.justifyContent = 'flex-start';
                    wrap.style.padding        = '0 0 48px 12px';
                    hintBox.style.display     = 'block';
                } else {
                    wrap.style.justifyContent = 'center';
                    wrap.style.padding        = '0 0 48px 0';
                    hintBox.style.display     = 'none';
                }
            }

            function init() {
                adjustWrap();
                initScreenScale();
            }

            var img = paper.querySelector('img');
            if (img && !img.complete) {
                img.addEventListener('load',  init);
                img.addEventListener('error', init);
            } else {
                init();
            }

            window.addEventListener('resize', init);

            /* Setelah print kembalikan tampilan layar */
            window.addEventListener('afterprint', init);
        })();
        </script>
    </body>
    </html>
    <?php
}
