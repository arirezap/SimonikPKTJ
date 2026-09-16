<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Cache Control: Paksa browser selalu ambil halaman terbaru dari server -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php 
    $pageTitle = $this->renderSection('title');
    $pageTitle = trim($pageTitle);
    if (empty($pageTitle)) {
        $pageTitle = 'ECC';
    } else {
        $pageTitle .= ' - ECC';
    }
    ?>
    <title><?= $pageTitle ?></title>

    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('favicon-32x32.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('android-chrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('android-chrome-512x512.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=1.4.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">

    <?= $this->renderSection('styles') ?>
</head>

<body>
    <!-- Bootstrap Offcanvas menangani backdrop secara otomatis -->

    <div class="main-wrapper">
        <?= $this->include('layouts/sidebar') ?>

        <div class="content-wrapper">
            <?php 
                $isMaintenanceModeOn = false;
                try {
                    $settingMdl = new \App\Models\SettingModel();
                    $isMaintenanceModeOn = ($settingMdl->getValue('enable_maintenance_mode', '0') === '1');
                } catch (\Throwable $e) {}
            ?>
            <?php if ($isMaintenanceModeOn && hasRole('admin')): ?>
                <div class="alert alert-warning border-0 rounded-0 py-2 px-3 mb-0 d-flex flex-wrap align-items-center justify-content-between text-dark shadow-sm" style="background: linear-gradient(90deg, #fef3c7, #fde68a); border-bottom: 2px solid #f59e0b !important; font-size: 0.82rem; z-index: 1040;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="spinner-grow spinner-grow-sm text-warning" role="status"></span>
                        <span><strong>Mode Pemeliharaan AKTIF:</strong> Pengguna biasa dialihkan ke halaman pemeliharaan. Anda memiliki akses penuh Administrator.</span>
                    </div>
                    <a href="<?= site_url('settings') ?>" class="btn btn-warning btn-sm rounded-pill px-3 py-0.5 fw-bold btn-tactile text-dark shadow-sm border border-warning-subtle" style="font-size: 0.75rem;">
                        <i class="bi bi-sliders me-1"></i> Kelola Pengaturan
                    </a>
                </div>
            <?php endif; ?>
            
            <header class="navbar navbar-expand header-promax mb-2 px-3 px-md-4 py-2">
                <div class="container-fluid px-0">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-primary d-lg-none me-2 p-0 text-decoration-none btn-tactile" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" aria-label="Buka navigasi menu">
                            <i class="bi bi-list fs-1 text-primary"></i>
                        </button>
                        
                        <?php
                            $rawName = session()->get('nama') ?? session()->get('nama_lengkap') ?? 'Pengguna';
                            $nameParts = explode(',', $rawName);
                            $mainName = trim($nameParts[0]);
                            $formattedName = ucwords(strtolower($mainName));
                            
                            if (count($nameParts) > 1) {
                                unset($nameParts[0]);
                                $formattedName .= ', ' . implode(', ', array_map('trim', $nameParts));
                            }
                            
                            $nameWords = explode(' ', $mainName);
                            $initials = strtoupper(substr($nameWords[0] ?? 'U', 0, 1));
                            if (count($nameWords) > 1) {
                                $initials .= strtoupper(substr($nameWords[1], 0, 1));
                            }
                        ?>

                        <div class="d-flex flex-column">
                            <span class="text-muted fw-medium" style="font-size: 0.75rem;">
                                Selamat datang,
                            </span>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="user-name fw-bold text-dark" style="font-size: 1.15rem; line-height: 1;">
                                    <?= esc($formattedName) ?>
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill fw-bold border border-primary-subtle d-none d-sm-inline-block" style="font-size: 0.6875rem; letter-spacing: 0.04em;">
                                    <?= str_replace('_', ' ', strtoupper(esc(session()->get('role') ?? 'GUEST'))) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <div class="date-text text-muted fw-medium d-none d-md-block" style="font-size: 0.85rem;">
                            <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?>
                        </div>
                        
                        <!-- NOTIFICATION BELL -->
                        <div class="dropdown" id="notifDropdownContainer">
                            <a href="#" class="text-decoration-none position-relative d-inline-block btn-tactile" data-bs-toggle="dropdown" aria-expanded="false" id="notifDropdownToggle" aria-label="Lihat notifikasi sistem">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow-subtle position-relative" style="width: 40px; height: 40px;">
                                    <i class="bi bi-bell-fill text-muted fs-5"></i>
                                    <span id="notifBadge" class="position-absolute badge rounded-pill bg-danger d-none shadow-sm" style="font-size: 0.55rem; padding: 0.2em 0.42em; top: -2px; right: -2px; border: 2px solid #ffffff; line-height: 1; z-index: 2;">
                                        0
                                    </span>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 rounded-4 overflow-hidden notif-dropdown-menu">
                                <div class="notif-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 0.9375rem;">
                                            <i class="bi bi-bell-fill me-2 text-primary"></i> Notifikasi
                                        </h6>
                                        <span id="notifHeaderBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-semibold d-none" style="font-size: 0.6875rem; padding: 2px 8px;">0 Baru</span>
                                    </div>
                                    <button type="button" class="btn btn-mark-all-read btn-tactile" id="markAllReadBtn" onclick="markAllNotificationsRead(event)" aria-label="Tandai semua notifikasi sudah dibaca">
                                        <i class="bi bi-check2-all fs-6"></i> <span>Tandai Semua Dibaca</span>
                                    </button>
                                </div>
                                <div id="notifList" class="list-group list-group-flush">
                                    <div class="p-4 text-center text-muted small">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                        <div class="mt-2">Memuat notifikasi...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a href="#" class="text-decoration-none d-inline-block btn-tactile" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu profil pengguna">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center shadow-sm profile-avatar" style="width: 42px; height: 42px; border: 2px solid #ffffff;">
                                    <?php
                                        $foto_session = session()->get('foto');
                                        $foto_header_path = 'assets/uploads/profile/' . $foto_session;
                                        if (!empty($foto_session) && file_exists(FCPATH . $foto_header_path)) :
                                    ?>
                                        <img src="<?= base_url($foto_header_path) ?>" alt="Foto Profil" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;" width="42" height="42">
                                    <?php else : ?>
                                        <span class="text-primary fw-bold" style="font-size: 1.05rem; letter-spacing: 0.5px;"><?= $initials ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-3" style="min-width: 200px;">
                                <li><h6 class="dropdown-header text-primary"><?= esc($formattedName) ?></h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item btn-tactile" href="<?= site_url('profile') ?>">
                                        <i class="bi bi-person-circle me-2"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger btn-tactile" href="<?= site_url('logout') ?>" onclick="confirmLogout(event)">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </a>
                                </li>
                            </ul>
                            <!-- Form Tersembunyi untuk Logout via POST (Anti-CSRF Protection) -->
                            <form id="logoutPostForm" action="<?= site_url('logout') ?>" method="post" class="d-none">
                                <?= csrf_field() ?>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="content-area">
                
                <div class="d-flex align-items-center mb-3">
                    <h3 class="mb-0 fw-bold text-dark"><?= $this->renderSection('page_title') ?></h3>
                </div>

                <?= $this->renderSection('content') ?>
            </div>
            
            <!-- Footer Aplikasi -->
            <footer class="footer-promax py-3 mt-auto d-flex justify-content-between px-3 px-md-4 align-items-center">
                <span class="footer-text">&copy; <?= date('Y') ?> Evidence Command Center (ECC) - PKTJ Tegal</span>
                <span class="badge bg-light text-secondary border rounded-pill version-badge px-2.5 py-1" style="font-size: 0.75rem; font-variant-numeric: tabular-nums;">v 1.4</span>
            </footer>

            <?= $this->renderSection('footer_bar') ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainWrapper = document.querySelector('.main-wrapper');

        // Fungsi untuk mengatur perilaku Dropdown
        function setSidebarState(isMini) {
            if (isMini) {
                mainWrapper.classList.add('sidebar-toggled');
            } else {
                mainWrapper.classList.remove('sidebar-toggled');
            }
        }

        // Cek LocalStorage saat load
        const isToggled = localStorage.getItem('sidebarToggled') === 'true';
        if (window.innerWidth >= 992) {
            setSidebarState(isToggled); // Terapkan preferensi mini/lebar HANYA di layar Desktop
        }

        // Event Listener Tombol Toggle (Desktop)
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                const willBeToggled = !mainWrapper.classList.contains('sidebar-toggled');
                setSidebarState(willBeToggled);
                localStorage.setItem('sidebarToggled', willBeToggled);
            });
        }

        // Smart Scroll Topbar Logic
        const topbar = document.querySelector('.header-promax');
        const contentWrapper = document.querySelector('.content-wrapper');
        let lastScrollY = contentWrapper.scrollTop;

        if (topbar && contentWrapper) {
            contentWrapper.addEventListener('scroll', () => {
                const currentScrollY = contentWrapper.scrollTop;
                
                // Jika scroll ke bawah dan melewati batas tertentu, sembunyikan topbar
                if (currentScrollY > lastScrollY && currentScrollY > 60) {
                    topbar.classList.add('header-hidden');
                } else {
                    // Jika scroll ke atas, tampilkan kembali topbar
                    topbar.classList.remove('header-hidden');
                }
                
                lastScrollY = currentScrollY;
            });
        }

        // --- NOTIFICATION LOGIC (SMART POLLING DENGAN PAGE VISIBILITY API) ---
        let lastNotifFetchTime = 0;
        let notifPollingInterval = null;
        let isFetchingNotif = false;
        const NOTIF_POLL_INTERVAL = 300000; // 5 Menit (300.000 ms)

        function fetchNotifications() {
            if (isFetchingNotif) return;
            isFetchingNotif = true;

            fetch('<?= site_url('notifications/fetch') ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                const notifBadge = document.getElementById('notifBadge');
                const notifHeaderBadge = document.getElementById('notifHeaderBadge');
                const notifList = document.getElementById('notifList');
                
                if (data.status === 'success') {
                    const unreadCount = parseInt(data.unread_count ?? data.count ?? 0);
                    // Update Badge
                    if (unreadCount > 0) {
                        notifBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
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

                    // Update List
                    if (data.data && data.data.length > 0) {
                        let html = '';
                        const escapeHtml = (str) => {
                            if (!str) return '';
                            return String(str)
                                .replace(/&/g, '&amp;')
                                .replace(/</g, '&lt;')
                                .replace(/>/g, '&gt;')
                                .replace(/"/g, '&quot;')
                                .replace(/'/g, '&#039;');
                        };

                        data.data.forEach(item => {
                            const isVirtual = item.is_virtual;
                            const isUnread = (parseInt(item.is_read) === 0) || isVirtual;
                            
                            let bgClass = 'bg-secondary';
                            let textClass = 'text-secondary';
                            let icon = 'bi-bell';
                            let badgeTag = '';

                            const titleLower = (item.title || '').toLowerCase();

                            if (item.id === 'virtual_target_awal_bulan' || item.id === 'virtual_target_draft_reminder') {
                                bgClass = 'bg-primary';
                                textClass = 'text-primary';
                                icon = 'bi-bullseye';
                                badgeTag = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-semibold" style="font-size: 0.625rem; padding: 2px 6px;">Target</span>';
                            } else if (item.id === 'virtual_target_approval_needed') {
                                bgClass = 'bg-primary';
                                textClass = 'text-primary';
                                icon = 'bi-person-check-fill';
                                badgeTag = '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fw-semibold" style="font-size: 0.625rem; padding: 2px 6px;">Persetujuan</span>';
                            } else if (item.id === 'virtual_penilaian_bulan_lalu') {
                                bgClass = 'bg-info';
                                textClass = 'text-info';
                                icon = 'bi-clipboard-check-fill';
                                badgeTag = '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fw-semibold" style="font-size: 0.625rem; padding: 2px 6px;">Penilaian</span>';
                            } else if (item.id === 'virtual_reminder') {
                                bgClass = 'bg-warning';
                                textClass = 'text-warning';
                                icon = 'bi-journal-check';
                                badgeTag = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fw-semibold" style="font-size: 0.625rem; padding: 2px 6px;">Laporan</span>';
                            } else if (item.id === 'virtual_target_deadline' || item.id === 'virtual_penilaian_deadline') {
                                bgClass = 'bg-danger';
                                textClass = 'text-danger';
                                icon = 'bi-exclamation-triangle-fill';
                                badgeTag = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fw-semibold" style="font-size: 0.625rem; padding: 2px 6px;">Mendesak</span>';
                            } else if (titleLower.includes('nilai') || titleLower.includes('diterbitkan')) {
                                bgClass = 'bg-success';
                                textClass = 'text-success';
                                icon = 'bi-award-fill';
                            } else if (titleLower.includes('disetujui')) {
                                bgClass = 'bg-success';
                                textClass = 'text-success';
                                icon = 'bi-check-circle-fill';
                            } else if (titleLower.includes('revisi') || titleLower.includes('dibatalkan')) {
                                bgClass = 'bg-warning';
                                textClass = 'text-warning';
                                icon = 'bi-pencil-square';
                            } else if (isVirtual) {
                                bgClass = 'bg-warning';
                                textClass = 'text-warning';
                                icon = 'bi-exclamation-circle-fill';
                            } else if (isUnread) {
                                bgClass = 'bg-primary';
                                textClass = 'text-primary';
                                icon = 'bi-bell-fill';
                            }

                            const safeLink = resolveAppUrl(item.link);
                            const safeTitle = escapeHtml(item.title);
                            const safeMessage = escapeHtml(item.message);
                            const safeTime = escapeHtml(item.time_ago || '');
                            const unreadDotHtml = isUnread ? '<span class="notif-unread-dot" title="Belum dibaca"></span>' : '';
                            const itemClass = isUnread ? 'notif-item is-unread' : 'notif-item is-read';
                            const titleWeight = isUnread ? 'fw-bold text-dark' : 'fw-semibold text-body-secondary';
                            
                            html += `
                                <a href="${safeLink}" class="list-group-item list-group-item-action border-0 d-flex align-items-start ${itemClass} btn-tactile" data-notif-id="${escapeHtml(item.id)}" onclick="markNotifRead('${escapeHtml(item.id)}', event, this, '${safeLink}')">
                                    <div class="${bgClass} bg-opacity-10 ${textClass} notif-icon-box">
                                        <i class="bi ${icon}"></i>
                                    </div>
                                    <div class="flex-grow-1 pe-1 overflow-hidden">
                                        <h6 class="notif-title ${titleWeight}">${safeTitle}</h6>
                                        <p class="notif-desc">${safeMessage}</p>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <small class="notif-time d-flex align-items-center gap-1">
                                                <i class="bi bi-clock"></i> ${safeTime}
                                            </small>
                                            ${badgeTag}
                                        </div>
                                    </div>
                                    <div class="align-self-center ps-1 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 16px; min-height: 16px;">
                                        ${unreadDotHtml}
                                    </div>
                                </a>
                            `;
                        });
                        notifList.innerHTML = html;
                    } else {
                        notifList.innerHTML = `
                            <div class="py-5 px-3 text-center text-muted">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3 shadow-subtle" style="width: 56px; height: 56px;">
                                    <i class="bi bi-bell-slash fs-3 text-secondary opacity-50 notif-empty-icon"></i>
                                </div>
                                <div class="fw-semibold text-dark mb-1" style="font-size: 0.875rem;">Belum ada notifikasi baru</div>
                                <div class="small text-secondary" style="font-size: 0.78rem;">Semua tugas dan target kinerja Anda telah terpantau rapi.</div>
                            </div>
                        `;
                    }
                } else {
                    notifList.innerHTML = `
                        <div class="py-4 px-3 text-center text-muted">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning d-inline-block mb-2"></i>
                            <div class="small text-secondary fw-medium">Gagal memuat notifikasi. Silakan coba lagi.</div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                const notifList = document.getElementById('notifList');
                if (notifList) {
                    notifList.innerHTML = `
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-x-circle fs-1 text-danger"></i>
                            <div class="mt-2 small text-secondary">Gagal memuat notifikasi. Silakan coba lagi.</div>
                        </div>
                    `;
                }
            })
            .finally(() => {
                isFetchingNotif = false;
                lastNotifFetchTime = Date.now();
            });
        }

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

        // Panggil saat pertama kali halaman dibuka
        fetchNotifications();
        startNotifPolling();

        // Smart Visibility Management (HTML5 Page Visibility API)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // Tab diminimalkan / disembunyikan: jeda interval untuk menghemat resource koneksi peladen
                stopNotifPolling();
            } else {
                // Tab kembali aktif: jika sudah >= 5 menit sejak pemanggilan terakhir, ambil data seketika
                if (Date.now() - lastNotifFetchTime >= NOTIF_POLL_INTERVAL) {
                    fetchNotifications();
                }
                startNotifPolling();
            }
        });

        // Online recovery: jika koneksi internet pulih setelah sempat offline
        window.addEventListener('online', function() {
            if (!document.hidden && (Date.now() - lastNotifFetchTime >= NOTIF_POLL_INTERVAL)) {
                fetchNotifications();
            }
            startNotifPolling();
        });

        // Prefetch on bell dropdown click jika data saat ini sudah lebih dari 60 detik
        const notifDropdownToggle = document.getElementById('notifDropdownToggle');
        if (notifDropdownToggle) {
            notifDropdownToggle.addEventListener('click', function() {
                if (Date.now() - lastNotifFetchTime > 60000) {
                    fetchNotifications();
                }
            });
        }
    });

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
        
        // Perbarui visual seluruh notifikasi di list menjadi status terbaca (tanpa menghapus item)
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
        .then(response => response.json())
        .then(data => {
            if (data && data.csrf_hash) {
                const metaCsrf = document.querySelector('meta[name="X-CSRF-TOKEN"]');
                if (metaCsrf) metaCsrf.setAttribute('content', data.csrf_hash);
            }
        })
        .catch(err => console.error('Error marking all notifications as read:', err));
    }

    function resolveAppUrl(rawLink) {
        if (!rawLink || rawLink === '#' || rawLink.toLowerCase().startsWith('javascript:')) {
            return '#';
        }
        try {
            if (rawLink.startsWith('http://') || rawLink.startsWith('https://')) {
                const parsed = new URL(rawLink);
                // Jika domain berbeda dari domain host browser saat ini (misal link kinerja.pktj.ac.id saat sedang di lokal/simonikpktj.test atau sebaliknya)
                if (parsed.host !== window.location.host) {
                    return window.location.origin + parsed.pathname + parsed.search + parsed.hash;
                }
                return rawLink;
            }
            if (rawLink.startsWith('/')) {
                return window.location.origin + rawLink;
            }
            return window.location.origin + '/' + rawLink;
        } catch (e) {
            return rawLink;
        }
    }

    function markNotifRead(id, event, element, link) {
        if (event) {
            event.preventDefault();
        }
        
        const targetLink = resolveAppUrl(link);
        const hasValidLink = targetLink && targetLink !== 'null' && targetLink !== '#' && !targetLink.toLowerCase().startsWith('javascript:');
        
        // 1. Ubah tampilan notifikasi ini menjadi status terbaca seketika
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

        // 2. Kurangi count unread badge secara instan
        const notifBadge = document.getElementById('notifBadge');
        const notifHeaderBadge = document.getElementById('notifHeaderBadge');
        if (notifBadge && !notifBadge.classList.contains('d-none')) {
            let currentCount = parseInt(notifBadge.textContent);
            if (!isNaN(currentCount) && currentCount > 0) {
                currentCount--;
                if (currentCount <= 0) {
                    notifBadge.classList.add('d-none');
                    notifBadge.textContent = '0';
                    if (notifHeaderBadge) notifHeaderBadge.classList.add('d-none');
                } else {
                    notifBadge.textContent = currentCount;
                    if (notifHeaderBadge) notifHeaderBadge.textContent = `${currentCount} Baru`;
                }
            }
        }

        // 3. Update status dibaca di server untuk notifikasi database via background fetch (keepalive)
        if (!String(id).startsWith('virtual_')) {
            const csrfTokenName = '<?= csrf_token() ?>';
            const csrfHash = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '<?= csrf_hash() ?>';

            try {
                fetch(`<?= site_url('notifications/read/') ?>${id}`, {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded' 
                    },
                    body: `${csrfTokenName}=${encodeURIComponent(csrfHash)}`,
                    keepalive: true
                }).catch(() => {});
            } catch (e) {}
        }

        // 4. Langsung alihkan pengguna ke menu tujuan tanpa jeda jaringan
        if (hasValidLink) {
            window.location.href = targetLink;
        }
    }

    function confirmLogout(event) {
        if (event) event.preventDefault();
        const executeLogout = function() {
            const logoutForm = document.getElementById('logoutPostForm');
            if (logoutForm) {
                logoutForm.submit();
            } else {
                window.location.href = '<?= site_url('logout') ?>';
            }
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Keluar dari Sistem?',
                text: 'Sesi Anda saat ini akan diakhiri.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-box-arrow-right me-1.5"></i> Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0 p-4',
                    title: 'fw-bold text-dark fs-5 mb-2',
                    htmlContainer: 'text-muted small mb-4',
                    confirmButton: 'btn btn-danger btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm',
                    cancelButton: 'btn btn-secondary btn-tactile rounded-pill px-4 py-2 fw-semibold shadow-sm'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    executeLogout();
                }
            });
        } else if (confirm('Keluar dari sistem?')) {
            executeLogout();
        }
    }

    // Cegah perubahan angka pada input type=number secara tidak sengaja saat scrolling halaman
    document.addEventListener('wheel', function(e) {
        if (document.activeElement && document.activeElement.type === 'number') {
            document.activeElement.blur();
        }
    });
</script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>