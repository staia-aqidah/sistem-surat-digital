<?php
require_once KS_PATH . 'config/template-helpers.php';
$p      = th_parse_prodi($data['prodi'] ?? '');
$kaprodi = $p['dekan'] ?? ks_pejabat('rektor');
$tgl_mulai   = !empty($data['tanggal_mulai'])   ? date_i18n('d F Y', strtotime($data['tanggal_mulai']))   : '_____';
$tgl_selesai = !empty($data['tanggal_selesai']) ? date_i18n('d F Y', strtotime($data['tanggal_selesai'])) : '_____';
?>
<?= ks_kop() ?>
<?= ks_header_surat('Surat Pengantar Praktik Kerja Lapangan', $letter['nomor_kode'] ?? '') ?>

<?= th_kepada($data['instansi'], '', $data['tujuan_tempat'] ?? 'Tempat') ?>

<div class="ks-body">
    <p>Dengan hormat,</p>
    <p>
        Yang bertanda tangan di bawah ini, <?= esc_html($kaprodi['jabatan']) ?>
        <?= esc_html($kampus['nama'] ?? '') ?>, mengajukan permohonan kepada
        Yth. Pimpinan <strong><?= esc_html($data['instansi']) ?></strong>
        di <?= nl2br(esc_html($data['alamat_instansi'])) ?>
        untuk berkenan menerima mahasiswa kami sebagai peserta <strong>Praktik Kerja Lapangan (PKL)</strong>:
    </p>
    <?= th_tabel_data([
        ['Nama Lengkap',   $data['nama']],
        ['NIM',            $data['nim']],
        ['Program Studi',  $p['prodi_label'] ?? $data['prodi']],
        ['Fakultas',       $p['fakultas_label'] ?? ''],
        ['Semester',       $data['semester']],
        ['Bidang',         $data['bidang'] ?: '-'],
        ['Periode PKL',    $tgl_mulai . ' s.d. ' . $tgl_selesai],
    ]) ?>
    <p>
        Besar harapan kami mahasiswa yang bersangkutan dapat diterima dan mendapatkan
        bimbingan selama pelaksanaan PKL. Atas perhatian dan kerja sama Bapak/Ibu,
        kami mengucapkan terima kasih.
    </p>
</div>

<?= ks_ttd_custom($kaprodi['jabatan'], '', $kaprodi['nama'], $kaprodi['nuptk'] ?? '', '', $kaprodi['ttd_file'] ?? '') ?>
<div class="ks-clear"></div>
<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>
