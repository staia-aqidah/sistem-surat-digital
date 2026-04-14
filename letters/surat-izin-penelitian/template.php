<?php
require_once KS_PATH . 'config/template-helpers.php';
$p       = th_parse_prodi($data['prodi'] ?? '');
$kaprodi = $p['dekan'] ?? ks_pejabat('rektor');
$tgl_mulai   = !empty($data['tanggal_mulai'])   ? date_i18n('d F Y', strtotime($data['tanggal_mulai']))   : '_____';
$tgl_selesai = !empty($data['tanggal_selesai']) ? date_i18n('d F Y', strtotime($data['tanggal_selesai'])) : '_____';
?>
<?= ks_kop() ?>
<?= ks_header_surat('Surat Izin Penelitian', $letter['nomor_kode'] ?? '') ?>

<?= th_kepada('Pimpinan ' . $data['instansi'], '', 'Tempat') ?>

<div class="ks-body">
    <p>Dengan hormat,</p>
    <p>
        Yang bertanda tangan di bawah ini, <?= esc_html($kaprodi['jabatan']) ?>
        <?= esc_html($kampus['nama'] ?? '') ?>, menerangkan bahwa mahasiswa berikut:
    </p>
    <?= th_tabel_data([
        ['Nama Lengkap', strtoupper($data['nama'])],
        ['NIM',              $data['nim']],
        ['Program Studi',    $p['prodi_label'] ?? $data['prodi']],
        ['Fakultas',         $p['fakultas_label'] ?? ''],
        ['Dosen Pembimbing', $data['nama_pembimbing']],
    ]) ?>
    <p>sedang melakukan penelitian dengan judul:</p>
    <p class="ks-center ks-italic ks-bold">"<?= esc_html($data['judul_penelitian']) ?>"</p>
    <p>
        Sehubungan hal tersebut, kami mohon izin kepada Bapak/Ibu untuk memberikan
        akses penelitian di <?= esc_html($data['instansi']) ?>,
        <?= nl2br(esc_html($data['alamat_instansi'])) ?>,
        pada periode <?= esc_html($tgl_mulai) ?> s.d. <?= esc_html($tgl_selesai) ?>.
    </p>
    <p>Atas izin dan bantuan Bapak/Ibu, kami mengucapkan terima kasih.</p>
</div>

<?= ks_ttd_custom($kaprodi['jabatan'], '', $kaprodi['nama'], $kaprodi['nuptk'] ?? '', '', $kaprodi['ttd_file'] ?? '') ?>
<div class="ks-clear"></div>
<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>
