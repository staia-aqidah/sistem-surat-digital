<?php
/**
 * config/template-helpers.php
 * ----------------------------
 * Fungsi-fungsi kecil untuk render bagian HTML surat yang berulang.
 * Di-include di awal setiap template.php yang membutuhkan.
 *
 * Cara pakai: require_once KS_PATH . 'config/template-helpers.php';
 */

/**
 * Render tabel data (Nama : Nilai) tanpa border.
 * @param array $rows  [ ['label', 'value'], ... ]
 */
function th_tabel_data(array $rows): string {
    $html = '<table class="ks-tabel-data">';
    foreach ($rows as $row) {
        $html .= '<tr>'
            . '<td>' . esc_html($row[0]) . '</td>'
            . '<td>:</td>'
            . '<td>' . (($row[2] ?? false) ? $row[1] : esc_html($row[1])) . '</td>'
            . '</tr>';
    }
    return $html . '</table>';
}

/**
 * Render blok "Kepada Yth."
 */
function th_kepada(string $jabatan, string $cq = '', string $kota = 'Tempat'): string {
    $html  = '<div class="ks-kepada">';
    $html .= '<p>Kepada Yth,</p>';
    $html .= '<p>' . esc_html($jabatan) . '</p>';
    if ($cq) $html .= '<p>Cq. ' . esc_html($cq) . '</p>';
    $html .= '<p>Di-</p>';
    $html .= '<p style="padding-left:24px">' . esc_html($kota) . '</p>';
    $html .= '</div>';
    return $html;
}

/**
 * Render blok tembusan dari string multi-baris.
 */
function th_tembusan(string $raw): string {
    $items = array_filter(array_map('trim', explode("\n", $raw)));
    if (empty($items)) return '';
    $html  = '<div class="ks-tembusan"><p>Tembusan disampaikan kepada:</p><ol>';
    foreach ($items as $item) {
        $html .= '<li>' . esc_html($item) . '</li>';
    }
    return $html . '</ol></div>';
}

/**
 * Ambil data prodi dari value select (format "S1|PAI").
 * Kembalikan array: ['jenjang', 'kode', 'label', 'fakultas_label', 'kaprodi', 'dekan']
 */
function th_parse_prodi(string $value): array {
    [$jenjang, $kode] = explode('|', $value . '|');
    return ks_get_prodi_data($jenjang, $kode) + ['jenjang' => $jenjang, 'kode' => $kode];
}


function fmt_tanggal_indo(string $ymd, bool $dengan_hari = false): string {
    if (!$ymd) return '_____';
    $ts  = strtotime($ymd);
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bln  = ['','Januari','Februari','Maret','April','Mei','Juni',
             'Juli','Agustus','September','Oktober','November','Desember'];
    $hasil = date('d', $ts) . ' ' . $bln[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    if ($dengan_hari) {
        $hasil = $hari[date('w', $ts)] . ', ' . $hasil;
    }
    return $hasil;
}