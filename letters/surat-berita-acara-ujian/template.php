<?php
require_once KS_PATH . 'config/template-helpers.php';
$p        = th_parse_prodi($data['prodi'] ?? '');
$tgl_ujian = !empty($data['tanggal_ujian']) ? date_i18n('d F Y', strtotime($data['tanggal_ujian'])) : '_____';
?>
<?= ks_kop() ?>

<div class="ks-judul">Berita Acara</div>
<div class="ks-nomor-tengah" style="margin-bottom:4px;">
    Ujian Sidang <?= (($p['jenjang'] ?? 'S1') === 'S2') ? 'Tesis' : 'Skripsi' ?>
</div>
<div class="ks-nomor-tengah">
    Nomor: _____ / <?= esc_html($letter['nomor_kode'] ?? 'IAI-AA.BA-Ujian') ?> / _____ / <?= date('Y') ?>
</div>

<table class="ks-tabel-formal" style="margin:18px 0 14px;">
    <tr><th colspan="3" style="background:#e8f5e9;">I. DATA PELAKSANAAN UJIAN</th></tr>
    <tr><td style="width:180px;padding-left:10px;">Hari / Tanggal</td><td style="width:12px;">:</td><td><?= esc_html($tgl_ujian) ?></td></tr>
    <tr><td style="padding-left:10px;">Waktu</td><td>:</td><td><?= esc_html($data['waktu_mulai']) ?> — <?= esc_html($data['waktu_selesai']) ?></td></tr>
    <tr><td style="padding-left:10px;">Tempat</td><td>:</td><td><?= esc_html($data['ruang']) ?></td></tr>
    <tr><th colspan="3" style="background:#e8f5e9;">II. DATA MAHASISWA</th></tr>
    <tr><td style="padding-left:10px;">Nama Lengkap</td><td>:</td><td><strong><?= esc_html($data['nama']) ?></strong></td></tr>
    <tr><td style="padding-left:10px;">NIM</td><td>:</td><td><?= esc_html($data['nim']) ?></td></tr>
    <tr><td style="padding-left:10px;">Program Studi</td><td>:</td><td><?= esc_html($p['prodi_label'] ?? $data['prodi']) ?></td></tr>
    <tr><td style="padding-left:10px;vertical-align:top;">Judul</td><td style="vertical-align:top;">:</td><td><em>"<?= esc_html($data['judul']) ?>"</em></td></tr>
</table>

<table class="ks-tabel-formal" style="margin-bottom:14px;">
    <tr><th colspan="5" style="background:#e8f5e9;">III. TIM PENGUJI DAN PENILAIAN</th></tr>
    <tr>
        <th style="width:35px;">No.</th>
        <th>Nama Penguji</th>
        <th style="width:110px;">Jabatan</th>
        <th style="width:90px;">Nilai (0–100)</th>
        <th style="width:110px;">Tanda Tangan</th>
    </tr>
    <tr><td class="center">1</td><td><?= esc_html($data['penguji1']) ?></td><td class="center">Ketua Penguji</td><td></td><td></td></tr>
    <tr><td class="center">2</td><td><?= esc_html($data['penguji2']) ?></td><td class="center">Anggota Penguji</td><td></td><td></td></tr>
    <tr><td class="center">3</td><td><?= esc_html($data['pembimbing']) ?></td><td class="center">Pembimbing</td><td></td><td></td></tr>
    <tr><th colspan="3" style="text-align:right;padding-right:8px;">Nilai Rata-Rata :</th><th class="center"></th><th></th></tr>
    <tr><th colspan="3" style="text-align:right;padding-right:8px;">Nilai Huruf :</th><th class="center"></th><th></th></tr>
    <tr><th colspan="3" style="text-align:right;padding-right:8px;">Hasil Ujian :</th><th colspan="2" class="center">☐ LULUS &nbsp;&nbsp; ☐ TIDAK LULUS</th></tr>
</table>

<table class="ks-tabel-formal" style="margin-bottom:20px;">
    <tr><th colspan="2" style="background:#e8f5e9;">IV. CATATAN DAN REVISI</th></tr>
    <tr><td colspan="2" style="height:60px;vertical-align:top;padding:8px;"></td></tr>
    <tr><th>Batas Waktu Revisi</th><th>Tanggal Selesai Revisi</th></tr>
    <tr><td style="height:30px;"></td><td></td></tr>
</table>

<p style="font-size:11pt;font-weight:bold;margin-bottom:8px;">Demikian berita acara ini dibuat dengan sebenarnya.</p>

<div style="display:flex;justify-content:space-between;margin-top:10px;font-size:10.5pt;font-family:'Times New Roman',serif;">
    <div style="width:30%;text-align:center;">
        <p style="margin:2px 0;">Jakarta, ___________________</p>
        <p style="margin:2px 0;">Mahasiswa,</p>
        <div class="ks-ruang-ttd"></div>
        <p style="margin:2px 0;font-weight:bold;text-decoration:underline;"><?= esc_html($data['nama']) ?></p>
        <p style="margin:2px 0;font-size:10pt;">NIM. <?= esc_html($data['nim']) ?></p>
    </div>
    <div style="width:30%;text-align:center;">
        <p style="margin:2px 0;">&nbsp;</p>
        <p style="margin:2px 0;">Ketua Penguji,</p>
        <div class="ks-ruang-ttd"></div>
        <p style="margin:2px 0;font-weight:bold;text-decoration:underline;"><?= esc_html($data['penguji1']) ?></p>
        <p style="margin:2px 0;font-size:10pt;">NUPTK. __________________</p>
    </div>
    <div style="width:30%;text-align:center;">
        <p style="margin:2px 0;">&nbsp;</p>
        <p style="margin:2px 0;">Anggota Penguji,</p>
        <div class="ks-ruang-ttd"></div>
        <p style="margin:2px 0;font-weight:bold;text-decoration:underline;"><?= esc_html($data['penguji2']) ?></p>
        <p style="margin:2px 0;font-size:10pt;">NUPTK. __________________</p>
    </div>
</div>
<div class="ks-clear"></div>
<?= ks_footer() ?>
