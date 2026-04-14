<?php
require_once KS_PATH . 'config/template-helpers.php';
$p       = th_parse_prodi($data['prodi'] ?? '');
$kaprodi = $p['dekan'] ?? ks_pejabat('rektor');
$tgl_mulai   = !empty($data['tanggal_mulai'])   ? date_i18n('d F Y', strtotime($data['tanggal_mulai']))   : '_____';
$tgl_selesai = !empty($data['tanggal_selesai']) ? date_i18n('d F Y', strtotime($data['tanggal_selesai'])) : '_____';
$periode = ($data['tanggal_mulai'] === $data['tanggal_selesai']) ? $tgl_mulai : "$tgl_mulai s.d. $tgl_selesai";
$kepada_teks = !empty($data['ditujukan']) ? 'Yth. ' . $data['ditujukan'] : 'Yth. Seluruh Dosen Pengampu';
?>
<?= ks_kop() ?>
<?= ks_header_surat('Surat Dispensasi', $letter['nomor_kode'] ?? '') ?>

<?= th_kepada($kepada_teks, '', 'Tempat') ?>

<div class="ks-body">
    <p>Dengan hormat,</p>
    <p>
        Yang bertanda tangan di bawah ini, <?= esc_html($kaprodi['jabatan']) ?>
        <?= esc_html($kampus['nama'] ?? '') ?>, memberikan dispensasi kepada mahasiswa:
    </p>
    <?= th_tabel_data([
        ['Nama Lengkap', $data['nama']],
        ['NIM',          $data['nim']],
        ['Program Studi',$p['prodi_label'] ?? $data['prodi']],
        ['Semester',     $data['semester']],
    ]) ?>
    <p>
        Mahasiswa tersebut tidak dapat mengikuti perkuliahan pada <strong><?= esc_html($periode) ?></strong>
        dikarenakan mengikuti <strong><?= esc_html($data['nama_kegiatan']) ?></strong>
        yang diselenggarakan oleh <?= esc_html($data['penyelenggara']) ?>.
    </p>
    <p>
        Sehubungan dengan hal tersebut, kami mohon Bapak/Ibu Dosen dapat memberikan dispensasi kehadiran
        kepada mahasiswa yang bersangkutan. Mahasiswa diwajibkan mengganti tugas atau kehadiran sesuai
        kesepakatan dengan dosen pengampu.
    </p>
    <p>Atas perhatian dan kerja sama Bapak/Ibu, kami mengucapkan terima kasih.</p>
</div>

<?= ks_ttd_custom($kaprodi['jabatan'], '', $kaprodi['nama'], $kaprodi['nuptk'] ?? '', '', $kaprodi['ttd_file'] ?? '') ?>
<div class="ks-clear"></div>
<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>
