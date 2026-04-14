<?php
require_once KS_PATH . 'config/template-helpers.php';
$p = th_parse_prodi($data['prodi'] ?? '');
$kaprodi = $p['dekan'] ?? ks_pejabat('rektor');
?>
<?= ks_kop() ?>
<?= ks_header_surat('Surat Keterangan', $letter['nomor_kode'] ?? '') ?>

<div class="ks-body" style="margin-top:18px;">
    <p><strong><em>Assalamu’alaikum Wr. Wb.</em></strong></p>
    <p>Saya yang bertanda tangan di bawah ini:</p>
    <?= th_tabel_data([
        ['Nama',    $kaprodi['nama']],
        ['NUPTK',   $kaprodi['nuptk'] ?? ''],
        ['Jabatan', $kaprodi['jabatan']],
        ['Unit Kerja', ($kampus['nama'] ?? '')],
    ]) ?>
    <p>dengan ini menerangkan bahwa mahasiswa berikut:</p>
    <?= th_tabel_data([
        ['Nama Lengkap', strtoupper($data['nama'])],
        ['NIM',            $data['nim']],
        ['Program Studi',  $p['prodi_label'] ?? $data['prodi']],
        ['Fakultas',       $p['fakultas_label'] ?? ''],
        ['Jenjang',        $p['jenjang'] ?? ''],
        ['Semester',       $data['semester']],
        ['Tahun Akademik', $data['tahun_akademik']],
    ]) ?>
    <p>
        Adalah benar sedang tidak menerima beasiswa lain dari pihak manapun
        pada Tahun Akademik <?= esc_html($data['tahun_akademik']) ?>.
    </p>
    <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    <p><strong><em>Wassalamu’alaikum Wr. Wb.</em></strong></p>
</div>

<?= ks_ttd_custom($kaprodi['jabatan'], '', $kaprodi['nama'], $kaprodi['nuptk'] ?? '', '', $kaprodi['ttd_file'] ?? '') ?>
<div class="ks-clear"></div>
<?= ks_footer() ?>
