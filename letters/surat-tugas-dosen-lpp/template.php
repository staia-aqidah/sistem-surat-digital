<?php
require_once KS_PATH . 'config/template-helpers.php';

// Format tanggal Indonesia
$tgl_mulai   = fmt_tanggal_indo($data['tanggal_mulai']   ?? '');
$tgl_selesai = fmt_tanggal_indo($data['tanggal_selesai'] ?? '');
$periode     = ($data['tanggal_mulai'] === $data['tanggal_selesai'])
    ? $tgl_mulai
    : $tgl_mulai . ' s.d. ' . $tgl_selesai;

// ── Tentukan penandatangan ──────────────────────────────────────
// Ambil pilihan dari form, fallback ke kepala_lpp jika tidak ada
$ttd_key  = $data['penandatangan'] ?? 'kepala_lpp';

// Whitelist key yang diizinkan — keamanan agar tidak bisa dimanipulasi
$allowed  = ['kepala_lpp', 'rektor', 'warek1', 'warek2'];
if (!in_array($ttd_key, $allowed, true)) {
    $ttd_key = 'kepala_lpp';
}

$pejabat  = ks_pejabat($ttd_key);

// Label "a.n." — tampil jika bukan Rektor atau Kepala LPP
$an_label = '';
if ($ttd_key === 'warek1' || $ttd_key === 'warek2') {
    $an_label = 'a.n. Rektor';
}

// Label jenis tugas
$label_tugas = [
    'mengajar'   => 'melaksanakan tugas mengajar / pengajaran',
    'seminar'    => 'mengikuti seminar / konferensi',
    'penelitian' => 'melaksanakan kegiatan penelitian',
    'pengabdian' => 'melaksanakan kegiatan pengabdian kepada masyarakat',
    'rapat'      => 'menghadiri rapat / pertemuan resmi',
    'pelatihan'  => 'mengikuti pelatihan / workshop',
    'lainnya'    => 'melaksanakan tugas',
][$data['jenis_tugas'] ?? 'lainnya'] ?? 'melaksanakan tugas';
?>
<?= ks_kop() ?>
<?= ks_header_surat('Surat Tugas', $letter['nomor_kode'] ?? 'IAI-AA.LPP/ST') ?>

<div class="ks-body" style="margin-top:18px;">
    <p>Yang bertanda tangan di bawah ini:</p>
    <?= th_tabel_data([
        ['Nama',      $pejabat['nama']],
        ['NUPTK',     $pejabat['nuptk'] ?? ''],
        ['Jabatan',   $pejabat['jabatan']],
        ['Unit Kerja', $kampus['nama'] ?? ''],
    ]) ?>

    <p>dengan ini menugaskan kepada:</p>
    <?= th_tabel_data([
        ['Nama Lengkap', strtoupper($data['nama_dosen'])],
        ['NUPTK/NIDN',   $data['nuptk_dosen']],
        ['Jabatan',      $data['jabatan_dosen']],
        ['Unit Kerja',   $kampus['nama'] ?? ''],
    ]) ?>

    <p>
        untuk <?= esc_html($label_tugas) ?> dalam kegiatan
        <strong><?= esc_html($data['nama_kegiatan']) ?></strong>
        yang dilaksanakan pada:
    </p>
    <?= th_tabel_data([
        ['Tempat', $data['tempat']],
        ['Waktu',  $periode],
    ]) ?>

    <?php if (!empty($data['keterangan'])): ?>
    <p><?= esc_html($data['keterangan']) ?></p>
    <?php endif; ?>

    <p>
        Kepada yang bersangkutan diminta untuk melaksanakan tugas ini
        dengan sebaik-baiknya dan menyampaikan laporan kepada pimpinan
        setelah kegiatan selesai.
    </p>
    <p>
        Demikian surat tugas ini dibuat untuk dapat dilaksanakan
        sebagaimana mestinya.
    </p>
</div>

<?= ks_ttd_custom($pejabat['jabatan'], $an_label, $pejabat['nama'], $pejabat['nuptk'] ?? '', '', $pejabat['ttd_file'] ?? '') ?>
<div class="ks-clear"></div>
<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>
