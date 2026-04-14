<?php
require_once KS_PATH . 'config/template-helpers.php';
$rektor  = ks_pejabat('rektor');
$tgl_se  = !empty($data['tanggal_se']) ? date_i18n('d F Y', strtotime($data['tanggal_se'])) : '_____';
?>
<?= ks_kop() ?>

<?= ks_header_surat('Surat Rekomendasi Lolos Butuh', $letter['nomor_kode'] ?? '', '-') ?>

<?= th_kepada($data['tujuan_jabatan'], $data['tujuan_cq'] ?? '', $data['tujuan_kota']) ?>

<div class="ks-body">
    <p>Saya Yang bertanda tangan di bawah ini Pimpinan <?= esc_html($kampus['nama'] ?? '') ?>:</p>
    <?= th_tabel_data([
        ['Nama',             $rektor['nama']],
        ['NUPTK',            $rektor['nuptk'] ?? ''],
        ['Jabatan',          $rektor['jabatan']],
        ['Pangkat/Golongan', '______________________'],
        ['Unit kerja',       $kampus['nama'] ?? ''],
    ]) ?>

    <?php if (!empty($data['nomor_se'])): ?>
    <p>
        Berdasarkan surat edaran <?= esc_html($data['tujuan_jabatan']) ?>
        Nomor. <?= esc_html($data['nomor_se']) ?> tanggal <?= esc_html($tgl_se) ?>
        tentang <?= esc_html($data['perihal_se']) ?>,
        maka dengan ini menyatakan memberikan persetujuan pindah homebase eksternal
        dosen tetap <?= esc_html($kampus['nama'] ?? '') ?> ke <?= esc_html($data['dosen_tujuan']) ?>
        atas nama Saudara yang tersebut di bawah ini:
    </p>
    <?php else: ?>
    <p>
        Dengan ini menyatakan memberikan persetujuan pindah homebase eksternal
        dosen tetap <?= esc_html($kampus['nama'] ?? '') ?> ke <?= esc_html($data['dosen_tujuan']) ?>
        atas nama Saudara yang tersebut di bawah ini:
    </p>
    <?php endif; ?>

    <?= th_tabel_data([
        ['Nama',              $data['dosen_nama']],
        ['Tempat/tgl lahir',  $data['dosen_ttl']],
        ['NUPTK',             $data['dosen_nuptk']],
        ['Pangkat/Golongan',  $data['dosen_pangkat']],
        ['Pendidikan',        $data['dosen_pendidikan']],
        ['Unit kerja/Instansi',$data['dosen_unitkerja']],
    ]) ?>

    <p>
        Demikian Surat Pernyataan ini kami buat dalam keadaan sehat jasmani dan rohani
        serta tanpa adanya unsur paksaan dari pihak manapun dan Surat Pernyataan ini
        dapat dipergunakan sebagaimana mestinya.
    </p>
</div>

<div class="ks-ttd-block">
    <p><?= esc_html($kampus['kota'] ?? 'Jakarta') ?>, ___________________</p>
    <p>Rektor,</p>
    <p><?= esc_html($kampus['nama'] ?? '') ?></p>
    <div class="ks-ruang-ttd"></div>
    <p class="ks-ttd-nama"><?= esc_html($rektor['nama']) ?></p>
    <p class="ks-ttd-nip">NUPTK. <?= esc_html($rektor['nuptk'] ?? '') ?></p>
</div>
<div class="ks-clear"></div>

<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>
