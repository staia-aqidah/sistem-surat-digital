<?php
require_once KS_PATH . 'config/template-helpers.php';
$p = th_parse_prodi($data['prodi'] ?? '');
?>
<?= ks_kop() ?>
<?= ks_header_surat('Surat Keterangan Bebas Perpustakaan', $letter['nomor_kode'] ?? '') ?>

<div class="ks-body" style="margin-top:18px;">
    <p>Yang bertanda tangan di bawah ini Bagian Perpustakaan Institut Agama Islam Al-Aqidah Al-Hasyimiyyah Jakarta menyatakan bahwa :</p>
    <?= th_tabel_data([
        ['Nama Lengkap', strtoupper($data['nama'])],
        ['NIM',            $data['nim']],
        ['Program Studi',  $p['prodi_label'] ?? $data['prodi']],
        ['Fakultas',       $p['fakultas_label'] ?? ''],
        ['Jenjang',        $p['jenjang'] ?? ''],
    ]) ?>
    <p>
        Mahasiswa tersebut bebas dari tanggungan buku perpustakaan dan sudah melengkapi syarat bebas perpustakaan. Surat Pernyataan ini dibuat sebagai syarat untuk mengajukan proses yudisium.
    </p>
    <p>Demikian Surat Pernyataan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya atas perhatian dan kerjasama kami ucapkan terima kasih.</p>
</div>

<?= ks_ttd('kepala_perpustakaan') ?>
<div class="ks-clear"></div>
<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>