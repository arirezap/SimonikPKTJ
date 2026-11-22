<script>
(function () {
    'use strict';

    // 1. Inisialisasi Flatpickr Bahasa Indonesia pada input tanggal
    function initLowFlatpickr() {
        if (typeof flatpickr === 'undefined') {
            return;
        }
        document.querySelectorAll('input.js-low-date, input[type="date"].ecc-control-sm').forEach(function (el) {
            if (el.dataset.fpInitialized === '1') {
                return;
            }
            el.dataset.fpInitialized = '1';
            flatpickr(el, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd M Y',
                allowInput: true,
                locale: (flatpickr.l10ns && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default'
            });
        });
    }

    // 2. Inisialisasi Select2 pada dropdown panjang
    function initLowSelect2() {
        if (typeof window.jQuery === 'undefined' || typeof window.jQuery.fn.select2 === 'undefined') {
            return;
        }
        window.jQuery('select.js-low-select2').each(function () {
            var $el = window.jQuery(this);
            if ($el.data('select2')) {
                return;
            }
            $el.select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    }

    // 3. Sorting tabel client-side dengan atribut aria-sort
    function initLowSortableTables() {
        document.querySelectorAll('table[data-sortable-table="true"]').forEach(function (table) {
            var headers = table.querySelectorAll('thead th');
            headers.forEach(function (th) {
                var btn = th.querySelector('.low-sort-btn');
                if (!btn) {
                    return;
                }
                btn.addEventListener('click', function () {
                    var colIdx = parseInt(btn.getAttribute('data-sort-col') || '0', 10);
                    var currentSort = th.getAttribute('aria-sort') || 'none';
                    var nextSort = currentSort === 'ascending' ? 'descending' : 'ascending';

                    headers.forEach(function (h) {
                        if (h.hasAttribute('aria-sort')) {
                            h.setAttribute('aria-sort', 'none');
                            var icon = h.querySelector('.sort-indicator');
                            if (icon) {
                                icon.className = 'bi bi-arrow-down-up text-muted opacity-50 sort-indicator';
                            }
                        }
                    });

                    th.setAttribute('aria-sort', nextSort);
                    var activeIcon = th.querySelector('.sort-indicator');
                    if (activeIcon) {
                        activeIcon.className = nextSort === 'ascending'
                            ? 'bi bi-sort-up text-primary sort-indicator'
                            : 'bi bi-sort-down text-primary sort-indicator';
                    }

                    var tbody = table.querySelector('tbody');
                    if (!tbody) {
                        return;
                    }
                    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                    if (rows.length <= 1 && rows[0] && rows[0].querySelector('td[colspan]')) {
                        return;
                    }

                    rows.sort(function (a, b) {
                        var cellA = a.children[colIdx];
                        var cellB = b.children[colIdx];
                        var valA = (cellA ? (cellA.getAttribute('data-sort-val') || cellA.textContent || '') : '').trim();
                        var valB = (cellB ? (cellB.getAttribute('data-sort-val') || cellB.textContent || '') : '').trim();

                        var numA = parseFloat(valA);
                        var numB = parseFloat(valB);
                        var cmp = 0;
                        if (!isNaN(numA) && !isNaN(numB) && String(numA) === valA && String(numB) === valB) {
                            cmp = numA - numB;
                        } else {
                            cmp = valA.localeCompare(valB, 'id', { numeric: true, sensitivity: 'base' });
                        }
                        return nextSort === 'ascending' ? cmp : -cmp;
                    });

                    rows.forEach(function (r) {
                        tbody.appendChild(r);
                    });
                });
            });
        });
    }

    // 4. Baris tabel & kartu klik (dengan dukungan Enter/Space)
    function initLowClickableRows() {
        document.querySelectorAll('.low-row-clickable[data-href]').forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.closest('a, button, input, select, textarea, label, form')) {
                    return;
                }
                var url = row.getAttribute('data-href');
                if (url) {
                    window.location.href = url;
                }
            });
            row.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    if (e.target.closest('a, button, input, select, textarea, label, form')) {
                        return;
                    }
                    e.preventDefault();
                    var url = row.getAttribute('data-href');
                    if (url) {
                        window.location.href = url;
                    }
                }
            });
        });
    }

    // 5. Konfirmasi SweetAlert2 & anti double-submit + pageshow bfcache reset
    window.eccLowConfirmSubmit = function (event, title, text, confirmText, isDanger) {
        var form = event.target.closest('form') || event.target;
        if (!form || form.dataset.confirmed === '1') {
            return true;
        }
        event.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title || 'Konfirmasi',
                text: text || 'Lanjutkan proses ini?',
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: confirmText || 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: isDanger ? '#dc2626' : '#0d6efd'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
            return false;
        }
        if (confirm(text || 'Lanjutkan proses ini?')) {
            form.dataset.confirmed = '1';
            form.submit();
            return true;
        }
        return false;
    };

    function initLowFormGuard() {
        document.querySelectorAll('form[method="post"], form[method="POST"]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (e.defaultPrevented) {
                    return;
                }
                if (form.dataset.submitting === '1') {
                    e.preventDefault();
                    return;
                }
                form.dataset.submitting = '1';
                form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
                    btn.classList.add('disabled');
                });
            });
        });

        window.addEventListener('pageshow', function () {
            document.querySelectorAll('form[data-submitting="1"], form[data-confirmed="1"]').forEach(function (form) {
                delete form.dataset.submitting;
                delete form.dataset.confirmed;
                form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
                    btn.classList.remove('disabled');
                    btn.disabled = false;
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initLowFlatpickr();
        initLowSelect2();
        initLowSortableTables();
        initLowClickableRows();
        initLowFormGuard();
    });
})();
</script>
