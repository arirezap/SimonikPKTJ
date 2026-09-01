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
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 rounded-4 overflow-hidden notif-dropdown-menu" style="width: 380px; max-width: 95vw;">
                                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
                                    <h6 class="m-0 fw-bold text-dark d-flex align-items-center" style="font-size: 0.95rem;"><i class="bi bi-bell-fill me-1.5 text-primary"></i> Notifikasi</h6>
                                    <button type="button" class="btn btn-link btn-sm text-primary text-decoration-none p-0 fw-semibold btn-tactile d-flex align-items-center gap-1" id="markAllReadBtn" onclick="markAllNotificationsRead(event)" style="font-size: 0.76rem;">
                                        <i class="bi bi-check2-all"></i> Tandai Semua Dibaca
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
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
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
                <span class="badge bg-light text-secondary border rounded-pill version-badge px-2 py-1" style="font-size: 0.75rem; font-variant-numeric: tabular-nums;">v1.3</span>
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

        // --- NOTIFICATION LOGIC ---
        function fetchNotifications() {
            fetch('<?= site_url('notifications/fetch') ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                const notifBadge = document.getElementById('notifBadge');
                const notifList = document.getElementById('notifList');
                
                if (data.status === 'success') {
                    const unreadCount = parseInt(data.unread_count ?? data.count ?? 0);
                    // Update Badge
                    if (unreadCount > 0) {
                        notifBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                        notifBadge.classList.remove('d-none');
                    } else {
                        notifBadge.classList.add('d-none');
                        notifBadge.textContent = '0';
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

                            const titleLower = (item.title || '').toLowerCase();

                            if (item.id === 'virtual_target_awal_bulan' || item.id === 'virtual_target_draft_reminder') {
                                bgClass = 'bg-primary';
                                textClass = 'text-primary';
                                icon = 'bi-bullseye';
                            } else if (item.id === 'virtual_target_approval_needed') {
                                bgClass = 'bg-primary';
                                textClass = 'text-primary';
                                icon = 'bi-person-check-fill';
                            } else if (item.id === 'virtual_penilaian_bulan_lalu') {
                                bgClass = 'bg-info';
                                textClass = 'text-info';
                                icon = 'bi-clipboard-check-fill';
                            } else if (item.id === 'virtual_reminder') {
                                bgClass = 'bg-warning';
                                textClass = 'text-warning';
                                icon = 'bi-journal-check';
                            } else if (item.id === 'virtual_target_deadline' || item.id === 'virtual_penilaian_deadline') {
                                bgClass = 'bg-danger';
                                textClass = 'text-danger';
                                icon = 'bi-exclamation-triangle-fill';
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

                            const safeLink = (item.link && !item.link.toLowerCase().startsWith('javascript:')) ? item.link : '#';
                            const safeTitle = escapeHtml(item.title);
                            const safeMessage = escapeHtml(item.message);
                            const safeTime = escapeHtml(item.time_ago || '');
                            const unreadDotHtml = isUnread ? '<span class="d-inline-block bg-primary rounded-circle notif-unread-dot" title="Belum dibaca"></span>' : '';
                            const itemClass = isUnread ? 'notif-item is-unread' : 'notif-item is-read';
                            const titleWeight = isUnread ? 'fw-bold text-dark' : 'fw-semibold text-body-secondary';
                            
                            html += `
                                <a href="${safeLink}" class="list-group-item list-group-item-action border-0 border-bottom p-3 d-flex gap-3 align-items-start ${itemClass} btn-tactile" data-notif-id="${escapeHtml(item.id)}" onclick="markNotifRead('${escapeHtml(item.id)}', event, this, '${safeLink}')">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle ${bgClass} bg-opacity-10 ${textClass} notif-icon-box flex-shrink-0">
                                        <i class="bi ${icon}"></i>
                                    </div>
                                    <div class="flex-grow-1 pe-1 overflow-hidden">
                                        <h6 class="notif-title ${titleWeight}">${safeTitle}</h6>
                                        <p class="notif-desc">${safeMessage}</p>
                                        <small class="notif-time d-flex align-items-center gap-1"><i class="bi bi-clock"></i> ${safeTime}</small>
                                    </div>
                                    <div class="align-self-center ps-1 flex-shrink-0">
                                        ${unreadDotHtml}
                                    </div>
                                </a>
                            `;
                        });
                        notifList.innerHTML = html;
                    } else {
                        notifList.innerHTML = `
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-bell-slash fs-1 text-secondary opacity-50 notif-empty-icon d-inline-block mb-1"></i>
                                <div class="mt-2 small text-secondary fw-medium">Belum ada riwayat notifikasi.</div>
                            </div>
                        `;
                    }
                } else {
                    notifList.innerHTML = `
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning d-inline-block mb-1"></i>
                            <div class="mt-2 small text-secondary">Gagal memuat notifikasi.</div>
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
                            <div class="mt-2 small">Terjadi kesalahan pada server.</div>
                        </div>
                    `;
                }
            });
        }

        // Panggil saat pertama kali load
        fetchNotifications();

        // Polling setiap 5 menit (300.000ms)
        setInterval(fetchNotifications, 300000); 
    });

    function markAllNotificationsRead(e) {
        if (e) e.preventDefault();
        
        const notifBadge = document.getElementById('notifBadge');
        if (notifBadge) {
            notifBadge.classList.add('d-none');
            notifBadge.textContent = '0';
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

    function markNotifRead(id, event, element, link) {
        // Jika ada link valid dan pengguna mengklik, kita izinkan navigasi setelah update status
        const hasValidLink = link && link !== 'null' && link !== '#';
        if (!hasValidLink) {
            event.preventDefault();
        }
        
        // Ubah tampilan notifikasi ini menjadi status terbaca
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

        // Kurangi count unread badge jika masih ada
        const notifBadge = document.getElementById('notifBadge');
        if (notifBadge && !notifBadge.classList.contains('d-none')) {
            let currentCount = parseInt(notifBadge.textContent);
            if (!isNaN(currentCount) && currentCount > 0) {
                currentCount--;
                if (currentCount <= 0) {
                    notifBadge.classList.add('d-none');
                    notifBadge.textContent = '0';
                } else {
                    notifBadge.textContent = currentCount;
                }
            }
        }

        const csrfTokenName = '<?= csrf_token() ?>';
        const csrfHash = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '<?= csrf_hash() ?>';

        fetch(`<?= site_url('notifications/read/') ?>${id}`, {
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
            if (hasValidLink) {
                window.location.href = link;
            }
        })
        .catch(error => {
            if (hasValidLink) {
                window.location.href = link;
            }
        });
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
                title: 'Konfirmasi Keluar',
                text: 'Apakah Anda yakin ingin mengakhiri sesi dan keluar dari sistem?',
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
        } else if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
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