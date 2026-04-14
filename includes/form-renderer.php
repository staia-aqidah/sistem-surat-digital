<?php
/**
 * form-renderer.php
 *
 * Render form pengisian surat secara dinamis
 * berdasarkan fields yang didefinisikan di config.php tiap surat.
 *
 * Tipe field yang didukung:
 *   text, textarea, select, date, number, email, tel
 */

defined('ABSPATH') || exit;

/**
 * Render tampilan lengkap: daftar surat + form isi data.
 * Dipanggil oleh shortcode [form_surat].
 */
function ks_render_form(): string {
    $letters       = ks_get_all_letters();
    $selected_slug = isset($_GET['jenis']) ? sanitize_key($_GET['jenis']) : '';
    $selected_letter = $selected_slug ? ks_get_letter($selected_slug) : null;

    // Kelompokkan surat per kategori
    $grouped    = [];
    $cat_labels = [
        'mahasiswa' => '🎓 Surat Mahasiswa',
        'dosen'     => '👨‍🏫 Surat Dosen',
        'umum'      => '📄 Surat Umum',
    ];

    foreach ($letters as $slug => $letter) {
        $cat            = $letter['category'] ?? 'umum';
        $grouped[$cat][$slug] = $letter;
    }

    // Ambil URL halaman saat ini (tanpa query string)
    $current_page = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

    ob_start();
    ?>
    <div class="ks-wrapper">

        <!-- ============================================
             BAGIAN 1: PILIH JENIS SURAT
        ============================================ -->
        <div class="ks-section">
            <h3 class="ks-section-title">
                <span class="ks-step-num">1</span> Pilih Jenis Surat
            </h3>

            <?php if (empty($letters)): ?>
                <p class="ks-empty">Belum ada jenis surat yang tersedia.</p>
            <?php else: ?>
                <?php foreach ($grouped as $cat => $cat_letters): ?>
                    <div class="ks-category-group">
                        <p class="ks-cat-label">
                            <?= esc_html($cat_labels[$cat] ?? ucfirst($cat)) ?>
                        </p>
                        <div class="ks-letter-grid">
                            <?php foreach ($cat_letters as $slug => $letter): ?>
                                <a href="<?= esc_url($current_page . '?jenis=' . $slug . '#ks-form') ?>"
                                   class="ks-letter-card <?= ($selected_slug === $slug) ? 'ks-card-active' : '' ?>">
                                    <strong><?= esc_html($letter['name']) ?></strong>
                                    <?php if (!empty($letter['info'])): ?>
                                        <span class="ks-card-desc">
                                            <?= esc_html($letter['info']) ?>
                                        </span>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- ============================================
             BAGIAN 2: ISI FORM (muncul setelah pilih)
        ============================================ -->
        <?php if ($selected_letter): ?>
        <div class="ks-section" id="ks-form">
            <h3 class="ks-section-title">
                <span class="ks-step-num">2</span>
                Isi Data — <em><?= esc_html($selected_letter['name']) ?></em>
            </h3>

            <?php if (!empty($selected_letter['petunjuk'])): ?>
                <div class="ks-petunjuk">
                    ℹ️ <?= esc_html($selected_letter['petunjuk']) ?>
                </div>
            <?php endif; ?>

            <form method="POST"
                  class="ks-form"
                  action="<?= esc_url($current_page) ?>"
                  novalidate>

                <?php wp_nonce_field('ks_form', 'ks_nonce'); ?>
                <input type="hidden" name="ks_submit" value="1">
                <input type="hidden" name="ks_jenis"  value="<?= esc_attr($selected_slug) ?>">

                <?php foreach ($selected_letter['fields'] as $field):
                    $field_name  = sanitize_key($field['name']);
                    $field_id    = 'ks_' . $field_name;
                    $is_required = $field['required'] ?? false;
                    $placeholder = $field['placeholder'] ?? '';
                    $input_name  = 'ks_fields[' . $field_name . ']';
                ?>
                    <div class="ks-field">
                        <label for="<?= esc_attr($field_id) ?>">
                            <?= esc_html($field['label']) ?>
                            <?php if ($is_required): ?>
                                <span class="ks-required" title="Wajib diisi">*</span>
                            <?php endif; ?>
                        </label>

                        <?php if ($field['type'] === 'textarea'): ?>
                            <textarea
                                id="<?= esc_attr($field_id) ?>"
                                name="<?= esc_attr($input_name) ?>"
                                rows="<?= intval($field['rows'] ?? 3) ?>"
                                placeholder="<?= esc_attr($placeholder) ?>"
                                <?= $is_required ? 'required' : '' ?>
                            ></textarea>

                        <?php elseif ($field['type'] === 'select'): ?>
                            <select
                                id="<?= esc_attr($field_id) ?>"
                                name="<?= esc_attr($input_name) ?>"
                                <?= $is_required ? 'required' : '' ?>
                            >
                                <option value="">-- Pilih <?= esc_html($field['label']) ?> --</option>
                                <?php foreach ($field['options'] as $opt_value => $opt_label):
                                    // Support array asosiatif ['value' => 'label'] atau array biasa
                                    if (is_int($opt_value)) {
                                        $opt_value = $opt_label;
                                    }
                                ?>
                                    <option value="<?= esc_attr($opt_value) ?>">
                                        <?= esc_html($opt_label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        <?php else: // text, number, date, email, tel ?>
                            <input
                                type="<?= esc_attr($field['type']) ?>"
                                id="<?= esc_attr($field_id) ?>"
                                name="<?= esc_attr($input_name) ?>"
                                placeholder="<?= esc_attr($placeholder) ?>"
                                <?= $is_required ? 'required' : '' ?>
                            >
                        <?php endif; ?>

                        <?php if (!empty($field['help'])): ?>
                            <span class="ks-help-text">
                                <?= esc_html($field['help']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="ks-form-footer">
                    <p class="ks-disclaimer">
                        ⚠️ Pastikan data yang diisi sudah benar sebelum membuat surat.
                        Setelah dicetak, bawa ke Tata Usaha untuk tanda tangan dan cap.
                    </p>
                    <div id="ks-nonce-status" style="display:none;font-size:12px;color:#888;margin-bottom:6px;text-align:center;">
                        🔄 Memperbarui sesi formulir...
                    </div>
                    <button type="submit" class="ks-btn-submit" id="ks-btn-submit">
                        📄 Buat Surat &amp; Cetak
                    </button>
                </div>

            </form>
        </div>
        <?php endif; ?>

    </div><!-- .ks-wrapper -->

    <?php if ($selected_letter): ?>
    <script>
    (function() {
        /*
         * Refresh nonce via AJAX sesaat sebelum submit.
         * Mencegah error "kembali ke halaman utama" akibat nonce expired
         * pada halaman yang sudah lama dibuka atau dari cache.
         */
        var form   = document.querySelector('.ks-form');
        var btn    = document.getElementById('ks-btn-submit');
        var status = document.getElementById('ks-nonce-status');

        if (!form || !btn) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var nonceField = form.querySelector('input[name="ks_nonce"]');
            if (!nonceField) {
                form.submit();
                return;
            }

            // Disable tombol & tampilkan status
            btn.disabled    = true;
            btn.textContent = '⏳ Memproses...';
            if (status) status.style.display = 'block';

            // Request nonce baru
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?= esc_url(admin_url('admin-ajax.php')) ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.timeout = 8000;

            function doSubmit() {
                if (status) status.style.display = 'none';
                form.submit();
            }

            function resetBtn() {
                btn.disabled    = false;
                btn.textContent = '📄 Buat Surat & Cetak';
                if (status) status.style.display = 'none';
            }

            xhr.onload = function() {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.success && res.data && res.data.nonce) {
                        nonceField.value = res.data.nonce; // update nonce
                        doSubmit();
                        return;
                    }
                } catch(err) {}
                // Gagal parse — submit dengan nonce lama
                doSubmit();
            };

            xhr.ontimeout = xhr.onerror = function() {
                // Jaringan lambat/error — submit dengan nonce lama
                doSubmit();
            };

            xhr.send('action=ks_refresh_nonce');
        });
    })();
    </script>
    <?php endif; ?>

    <?php
    return ob_get_clean();
}
