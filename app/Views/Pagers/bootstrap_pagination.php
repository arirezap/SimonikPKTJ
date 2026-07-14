<?php 
$pager->setSurroundCount(2);

// Ambil semua parameter GET saat ini, kecuali parameter halaman (misalnya page_default)
$queryParams = $_GET;
unset($queryParams['page'], $queryParams['page_default'], $queryParams['page_users']);
$queryString = !empty($queryParams) ? '&' . http_build_query($queryParams) : '';

// Helper untuk menambahkan query string ke URL pagination
$buildLink = function($uri) use ($queryString) {
    if (empty($queryString)) return $uri;
    return (strpos($uri, '?') !== false) ? $uri . $queryString : $uri . '?' . ltrim($queryString, '&');
};

$currentPage = $pager->getCurrentPageNumber();
$pageCount = $pager->getPageCount();
$perPage = $_GET['per_page'] ?? 10;
?>

<div class="custom-pagination mt-2 d-flex justify-content-end">
    <div class="d-flex align-items-center bg-white shadow-sm rounded-pill px-3 py-2 border">
        
        <!-- Prev -->
        <?php if ($pager->hasPrevious()) : ?>
            <a href="<?= $buildLink($pager->getPrevious()) ?>" class="page-btn rounded-circle me-1"><i class="bi bi-chevron-left"></i></a>
        <?php else: ?>
            <a href="#" class="page-btn rounded-circle me-1 disabled"><i class="bi bi-chevron-left"></i></a>
        <?php endif; ?>
        
        <!-- Page Numbers -->
        <div class="d-flex align-items-center mx-2 gap-1">
            <?php foreach ($pager->links() as $link) : ?>
                <a href="<?= $buildLink($link['uri']) ?>" class="page-btn rounded-circle <?= $link['active'] ? 'active' : '' ?>">
                    <?= $link['title'] ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Next -->
        <?php if ($pager->hasNext()) : ?>
            <a href="<?= $buildLink($pager->getNext()) ?>" class="page-btn rounded-circle ms-1"><i class="bi bi-chevron-right"></i></a>
        <?php else: ?>
            <a href="#" class="page-btn rounded-circle ms-1 disabled"><i class="bi bi-chevron-right"></i></a>
        <?php endif; ?>

        <div class="vr mx-3"></div>

        <!-- Items Per Page -->
        <select class="form-select form-select-sm rounded-pill me-3 border-0 bg-light text-muted" style="width: 105px; cursor: pointer; font-size: 0.85rem;" onchange="changePerPage(this.value)">
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10 / page</option>
            <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25 / page</option>
            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 / page</option>
            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 / page</option>
        </select>

        <!-- Go To Page -->
        <div class="d-flex align-items-center">
            <span class="me-2 text-muted small" style="font-size: 0.85rem;">Go to</span>
            <input type="number" class="form-control form-control-sm rounded-pill text-center me-2 border-primary" style="width: 60px; font-size: 0.85rem;" min="1" max="<?= $pageCount ?>" value="<?= $currentPage ?>" onkeydown="goToPage(this, event)">
            <span class="text-muted small" style="font-size: 0.85rem;">Page</span>
        </div>
    </div>
</div>

<script>
function changePerPage(val) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('per_page', val);
    // Hapus parameter page agar kembali ke halaman 1
    for(const key of urlParams.keys()) {
        if (key.startsWith('page')) {
            urlParams.delete(key);
        }
    }
    window.location.search = urlParams.toString();
}

function goToPage(input, event) {
    if (event.key === 'Enter') {
        let page = parseInt(input.value);
        let max = parseInt(input.getAttribute('max'));
        if (page > 0 && page <= max) {
            const urlParams = new URLSearchParams(window.location.search);
            // Hapus semua param page* lama agar bersih
            for(const key of urlParams.keys()) {
                if (key.startsWith('page')) {
                    urlParams.delete(key);
                }
            }
            // Set parameter page yang baru
            urlParams.set('page', page);
            window.location.search = urlParams.toString();
        } else {
            input.value = <?= $currentPage ?>;
        }
        event.preventDefault();
    }
}
</script>
