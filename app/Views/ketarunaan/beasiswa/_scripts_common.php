<script>
window.eccBeaShowMessage = function(title, text, icon) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: icon || 'warning',
            confirmButtonText: 'Mengerti',
            customClass: { popup: 'rounded-4 shadow border-0' }
        });
    } else {
        alert(title + ': ' + text);
    }
};

window.eccBeaConfirmSubmit = function(event, title, text, confirmLabel, confirmColor) {
    event.preventDefault();
    var form = event.target;
    if (!form) return false;

    var doSubmit = function() {
        form.dataset.confirmed = '1';
        var btns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        btns.forEach(function(b) {
            b.disabled = true;
            if (b.tagName === 'BUTTON' && !b.dataset.origHtml) {
                b.dataset.origHtml = b.innerHTML;
                b.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Memproses...';
            }
        });
        form.submit();
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: confirmColor === '#dc3545' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor || '#0d6efd',
            confirmButtonText: confirmLabel || 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-4 shadow border-0' }
        }).then(function(res) {
            if (res.isConfirmed) {
                doSubmit();
            }
        });
    } else if (confirm(text)) {
        doSubmit();
    }
    return false;
};

document.addEventListener('DOMContentLoaded', function() {
    // 1. Klik baris / kartu menuju detail
    document.querySelectorAll('.bea-row-clickable').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.closest('a, button, input, select, label, form')) {
                return;
            }
            var href = row.getAttribute('data-href');
            if (!href) return;
            if (e.ctrlKey || e.metaKey) {
                window.open(href, '_blank', 'noopener,noreferrer');
            } else {
                window.location.href = href;
            }
        });
        row.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                if (e.target.closest('a, button, input, select, label, form')) {
                    return;
                }
                e.preventDefault();
                var href = row.getAttribute('data-href');
                if (href) {
                    window.location.href = href;
                }
            }
        });
    });

    // 2. Sorting sisi klien untuk tabel berlabel data-sortable-table="true"
    document.querySelectorAll('table[data-sortable-table="true"]').forEach(function(tbl) {
        var tbody = tbl.querySelector('tbody');
        if (!tbody) return;
        tbl.querySelectorAll('.bea-sort-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var colIdx = parseInt(btn.getAttribute('data-sort-col') || '0', 10);
                var th = btn.closest('th');
                if (!th) return;
                var curDir = th.getAttribute('aria-sort') === 'ascending' ? 'asc' : (th.getAttribute('aria-sort') === 'descending' ? 'desc' : 'none');
                var nextDir = curDir === 'asc' ? 'desc' : 'asc';

                tbl.querySelectorAll('thead th').forEach(function(otherTh) {
                    otherTh.setAttribute('aria-sort', 'none');
                    otherTh.classList.remove('th-sorted');
                    var icon = otherTh.querySelector('.sort-indicator');
                    if (icon) {
                        icon.className = 'bi bi-arrow-down-up text-muted opacity-50 sort-indicator';
                    }
                });

                th.setAttribute('aria-sort', nextDir === 'asc' ? 'ascending' : 'descending');
                th.classList.add('th-sorted');
                var ind = btn.querySelector('.sort-indicator');
                if (ind) {
                    ind.className = (nextDir === 'asc' ? 'bi bi-arrow-up text-primary' : 'bi bi-arrow-down text-primary') + ' sort-indicator';
                }

                var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
                rows.sort(function(a, b) {
                    var tdA = a.children[colIdx];
                    var tdB = b.children[colIdx];
                    var valA = tdA ? (tdA.getAttribute('data-sort-val') || tdA.textContent || '').trim() : '';
                    var valB = tdB ? (tdB.getAttribute('data-sort-val') || tdB.textContent || '').trim() : '';
                    var numA = parseFloat(valA.replace(',', '.'));
                    var numB = parseFloat(valB.replace(',', '.'));
                    var cmp = (!isNaN(numA) && !isNaN(numB) && /^-?\d+(\.\d+)?$/.test(valA))
                        ? (numA - numB)
                        : valA.localeCompare(valB, 'id', { sensitivity: 'base' });
                    return nextDir === 'asc' ? cmp : -cmp;
                });
                rows.forEach(function(r) { tbody.appendChild(r); });
            });
        });
    });

    // 3. Anti double-submit pada form POST bertanda data-lock-submit="true"
    document.querySelectorAll('form[method="post"][data-lock-submit="true"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (e.defaultPrevented || form.dataset.submitting === '1') {
                return;
            }
            var clicked = document.activeElement;
            if (clicked && clicked.form === form && clicked.name && clicked.value && !form.querySelector('input[type="hidden"][data-submit-mirror="1"]')) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = clicked.name;
                hidden.value = clicked.value;
                hidden.setAttribute('data-submit-mirror', '1');
                form.appendChild(hidden);
            }
            form.dataset.submitting = '1';
            form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(btn) {
                btn.disabled = true;
                if (btn === clicked && btn.tagName === 'BUTTON' && !btn.dataset.origHtml) {
                    btn.dataset.origHtml = btn.innerHTML;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>Memproses...';
                }
            });
        });
    });
});

window.addEventListener('pageshow', function() {
    document.querySelectorAll('form[method="post"]').forEach(function(form) {
        form.dataset.submitting = '0';
        form.querySelectorAll('input[type="hidden"][data-submit-mirror="1"]').forEach(function(h) {
            h.remove();
        });
        form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function(btn) {
            btn.disabled = false;
            if (btn.dataset.origHtml) {
                btn.innerHTML = btn.dataset.origHtml;
            }
        });
    });
});
</script>
