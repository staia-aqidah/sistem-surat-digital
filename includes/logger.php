<?php
/**
 * logger.php
 * Log semua aktivitas plugin IAI Surat ke file log.
 * File log: wp-content/plugins/iai-surat/logs/iai-surat.log
 *
 * Cara lihat log:
 *   - Via cPanel File Manager: buka folder iai-surat/logs/
 *   - Via WP Admin: tambahkan shortcode [iai_surat_log] di halaman admin
 *   - Via URL: /wp-admin/?ks_view_log=1&ks_log_key=KEY (key di wp-config atau kampus.php)
 */

defined('ABSPATH') || exit;

class KS_Logger {

    private static string $log_file = '';
    private static bool   $enabled  = true;

    public static function init(): void {
        $log_dir = KS_PATH . 'logs/';

        // Buat folder logs jika belum ada
        if (!is_dir($log_dir)) {
            // Coba buat via wp_mkdir_p
            $created = wp_mkdir_p($log_dir);
            if (!$created) {
                // Fallback: coba chmod dulu folder plugin
                @chmod(KS_PATH, 0755);
                @mkdir($log_dir, 0755, true);
            }
            // Proteksi direktori dari akses langsung
            if (is_dir($log_dir)) {
                file_put_contents($log_dir . '.htaccess', "Deny from all\n");
                file_put_contents($log_dir . 'index.php', "<?php // silence\n");
            }
        }

        // Cek apakah folder bisa ditulis
        if (!is_dir($log_dir) || !is_writable($log_dir)) {
            // Folder tidak bisa dibuat/ditulis — log dinonaktifkan, tidak error
            self::$enabled = false;
            return;
        }

        self::$log_file = $log_dir . 'iai-surat.log';

        // Rotasi log jika lebih dari 2MB
        if (file_exists(self::$log_file) && filesize(self::$log_file) > 2 * 1024 * 1024) {
            rename(self::$log_file, $log_dir . 'iai-surat-' . date('Y-m-d-His') . '.log');
        }
    }

