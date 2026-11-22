<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notifBadge = document.getElementById('notifBadge');
        const notifHeaderBadge = document.getElementById('notifHeaderBadge');
        const notifList = document.getElementById('notifList');
        if (!notifBadge || !notifList) return;

        let lastNotifFetchTime = 0;
        let notifPollingInterval = null;
        let isFetchingNotif = false;
        const NOTIF_POLL_INTERVAL = 300000;

        function escapeNotifHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function resolveNotifTagMeta(item) {
            const id = String(item.id || '');
            const titleLower = String(item.title || '').toLowerCase();
            const meta = {
                bgClass: 'bg-secondary',
                textClass: 'text-secondary',
                icon: 'bi-bell',
                badgeTag: ''
            };

            if (id === 'virtual_target_awal_bulan' || id === 'virtual_target_draft_reminder') {
                meta.bgClass = 'bg-primary';
                meta.textClass = 'text-primary';
                meta.icon = 'bi-bullseye';
                meta.badgeTag = '<span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill fw-semibold" style="font-size: 0.75rem; padding: 4px 8px;">Target</span>';
            } else if (id === 'virtual_target_approval_needed') {
                meta.bgClass = 'bg-primary';
                meta.textClass = 'text-primary';
                meta.icon = 'bi-person-check-fill';
                meta.badgeTag = '<span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle rounded-pill fw-semibold" style="font-size: 0.75rem; padding: 4px 8px;">Persetujuan</span>';
            } else if (id === 'virtual_penilaian_bulan_lalu') {
                meta.bgClass = 'bg-info';
                meta.textClass = 'text-info';
                meta.icon = 'bi-clipboard-check-fill';
                meta.badgeTag = '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill fw-semibold" style="font-size: 0.75rem; padding: 4px 8px;">Penilaian</span>';
            } else if (id === 'virtual_reminder') {
                meta.bgClass = 'bg-warning';
                meta.textClass = 'text-warning-emphasis';
                meta.icon = 'bi-journal-check';
                meta.badgeTag = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fw-semibold" style="font-size: 0.75rem; padding: 4px 8px;">Laporan</span>';
            } else if (id === 'virtual_target_deadline' || id === 'virtual_penilaian_deadline') {
                meta.bgClass = 'bg-danger';
                meta.textClass = 'text-danger';
                meta.icon = 'bi-exclamation-triangle-fill';
                meta.badgeTag = '<span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle rounded-pill fw-semibold" style="font-size: 0.75rem; padding: 4px 8px;">Mendesak</span>';
            } else if (titleLower.includes('nilai') || titleLower.includes('diterbitkan')) {
                meta.bgClass = 'bg-success';
                meta.textClass = 'text-success';
                meta.icon = 'bi-award-fill';
            } else if (titleLower.includes('disetujui')) {
                meta.bgClass = 'bg-success';
                meta.textClass = 'text-success';
                meta.icon = 'bi-check-circle-fill';
            } else if (titleLower.includes('revisi') || titleLower.includes('dibatalkan')) {
                meta.bgClass = 'bg-warning';
                meta.textClass = 'text-warning-emphasis';
                meta.icon = 'bi-pencil-square';
            } else if (item.is_virtual) {
                meta.bgClass = 'bg-warning';
                meta.textClass = 'text-warning-emphasis';
                meta.icon = 'bi-exclamation-circle-fill';
            } else if ((parseInt(item.is_read, 10) || 0) === 0) {
                meta.bgClass = 'bg-primary';
                meta.textClass = 'text-primary';
                meta.icon = 'bi-bell-fill';
            }

            return meta;
        }

        function renderEmptyNotifications() {
            notifList.innerHTML = `
                <div class="py-5 px-3 text-center text-muted">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3 shadow-subtle" style="width: 56px; height: 56px;">
                        <i class="bi bi-bell-slash fs-3 text-secondary opacity-50 notif-empty-icon" aria-hidden="true"></i>
                    </div>
                    <div class="fw-semibold text-dark mb-1" style="font-size: 0.875rem;">Belum ada notifikasi baru</div>
                    <div class="small text-secondary" style="font-size: 0.75rem;">Semua tugas dan pemantauan Anda sudah terbaca.</div>
                </div>
            `;
        }

        function renderErrorNotifications() {
            notifList.innerHTML = `
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-exclamation-circle fs-3 text-danger d-inline-block mb-2" aria-hidden="true"></i>
                    <div class="small text-secondary fw-medium">Gagal memuat notifikasi. Silakan coba lagi.</div>
                </div>
            `;
        }

        function fetchNotifications() {
            if (isFetchingNotif) return;
            isFetchingNotif = true;

            fetch('<?= site_url('notifications/fetch') ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.status !== 'success') {
                    renderErrorNotifications();
                    return;
                }

                const unreadCount = parseInt(data.unread_count ?? data.count ?? 0, 10) || 0;
                if (unreadCount > 0) {
                    notifBadge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
                    notifBadge.classList.remove('d-none');
                    if (notifHeaderBadge) {
                        notifHeaderBadge.textContent = `${unreadCount} Baru`;
                        notifHeaderBadge.classList.remove('d-none');
                    }
                } else {
                    notifBadge.classList.add('d-none');
                    notifBadge.textContent = '0';
                    if (notifHeaderBadge) {
                        notifHeaderBadge.classList.add('d-none');
                    }
                }

                if (!Array.isArray(data.data) || data.data.length === 0) {
                    renderEmptyNotifications();
                    return;
                }

                let html = '';
                data.data.forEach(item => {
                    const isVirtual = Boolean(item.is_virtual);
                    const isUnread = (parseInt(item.is_read, 10) === 0) || isVirtual;
                    const tag = resolveNotifTagMeta(item);
                    const safeLink = escapeNotifHtml(resolveAppUrl(item.link));
                    const safeId = escapeNotifHtml(item.id);
                    const safeTitle = escapeNotifHtml(item.title);
                    const safeMessage = escapeNotifHtml(item.message);
                    const safeTime = escapeNotifHtml(item.time_ago || '');
                    const unreadDotHtml = isUnread ? '<span class="notif-unread-dot" title="Belum dibaca"></span>' : '';
                    const itemClass = isUnread ? 'notif-item is-unread' : 'notif-item is-read';
                    const titleWeight = isUnread ? 'fw-bold text-dark' : 'fw-semibold text-body-secondary';

                    html += `
                        <a href="${safeLink}" class="list-group-item list-group-item-action border-0 d-flex align-items-start ${itemClass} btn-tactile" data-notif-id="${safeId}" data-notif-link="${safeLink}">
                            <div class="${tag.bgClass} bg-opacity-10 ${tag.textClass} notif-icon-box">
                                <i class="bi ${tag.icon}" aria-hidden="true"></i>
                            </div>
                            <div class="flex-grow-1 pe-1 overflow-hidden">
                                <h6 class="notif-title ${titleWeight}">${safeTitle}</h6>
                                <p class="notif-desc">${safeMessage}</p>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <small class="notif-time d-flex align-items-center gap-1">
                                        <i class="bi bi-clock" aria-hidden="true"></i> ${safeTime}
                                    </small>
                                    ${tag.badgeTag}
                                </div>
                            </div>
                            <div class="align-self-center ps-1 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 16px; min-height: 16px;">
                                ${unreadDotHtml}
                            </div>
                        </a>
                    `;
                });

                notifList.innerHTML = html;
            })
            .catch(() => {
                renderErrorNotifications();
            })
            .finally(() => {
                isFetchingNotif = false;
                lastNotifFetchTime = Date.now();
            });
        }

        notifList.addEventListener('click', function (event) {
            const anchor = event.target.closest('a[data-notif-id]');
            if (!anchor) return;
            const id = anchor.getAttribute('data-notif-id') || '';
            const link = anchor.getAttribute('data-notif-link') || '#';
            markNotifRead(id, event, anchor, link);
        });

        function startNotifPolling() {
            if (notifPollingInterval) {
                clearInterval(notifPollingInterval);
            }
            notifPollingInterval = setInterval(() => {
                if (!document.hidden) {
                    fetchNotifications();
                }
            }, NOTIF_POLL_INTERVAL);
        }

        function stopNotifPolling() {
            if (notifPollingInterval) {
                clearInterval(notifPollingInterval);
                notifPollingInterval = null;
            }
        }

        fetchNotifications();
        startNotifPolling();

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopNotifPolling();
            } else {
                if (Date.now() - lastNotifFetchTime >= NOTIF_POLL_INTERVAL) {
                    fetchNotifications();
                }
                startNotifPolling();
            }
        });

        window.addEventListener('online', function () {
            if (!document.hidden && (Date.now() - lastNotifFetchTime >= NOTIF_POLL_INTERVAL)) {
                fetchNotifications();
            }
            startNotifPolling();
        });

        const notifDropdownToggle = document.getElementById('notifDropdownToggle');
        if (notifDropdownToggle) {
            notifDropdownToggle.addEventListener('click', function () {
                if (Date.now() - lastNotifFetchTime > 60000) {
                    fetchNotifications();
                }
            });
        }
    });

    function syncCsrfHashEverywhere(newHash) {
        if (!newHash) return;
        const metaCsrf = document.querySelector('meta[name="X-CSRF-TOKEN"]');
        if (metaCsrf) metaCsrf.setAttribute('content', newHash);
        const tokenName = '<?= csrf_token() ?>';
        document.querySelectorAll(`input[name="${tokenName}"]`).forEach(input => {
            input.value = newHash;
        });
    }

    function resolveAppUrl(rawLink) {
        if (!rawLink || rawLink === '#') {
            return '#';
        }
        const trimmed = String(rawLink).trim();
        const lower = trimmed.toLowerCase();
        if (lower.startsWith('javascript:') || lower.startsWith('data:') || lower.startsWith('vbscript:')) {
            return '#';
        }
        try {
            if (lower.startsWith('http://') || lower.startsWith('https://')) {
                const parsed = new URL(trimmed);
                if (parsed.host !== window.location.host) {
                    return window.location.origin + parsed.pathname + parsed.search + parsed.hash;
                }
                return parsed.href;
            }
            if (trimmed.startsWith('/')) {
                return window.location.origin + trimmed;
            }
            return window.location.origin + '/' + trimmed;
        } catch (e) {
            return '#';
        }
    }

    function markAllNotificationsRead(e) {
        if (e) e.preventDefault();

        const notifBadge = document.getElementById('notifBadge');
        if (notifBadge) {
            notifBadge.classList.add('d-none');
            notifBadge.textContent = '0';
        }
        const notifHeaderBadge = document.getElementById('notifHeaderBadge');
        if (notifHeaderBadge) {
            notifHeaderBadge.classList.add('d-none');
            notifHeaderBadge.textContent = '0 Baru';
        }

        document.querySelectorAll('#notifList a').forEach(el => {
            el.classList.remove('is-unread');
            el.classList.add('is-read');
            const dot = el.querySelector('.notif-unread-dot');
            if (dot) dot.remove();
            const title = el.querySelector('h6');
            if (title) {
                title.classList.remove('fw-bold', 'text-dark');
                title.classList.add('fw-semibold', 'text-body-secondary');
            }
        });

        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfHash = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '<?= csrf_hash() ?>';

        fetch('<?= site_url('notifications/read-all') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `${csrfTokenName}=${encodeURIComponent(csrfHash)}`
        })
        .then(response => (response.ok ? response.json() : null))
        .then(data => {
            if (data && data.csrf_hash) {
                syncCsrfHashEverywhere(data.csrf_hash);
            }
        })
        .catch(() => {});
    }

    function markNotifRead(id, event, element, link) {
        if (event) {
            event.preventDefault();
        }

        const targetLink = resolveAppUrl(link);
        const hasValidLink = targetLink && targetLink !== 'null' && targetLink !== '#';

        if (element) {
            element.classList.remove('is-unread');
            element.classList.add('is-read');
            const dot = element.querySelector('.notif-unread-dot');
            if (dot) dot.remove();
            const title = element.querySelector('h6');
            if (title) {
                title.classList.remove('fw-bold', 'text-dark');
                title.classList.add('fw-semibold', 'text-body-secondary');
            }
        }

        const notifBadge = document.getElementById('notifBadge');
        const notifHeaderBadge = document.getElementById('notifHeaderBadge');
        if (notifBadge && !notifBadge.classList.contains('d-none')) {
            let currentCount = parseInt(notifBadge.textContent, 10);
            if (!isNaN(currentCount) && currentCount > 0) {
                currentCount--;
                if (currentCount <= 0) {
                    notifBadge.classList.add('d-none');
                    notifBadge.textContent = '0';
                    if (notifHeaderBadge) notifHeaderBadge.classList.add('d-none');
                } else {
                    notifBadge.textContent = String(currentCount);
                    if (notifHeaderBadge) notifHeaderBadge.textContent = `${currentCount} Baru`;
                }
            }
        }

        if (!String(id).startsWith('virtual_')) {
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfHash = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '<?= csrf_hash() ?>';

            try {
                fetch(`<?= site_url('notifications/read/') ?>${encodeURIComponent(String(id))}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `${csrfTokenName}=${encodeURIComponent(csrfHash)}`,
                    keepalive: true
                })
                .then(response => (response.ok ? response.json() : null))
                .then(data => {
                    if (data && data.csrf_hash) {
                        syncCsrfHashEverywhere(data.csrf_hash);
                    }
                })
                .catch(() => {});
            } catch (e) {}
        }

        if (hasValidLink) {
            window.location.href = targetLink;
        }
    }
</script>
