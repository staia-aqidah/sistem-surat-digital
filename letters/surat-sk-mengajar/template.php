<?php
require_once KS_PATH . 'config/template-helpers.php';
// Parse mata kuliah (format: "Nama MK | SKS | Keterangan")
$matakuliah_rows = [];
$no = 1;
foreach (array_filter(array_map('trim', explode("\n", $data['mata_kuliah'] ?? ''))) as $baris) {
    $parts = array_map('trim', explode('|', $baris));
    $matakuliah_rows[] = [$no++, $parts[0] ?? '', $parts[1] ?? '', $parts[2] ?? ''];
}
?>
<?= ks_kop() ?>

<div class="ks-judul">Surat Keputusan</div>
<div class="ks-nomor-tengah">
    Nomor: _____ / <?= esc_html($letter['nomor_kode'] ?? 'IAI-AA.SK-MK') ?> / _____ / <?= date('Y') ?>
</div>

<div class="ks-body">
    <p>Yang bertanda tangan di bawah ini, <?= esc_html(ks_pejabat('rektor')['jabatan']) ?>
    <?= esc_html($kampus['nama'] ?? '') ?>, dengan ini menetapkan Surat Keputusan penugasan mengajar kepada:</p>

    <?= th_tabel_data([
        ['Nama Lengkap', $data['nama_dosen']],
        ['NUPTK/NIDN',   $data['nuptk_dosen']],
        ['Jabatan',      $data['jabatan_dosen']],
        ['Unit Kerja',   $kampus['nama'] ?? ''],
    ]) ?>

    <p>untuk melaksanakan tugas mengajar pada <strong>Semester <?= esc_html($data['semester_mengajar']) ?>
    Tahun Akademik <?= esc_html($data['tahun_akademik']) ?></strong> dengan rincian sebagai berikut:</p>
</div>

<table class="ks-tabel-formal">
    <tr>
        <th class="no-col" style="width:40px">No.</th>
        <th>Mata Kuliah</th>
        <th style="width:60px">SKS</th>
        <th style="width:200px">Prodi / Semester</th>
    </tr>
    <?php foreach ($matakuliah_rows as $r): ?>
    <tr>
        <td class="center"><?= $r[0] ?></td>
        <td><?= esc_html($r[1]) ?></td>
        <td class="center"><?= esc_html($r[2]) ?></td>
        <td><?= esc_html($r[3]) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="2" style="text-align:right; padding-right:10px;"><strong>Total SKS</strong></td>
        <td class="center"><strong>
            <?php
            $total = 0;
            foreach ($matakuliah_rows as $r) { $total += (int)filter_var($r[2], FILTER_SANITIZE_NUMBER_INT); }
            echo $total;
            ?>
        </strong></td>
        <td></td>
    </tr>
</table>

<div class="ks-body" style="margin-top:14px;">
    <p>Surat Keputusan ini berlaku selama satu semester dan yang bersangkutan wajib melaksanakan
    tugas mengajar dengan penuh tanggung jawab sesuai ketentuan yang berlaku.</p>
    <p>Demikian Surat Keputusan ini dibuat untuk dilaksanakan sebagaimana mestinya.</p>
</div>

<?= ks_ttd('rektor') ?>
<div class="ks-clear"></div>
<?= !empty($data['tembusan']) ? th_tembusan($data['tembusan']) : '' ?>
<?= ks_footer() ?>
