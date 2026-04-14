<?php
/**
 * kop/image.php — Kop dari file gambar (kop-surat.jpg)
 * Menampilkan kop sebagai gambar full-width, persis seperti aslinya.
 * Ganti file assets/kop-surat.jpg untuk update kop.
 */

$exts = ['jpg', 'jpeg', 'png', 'webp'];
$kop_url = '';
foreach ($exts as $ext) {
    if (file_exists(KS_PATH . "assets/kop-surat.{$ext}")) {
        $kop_url = KS_URL . "assets/kop-surat.{$ext}";
        break;
    }
}
?>
<div class="ks-kop-image">
    <?php if ($kop_url): ?>
        <img src="<?= esc_url($kop_url) ?>" alt="Kop IAI Al-Aqidah Al-Hasyimiyyah">
    <?php else: ?>
        <p style="color:red; text-align:center;">⚠ File kop-surat.jpg tidak ditemukan di folder assets/</p>
    <?php endif; ?>
</div>
