<?php
require_once KS_PATH . 'config/template-helpers.php';
$p       = th_parse_prodi($data['prodi'] ?? '');
$kaprodi = $p['dekan'] ?? ks_pejabat('rektor');

$teks = [
    'keaslian-skripsi'  => 'karya ilmiah yang saya ajukan sebagai tugas akhir/skripsi dengan judul <strong>"' . esc_html($data['judul_karya'] ?? '') . '"</strong> adalah benar-benar hasil karya saya sendiri dan bukan merupakan plagiat atau salinan dari karya orang lain.',
    'bebas-plagiat'     => 'selama menjalani studi di ' . esc_html($kampus['nama'] ?? '') . ', saya tidak pernah melakukan tindakan plagiarisme dalam bentuk apapun.',
    'tidak-pelanggaran' => 'selama menjalani studi di ' . esc_html($kampus['nama'] ?? '') . ', saya tidak pernah melakukan pelanggaran akademik atau tindakan yang bertentangan dengan norma etika akademik.',
    'sanggup-lulus'     => 'saya bersedia dan sanggup menyelesaikan studi di ' . esc_html($kampus['nama'] ?? '') . ' tepat waktu sesuai masa studi yang ditetapkan dan tunduk pada seluruh peraturan yang berlaku.',
];
$isi = $teks[$data['jenis_pernyataan']] ?? 'bertanggung jawab penuh atas pernyataan ini.';
?>
<?= ks_kop() ?>

<div class="ks-judul">Surat Pernyataan</div>
<div class="ks-nomor-tengah">Nomor: _____ / <?= esc_html($letter['nomor_kode'] ?? 'IAI-AA.S-Pern') ?> / _____ / <?= date('Y') ?></div>

<div class="ks-body">
    <p>Yang bertanda tangan di bawah ini:</p>
    <?= th_tabel_data([
        ['Nama Lengkap', $data['nama']],
        ['NIM',          $data['nim']],
        ['Program Studi',$p['prodi_label'] ?? $data['prodi']],
        ['Semester',     $data['semester']],
        ['Alamat',       $data['alamat']],
    ]) ?>
    <p>dengan ini menyatakan bahwa <?= $isi ?></p>
    <p>
        Pernyataan ini saya buat dengan sesungguhnya. Apabila di kemudian hari terbukti tidak benar,
        saya bersedia menerima sanksi sesuai ketentuan yang berlaku di <?= esc_html($kampus['nama'] ?? '') ?>.
    </p>
</div>

<!-- TTD DUA: Mahasiswa (kiri + materai) + Mengetahui Kaprodi (kanan) -->
<div class="ks-ttd-dua" style="margin-top:36px;">
    <div class="ks-ttd-item">
        <p><?= esc_html($kampus['kota'] ?? 'Jakarta') ?>, ___________________</p>
        <p>Yang Menyatakan,</p>
        <div class="ks-materai">Tempel<br>Materai<br>Rp 10.000</div>
        <p class="ks-ttd-nama"><?= esc_html($data['nama']) ?></p>
        <p class="ks-ttd-nip">NIM. <?= esc_html($data['nim']) ?></p>
    </div>
    <div class="ks-ttd-item">
        <p><?= esc_html($kampus['kota'] ?? 'Jakarta') ?>, ___________________</p>
        <p>Mengetahui,</p>
        <p><?= esc_html($kaprodi['jabatan']) ?>,</p>
        <div class="ks-ruang-ttd"></div>
        <p class="ks-ttd-nama"><?= esc_html($kaprodi['nama']) ?></p>
        <p class="ks-ttd-nip">NUPTK. <?= esc_html($kaprodi['nuptk'] ?? '') ?></p>
    </div>
</div>
<div class="ks-clear"></div>
<?= ks_footer() ?>
