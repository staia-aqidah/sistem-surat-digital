<?php
/**
 * Plugin Name:  IAI Surat Digital
 * Description:  Sistem cetak surat digital IAI Al-Aqidah Al-Hasyimiyyah Jakarta.
 * Version:      2.1.0
 */

defined('ABSPATH') || exit;

define('KS_PATH',    plugin_dir_path(__FILE__));
define('KS_URL',     plugin_dir_url(__FILE__));
define('KS_VERSION', '2.1.0');

require_once KS_PATH . 'includes/logger.php';
require_once KS_PATH . 'includes/letter-registry.php';
require_once KS_PATH . 'includes/form-renderer.php';
require_once KS_PATH . 'includes/print-handler.php';
require_once KS_PATH . 'includes/pdf-generator.php';
require_once KS_PATH . 'includes/shortcode.php';

// Init logger sedini mungkin
add_action('init', function () { KS_Logger::init(); }, 1);

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('iai-surat-style', KS_URL . 'assets/style.css', [], KS_VERSION);
});

// ─────────────────────────────────────────────────────────────
// NONCE: Perpanjang masa berlaku ke 24 jam
// Default WP 12 jam terlalu pendek untuk halaman yang di-cache
// ─────────────────────────────────────────────────────────────
add_filter('nonce_life', function () {
    return 24 * HOUR_IN_SECONDS;
});

// ─────────────────────────────────────────────────────────────
// AJAX: Endpoint refresh nonce — dipanggil JS sebelum submit
// Memastikan nonce selalu fresh meski halaman dari cache
// ─────────────────────────────────────────────────────────────
add_action('wp_ajax_ks_refresh_nonce',        'ks_ajax_refresh_nonce');
add_action('wp_ajax_nopriv_ks_refresh_nonce', 'ks_ajax_refresh_nonce');

function ks_ajax_refresh_nonce(): void {
    KS_Logger::info('NONCE', 'AJAX refresh nonce berhasil');
    wp_send_json_success(['nonce' => wp_create_nonce('ks_form')]);
}

// ─────────────────────────────────────────────────────────────
// Cegah WordPress redirect canonical untuk halaman cetak/pdf
// ─────────────────────────────────────────────────────────────
add_filter('redirect_canonical', function ($redirect_url) {
    if (isset($_GET['ks_print']) || isset($_GET['ks_pdf'])) {
        return false;
    }
    return $redirect_url;
});

add_action('template_redirect', function () {
    if (isset($_GET['ks_print'])) {
        $token = sanitize_text_field($_GET['ks_print']);
        KS_Logger::info('PRINT', 'Halaman preview dibuka', ['token' => substr($token, 0, 8) . '…']);
        ks_render_print_page($token);
        exit;
    }
    if (isset($_GET['ks_pdf'])) {
        $token = sanitize_text_field($_GET['ks_pdf']);
        KS_Logger::info('PDF', 'Download PDF dimulai', ['token' => substr($token, 0, 8) . '…']);
        ks_generate_pdf($token);
        exit;
    }
});

// ─────────────────────────────────────────────────────────────
// FORM SUBMIT — dengan logging lengkap dan error handling
// ─────────────────────────────────────────────────────────────
add_action('init', function () {

    if (!isset($_POST['ks_submit'])) return;

    $jenis = sanitize_key($_POST['ks_jenis'] ?? '');

    // Log setiap POST masuk
    KS_Logger::info('SUBMIT', 'Form submit diterima', [
        'jenis'     => $jenis,
        'has_nonce' => isset($_POST['ks_nonce']) ? 'ya' : 'tidak',
    ]);

    // Cek nonce ada
    if (!isset($_POST['ks_nonce'])) {
        KS_Logger::error('SUBMIT', 'GAGAL: field ks_nonce tidak ada di POST');
        wp_die(
            '<h2>⚠️ Permintaan Tidak Valid</h2><p>Data form tidak lengkap. Silakan kembali dan coba lagi.</p>' .
            '<p><a href="javascript:history.back()">← Kembali</a></p>'
        );
        return;
    }

    // Verifikasi nonce
    $nonce_result = wp_verify_nonce($_POST['ks_nonce'], 'ks_form');

    if (!$nonce_result) {
        KS_Logger::error('SUBMIT', 'GAGAL: nonce tidak valid atau expired', [
            'jenis' => $jenis,
            'tip'   => 'Kemungkinan: halaman dibuka terlalu lama (>24 jam), atau plugin cache menyimpan nonce lama',
        ]);
        // Tampilkan pesan jelas — bukan redirect diam ke homepage
        wp_die(
            '<div style="font-family:Arial,sans-serif;max-width:500px;margin:60px auto;padding:30px;border:1px solid #ddd;border-radius:8px;">' .
            '<h2 style="color:#c62828;">⏰ Sesi Formulir Habis</h2>' .
            '<p>Formulir ini sudah terlalu lama dibiarkan terbuka. Silakan kembali dan isi ulang — data yang sudah diisi perlu diinput kembali.</p>' .
            '<p><a href="javascript:history.back()" style="background:#1a237e;color:#fff;padding:8px 20px;border-radius:5px;text-decoration:none;">← Kembali ke Formulir</a></p>' .
            '</div>',
            'Sesi Kadaluarsa',
            ['response' => 403]
        );
        return;
    }

    KS_Logger::info('SUBMIT', 'Nonce valid', ['jenis' => $jenis]);

    // Rate limiting: max 10 submit per IP per 5 menit
    $ip_key   = 'ks_rate_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    $attempts = (int) get_transient($ip_key);
    if ($attempts >= 10) {
        KS_Logger::warn('RATE_LIMIT', 'IP diblokir sementara', [
            'ip'       => $_SERVER['REMOTE_ADDR'] ?? '-',
            'attempts' => $attempts,
        ]);
        wp_die('Terlalu banyak permintaan. Coba lagi beberapa menit lagi.');
        return;
    }
    set_transient($ip_key, $attempts + 1, 5 * MINUTE_IN_SECONDS);

    ks_handle_form_submit();

}, 10);
