<?php
require_once KS_PATH . 'config/template-helpers.php';
$p       = th_parse_prodi($data['prodi'] ?? '');
$dekan   = $p['dekan'] ?? ks_pejabat('rektor');
?>
<?= ks_kop() ?>
<?= ks_header_surat('Surat Rekomendasi Beasiswa', $letter['nomor_kode'] ?? '') ?>

<div class="ks-body" style="margin-top:18px;">
    <p>
        Sehubungan dengan program <strong><?= esc_html($data['nama_beasiswa']) ?></strong>
        yang diselenggarakan oleh <?= esc_html($data['penyelenggara_beasiswa']) ?>,
        yang bertanda tangan di bawah ini:
    </p>
    <?= th_tabel_data([
        ['Nama',      $dekan['nama']],
        ['NUPTK',     $dekan['nuptk'] ?? ''],
        ['Jabatan',   $dekan['jabatan']],
        ['Unit Kerja', $kampus['nama'] ?? ''],
    ]) ?>
    <p>dengan ini memberikan rekomendasi kepada mahasiswa berikut:</p>
    <?= th_tabel_data([
        ['Nama Lengkap',   $data['nama']],
        ['NIM',            $data['nim']],
        ['Program Studi',  $p['prodi_label'] ?? $data['prodi']],
        ['Fakultas',       $p['fakultas_label'] ?? ''],
        ['Semester',       $data['semester']],
        ['Tahun Akademik', $data['tahun_akademik']],
        ['IPK',            $data['ipk'] . ' / 4,00'],
    ]) ?>
    <p>
        Berdasarkan catatan akademik kami, mahasiswa yang bersangkutan menunjukkan
        prestasi akademik yang baik dengan IPK <strong><?= esc_html($data['ipk']) ?></strong> dari skala 4,00.
        <?php if (!empty($data['prestasi'])): ?>
            Selain itu, mahasiswa tersebut juga memiliki rekam jejak yang membanggakan,
            antara lain: <?= esc_html($data['prestasi']) ?>.
        <?php endif; ?>
    </p>
    <p>
        Atas dasar tersebut, kami sangat merekomendasikan mahasiswa yang bersangkutan
        untuk dapat diterima sebagai penerima <strong><?= esc_html($data['nama_beasiswa']) ?></strong>.
        Kami meyakini mahasiswa ini layak mendapatkan dukungan guna memaksimalkan
        potensi akademik dan kontribusinya.
    </p>
    <p>Demikian surat rekomendasi ini kami buat. Atas perhatian Bapak/Ibu, kami mengucapkan terima kasih.</p>
</div>

<?= ks_ttd_custom($dekan['jabatan'], '', $dekan['nama'], $dekan['nuptk'] ?? '', '', $dekan['ttd_file'] ?? '') ?>
<div class="ks-clear"></div>
<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>
