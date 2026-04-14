<?php
/**
 * shortcode.php
 *
 * Mendaftarkan shortcode [form_surat] ke WordPress.
 * Shortcode ini ditempel di halaman Elementor via widget "Shortcode".
 *
 * Penggunaan: [form_surat]
 */

defined('ABSPATH') || exit;

add_shortcode('form_surat', function ($atts) {
    // Pastikan fungsi render tersedia
    if (!function_exists('ks_render_form')) return '';
    return ks_render_form();
});