    public static function write(string $level, string $context, string $message, array $data = []): void {
        if (!self::$enabled || empty(self::$log_file)) return;

        $ip      = $_SERVER['REMOTE_ADDR'] ?? '-';
        $ua      = substr($_SERVER['HTTP_USER_AGENT'] ?? '-', 0, 80);
        $ts      = date('Y-m-d H:i:s');
        $data_str = !empty($data) ? ' | DATA: ' . json_encode($data, JSON_UNESCAPED_UNICODE) : '';

        $line = "[{$ts}] [{$level}] [{$context}] {$message} | IP: {$ip} | UA: {$ua}{$data_str}" . PHP_EOL;

        file_put_contents(self::$log_file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $ctx, string $msg, array $data = []): void {
        self::write('INFO ', $ctx, $msg, $data);
    }

    public static function warn(string $ctx, string $msg, array $data = []): void {
        self::write('WARN ', $ctx, $msg, $data);
    }

    public static function error(string $ctx, string $msg, array $data = []): void {
        self::write('ERROR', $ctx, $msg, $data);
    }

    public static function get_log_path(): string {
        return self::$log_file;
    }
}

// ============================================================
// VIEWER LOG — akses via WP Admin
// URL: /wp-admin/?ks_view_log=1
// Hanya bisa diakses oleh administrator WordPress
// ============================================================
add_action('admin_init', function () {
    if (!isset($_GET['ks_view_log'])) return;
    if (!current_user_can('administrator')) {
        wp_die('Akses ditolak.');
    }

    $log_file = KS_PATH . 'logs/iai-surat.log';
    $action   = $_GET['ks_log_action'] ?? 'view';

    // Hapus log
    if ($action === 'clear') {
        if (file_exists($log_file)) {
            file_put_contents($log_file, '');
        }
        wp_redirect(admin_url('?ks_view_log=1'));
        exit;
    }

    // Download log
    if ($action === 'download') {
        if (!file_exists($log_file)) {
            wp_die('File log tidak ditemukan.');
        }
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="iai-surat-' . date('Y-m-d') . '.log"');
        readfile($log_file);
        exit;
    }

    // Tampilkan log
    $lines    = [];
    $filter   = sanitize_text_field($_GET['ks_filter'] ?? '');
    $limit    = 200; // tampilkan 200 baris terakhir

    if (file_exists($log_file)) {
        $all   = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $all   = array_reverse($all); // terbaru di atas
        foreach ($all as $line) {
            if ($filter && stripos($line, $filter) === false) continue;
            $lines[] = $line;
            if (count($lines) >= $limit) break;
        }
    }

    // Hitung statistik
    $stats = ['INFO' => 0, 'WARN' => 0, 'ERROR' => 0];
    if (file_exists($log_file)) {
        foreach (file($log_file) as $l) {
            if (str_contains($l, '[INFO ')) $stats['INFO']++;
            if (str_contains($l, '[WARN ')) $stats['WARN']++;
            if (str_contains($l, '[ERROR]')) $stats['ERROR']++;
        }
    }

    $size = file_exists($log_file) ? round(filesize($log_file) / 1024, 1) . ' KB' : '0 KB';

    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>IAI Surat — Log Viewer</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
            h1 { color: #1a237e; font-size: 22px; margin: 0 0 16px; }
            .toolbar { display: flex; gap: 10px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
            .toolbar a, .toolbar button {
                padding: 7px 16px; border-radius: 5px; font-size: 13px;
                text-decoration: none; border: none; cursor: pointer;
            }
            .btn-clear  { background: #c62828; color: #fff; }
            .btn-dl     { background: #1565c0; color: #fff; }
            .btn-back   { background: #555; color: #fff; }
            .stats { display: flex; gap: 12px; margin-bottom: 12px; }
            .stat { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: bold; }
            .stat-info  { background: #e3f2fd; color: #1565c0; }
            .stat-warn  { background: #fff8e1; color: #f57f17; }
            .stat-error { background: #ffebee; color: #c62828; }
            .stat-size  { background: #f3e5f5; color: #6a1b9a; }
            .filter-bar { display: flex; gap: 8px; margin-bottom: 12px; }
            .filter-bar input { padding: 7px 12px; border: 1px solid #ccc; border-radius: 5px; width: 280px; font-size: 13px; }
            .filter-bar button { padding: 7px 14px; background: #1a237e; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
            .log-wrap { background: #1a1a2e; border-radius: 8px; padding: 16px; overflow-x: auto; max-height: 75vh; overflow-y: auto; }
            .log-line { font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.8; white-space: pre; }
            .log-info  { color: #90caf9; }
            .log-warn  { color: #ffe082; }
            .log-error { color: #ef9a9a; }
            .empty { color: #aaa; font-style: italic; text-align: center; padding: 40px; }
        </style>
    </head>
    <body>
        <h1>📋 IAI Surat — Log Viewer</h1>

        <div class="stats">
            <span class="stat stat-info">✅ INFO: <?= $stats['INFO'] ?></span>
            <span class="stat stat-warn">⚠️ WARN: <?= $stats['WARN'] ?></span>
            <span class="stat stat-error">❌ ERROR: <?= $stats['ERROR'] ?></span>
            <span class="stat stat-size">📁 Ukuran: <?= $size ?></span>
        </div>

        <div class="toolbar">
            <a href="<?= admin_url() ?>" class="btn-back">← Kembali ke WP Admin</a>
            <a href="<?= admin_url('?ks_view_log=1&ks_log_action=download') ?>" class="btn-dl">⬇ Download Log</a>
            <a href="<?= admin_url('?ks_view_log=1&ks_log_action=clear') ?>"
               class="btn-clear"
               onclick="return confirm('Yakin hapus semua log?')">🗑 Hapus Log</a>
        </div>

        <form class="filter-bar" method="get" action="<?= admin_url() ?>">
            <input type="hidden" name="ks_view_log" value="1">
            <input type="text" name="ks_filter"
                   value="<?= esc_attr($filter) ?>"
                   placeholder="Filter: ketik ERROR, nonce, submit, IP, dll…">
            <button type="submit">🔍 Filter</button>
            <?php if ($filter): ?>
                <a href="<?= admin_url('?ks_view_log=1') ?>" style="color:#c62828;text-decoration:none;font-size:13px">✕ Reset</a>
            <?php endif; ?>
        </form>

        <div class="log-wrap">
            <?php if (empty($lines)): ?>
                <div class="empty">Belum ada log<?= $filter ? " yang cocok dengan filter \"" . esc_html($filter) . "\"" : '' ?>.</div>
            <?php else: ?>
                <?php foreach ($lines as $line):
                    $cls = 'log-info';
                    if (str_contains($line, '[WARN ')) $cls = 'log-warn';
                    if (str_contains($line, '[ERROR]')) $cls = 'log-error';
                ?>
                    <div class="log-line <?= $cls ?>"><?= esc_html($line) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <p style="color:#888;font-size:12px;margin-top:8px;">Menampilkan <?= count($lines) ?> baris terbaru. <?= $filter ? "Filter aktif: \"" . esc_html($filter) . "\"" : '' ?></p>
    </body>
    </html>
    <?php
    exit;
});

// Shortcut: tambah link "Lihat Log" di menu WP Admin
add_action('admin_menu', function () {
    add_management_page(
        'IAI Surat Log',
        '📋 IAI Surat Log',
        'administrator',
        'iai-surat-log',
        function () {
            wp_redirect(admin_url('?ks_view_log=1'));
            exit;
        }
    );
});
