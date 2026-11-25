<?php
$loadJquery  = isset($load_jquery) ? (bool) $load_jquery : true;
$loadSelect2 = isset($load_select2) ? (bool) $load_select2 : true;
$wilayahVer  = is_file(FCPATH . 'assets/js/ecc-wilayah.js') ? (string) filemtime(FCPATH . 'assets/js/ecc-wilayah.js') : '2025.1';
?>
<?php if ($loadJquery): ?>
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<?php endif; ?>
<?php if ($loadSelect2): ?>
<script src="<?= base_url('assets/vendor/select2/select2.min.js') ?>"></script>
<?php endif; ?>
<script src="<?= base_url('assets/js/ecc-wilayah.js?v=' . $wilayahVer) ?>" data-wilayah-api="<?= esc(base_url('api/wilayah/all')) ?>"></script>
