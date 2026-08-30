# 🎨 DESIGN SYSTEM & UI/UX GUIDELINES
## Evidence Command Center (ECC)
**Official Application Name:** Evidence Command Center (ECC) (formerly Simonik)  
**Version:** 1.3.0 (Enterprise Production Release)  
**Design Philosophy:** Modern Enterprise Bento Grid, Visual Excellence, Micro-Interactions, Mobile-First Touch Experience, 3-Tier Data Guards, and High-Performance Ergonomics.

---

## 1. 🌟 Core Design Philosophy & Principles

The Evidence Command Center (ECC) UI/UX is built to deliver a **world-class, high-density enterprise dashboard** that balances executive readability with rapid data entry for operational staff.

### Key Pillars:
1. **Bento Box Architecture**: Information is organized into clean, rounded, elevated surface containers (`.bento-card`) rather than rigid bordered boxes.
2. **Visual Hierarchy & Single Source of Action**: Every actionable button has a distinct purpose and is positioned according to the cognitive workflow (Setup actions at the top; Primary form execution at the bottom).
3. **No Redundancy & No Ambiguity**: Eliminates duplicate action buttons and ambiguous form affordances.
4. **3-Tier Protection & Data Guarding**: Visual highlight feedback on frontend (e.g. `.table-danger` on duplicate rows), instant client-side validation, and server-side atomic validation.
5. **Mobile-First & Touch-Friendly**: Dual-view paradigm where wide desktop tables seamlessly collapse into touch cards on smartphones (<768px), maintaining minimum 44px touch targets.
6. **Corporate Tone & Strict Terminology**: Strictly use **"staf"** across all UI labels, buttons, documentation, and variables (never use "staf").

---

## 2. 🎨 Design Tokens & Color Palette

### 2.1 Primary & Brand Palette
| Token Name | Hex Code | Purpose & Application |
| :--- | :--- | :--- |
| `--ecc-primary` | `#0d6efd` | Main CTA buttons, active navigation pills, primary links, key statistics |
| `--ecc-primary-dark` | `#1e40af` | Hover states, table header titles, executive accents |
| `--ecc-primary-subtle` | `#dbeafe` | Selected state backgrounds, soft indicator chips |
| `--ecc-navy-header` | `#0f172a` | Deep navy for high-contrast section headers |

### 2.2 Semantic & Status Colors
| Semantic State | Base Hex | Soft Background | Border / Accent | Usage in ECC |
| :--- | :--- | :--- | :--- | :--- |
| **Success (Baik / Disetujui)** | `#198754` | `#dcfce7` (`bg-success-subtle`) | `#bbf7d0` | Approved targets, score $\ge 90$, submitted reports, CREATE action |
| **Info (Standar / Terkirim)** | `#0284c7` | `#e0f2fe` (`bg-info-subtle`) | `#bae6fd` | Standard score ($75 - 89.9$), LOGIN/LOGOUT logs, Cuti Bersama, Modal Info |
| **Warning (Perhatian / Menunggu)** | `#d97706` | `#fef3c7` (`bg-warning-subtle`) | `#fde68a` | Needs attention score ($60 - 74.9$), pending approval, UPDATE action, Duplicate Warning |
| **Danger (Kurang / Ditolak)** | `#dc2626` | `#fee2e2` (`bg-danger-subtle`) | `#fecaca` | Score $< 60$, rejected status, delete actions, `.table-danger` highlight |
| **Secondary / Muted** | `#64748b` | `#f1f5f9` (`bg-secondary-subtle`) | `#e2e8f0` | Draft status, unrated logs, neutral badges, system logs |

### 2.3 Surface & Canvas Tokens
- **App Background**: `#f8fafc` (Cool Slate Canvas)
- **Surface / Card Background**: `#ffffff` (Pure White)
- **Subtle Surface Tint**: `#f1f5f9` (Used for `.date-group-odd` zebra striping and off-canvas panels)
- **Card Border**: `1px solid rgba(0, 0, 0, 0.05)`
- **Divider Line**: `1px solid #e2e8f0`

---

## 3. 🔤 Typography & Iconography

### 3.1 Typography Scale
- **Primary Font Family**: `'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif`
- **Monospace Font (Code & JSON Viewer)**: `SFMono-Regular, Menlo, Monaco, Consolas, monospace`

| Level | Size | Weight | Line Height | Usage |
| :--- | :--- | :--- | :--- | :--- |
| **Display / H1** | `1.75rem (28px)` | `700 (Bold)` | `1.2` | Page Titles, Metric Big Numbers |
| **Section H2 / H4** | `1.15rem (18px)` | `700 (Bold)` | `1.3` | Bento Card Headers, Modal Titles |
| **Subheader / H6** | `0.95rem (15px)` | `600 (Semi-Bold)`| `1.4` | Section dividing subtitles, Card Subheadings |
| **Body Default** | `0.875rem (14px)` | `400 / 500` | `1.5` | Table content, form inputs, general text |
| **Caption / Small** | `0.75rem (12px)` | `500 / 600` | `1.4` | Time stamps, NIP labels, sub-badges |

### 3.2 Iconography Standards
- **Strict Single Icon Set**: **Bootstrap Icons (`bi bi-...`)** exclusively across 100% of views.
- **Rules**:
  - Never mix with FontAwesome or custom unstandardized SVGs.
  - Standard Action Icons:
    - Add/Create: `bi-plus-circle` / `bi-plus-lg`
    - Edit/Revise: `bi-pencil-square`
    - Delete/Remove: `bi-trash3`
    - Approve/Verify: `bi-check-circle-fill`
    - Copy/Duplicate: `bi-copy` / `bi-arrow-down-left-square`
    - Refresh/Sync: `bi-arrow-repeat`
    - Search/Filter: `bi-search` / `bi-funnel-fill`
    - View/Detail: `bi-fullscreen` / `bi-eye`
    - Export Excel: `bi-file-earmark-excel-fill`

---

## 4. 🧩 Core Component Specifications

### 4.1 Bento Card Container (`.bento-card`)
```css
.bento-card {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.bento-card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
}
.bento-header {
    background: linear-gradient(to right, #f8f9fa, #ffffff);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 1.1rem 1.5rem;
}
```

### 4.2 Sidebar Menu Structure (v1.2 Categorization)
The navigation sidebar is structured into 4 logical category groups:
1. **Menu Utama**: Dashboard & Pusat Komando
2. **Kinerja & Aktivitas**: Target Kinerja Bulanan, Lapor Kegiatan Harian, Rekap & Penilaian Kinerja, Kelola Tim
3. **Dokumen & Panduan**: Kontrak Kinerja, Pakta Integritas, Panduan Penggunaan
4. **Administrasi & Sistem**: Master Data, Kelola Pengguna, Pengaturan Sistem, Audit Logs

**Styling Tokens:**
- High-contrast white background (`bg-white border-end`).
- Active item highlighted with soft blue pill (`background: #eff6ff; color: #2563eb; font-weight: 700;`).
- Custom slim scrollbar (`width: 4px; thumb: #cbd5e1;`).

### 4.3 Form Action Hierarchy & Table Toolbars
To eliminate cognitive clutter:
- **Table Toolbar Header (Top of Table)**: Contains context badge (`Rincian Target [Bulan] [Tahun]`) and secondary setup actions (e.g. `Salin dari Bulan Lain` pill button).
- **Form Action Hub (Bottom of Table)**: Dedicated purely to primary form lifecycle execution:
  - 🔵 **Tambah Target** (`btn-primary` pill): Inserts 1 new empty row.
  - ⚪ **Simpan Draf** (`btn-outline-primary` pill): Zero-reload AJAX autosave.
  - 🟢 **Ajukan Target** (`btn-success` pill): Primary submit CTA sending targets to direct supervisor.

### 4.4 Form Inputs, Number Controls & Wheel Protection
- **Standard Inputs**: Borderless light inputs (`bg-light border-0 rounded-3`) with active focus ring `box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15)`.
- **Score & Metric Inputs (`.col-nilai`, `.col-target`)**:
  - Number spinner arrows suppressed for clean numeric entry:
    ```css
    .input-nilai-capaian::-webkit-outer-spin-button,
    .input-nilai-capaian::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    ```
  - **Mouse Wheel Blur**: Event handler `$(document).on('wheel', 'input[type="number"]', function() { $(this).blur(); })` prevents accidental value modification during page scrolling.

### 4.5 3-Tier Data Deduplication & Visual Feedback
- **Salin Append Filter**: Auto-skips items that already exist in the target table when appending.
- **Visual Highlight**: Conflicting duplicate rows are immediately marked with `.table-danger` (soft red background) on client-side submit/save validation.
- **Backend Guard**: Hash tracking `mb_strtolower($sasaran . '|||' . $indikator)` in Controller prevents duplicate database entries.

### 4.6 Universal Excel Export Standards
All generated CSV/Excel exports across ECC (Rekap Kepegawaian, Master Data, etc.) follow:
1. **UTF-8 Byte Order Mark (BOM)**: `fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF))` for universal Microsoft Excel compatibility.
2. **Semicolon Delimiter (`;`)**: Standard delimiter for Indonesian locale Excel.
3. **NIP String Protection**: `="[NIP]"` to prevent automatic conversion into scientific notation (`1.998E+17`).
4. **Indonesian Decimal Format**: Decimal numbers formatted with commas (`,`).

---

## 5. 📱 Mobile & Responsive Experience Guidelines

### 5.1 Dual-View Presentation Pattern
For dense tabular data (Penilaian Kinerja, Rekap Kepegawaian, Activity Log):
- **Desktop ($\ge 768\text{px}$)**: Full desktop table with sticky header (`.desktop-table-view`).
- **Mobile ($< 768\text{px}$)**: Collapsible Touch Card List (`.mobile-cards-view`) presenting name, NIP, avatar, target count, and evaluation scores in stacked touch-friendly cards.

### 5.2 Mobile Touch Targets
- All action buttons and tabs maintain a minimum touch target size of **$44\text{px} \times 44\text{px}$**.
- iOS font-zoom fix: Enforces `font-size: 16px !important` on `.form-control` and `.form-select` on mobile screens to prevent unwanted viewport zooming.

### 5.3 Document & A4 Print Scaling
- Printable documents (Kontrak Kinerja & Pakta Integritas) render at strict $210\text{mm}$ (A4) width for desktop PDF export.
- On screens $< 768\text{px}$, responsive scaling enforces `.paper-container { width: 100% !important; font-size: 10pt !important; }` to eliminate horizontal scroll overflow.

---

## 6. ⚡ Performance & Asset Delivery Standards

### 6.1 Automatic Cache Busting
To ensure client browsers immediately receive styling updates without requiring manual cache clearing:
```html
<link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=1.3.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
```
Coupled with HTTP no-cache headers in `main.php`:
```html
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
```

### 6.2 Asynchronous Micro-Interactions & CSRF Synchronization
- **AJAX Dynamic CSRF**: All interactive modals and AJAX endpoints update the meta tag `X-CSRF-TOKEN` and all hidden form inputs on every JSON response.
- **SweetAlert2 with Fallback**: All destructive or confirmation actions use SweetAlert2 with native `window.confirm()` fallback for offline or CDN-delayed environments.
- **Zero-Reload Dynamic ID Injection**: Newly inserted rows receive their database IDs asynchronously via AJAX response, allowing immediate deletion or updates without page reload.

---

## 7. ✨ Motion Design & Animation Guidelines (/impeccable animate)

### 7.1 Motion Philosophy
- **Visitor Mode**: `Operate + Read`. Animations must prioritize zero cognitive load, instant perception of hierarchical structure, and seamless continuity.
- **Timing & Curves**:
  - Micro-Interactions (Hover, clicks): `0.2s - 0.25s cubic-bezier(0.16, 1, 0.3, 1)`
  - Surface Entrances (Bento Card cascade): `0.65s cubic-bezier(0.16, 1, 0.3, 1)` with $0.08\text{s}$ stagger delays (`.bento-stagger-1` through `.bento-stagger-4`).
  - Metric Count-Up Number Ticker (`animateValue()`): $700\text{ms} - 850\text{ms}$ with `1 - Math.pow(2, -10 * progress)` (`easeOutExpo`).
  - Chart Rendering: $900\text{ms}$ with `easeOutQuart`.

### 7.2 Core Motion Tokens
- **Bento Card Elevation**: `transform: translateY(-2.5px); box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);`
- **Tab Cross-Fade**: `transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1)` for zero layout shift during radar and analytics navigation.
- **Reduced Motion Fallback**:
  - Full adherence to `@media (prefers-reduced-motion: reduce)` in CSS and `window.matchMedia` in JavaScript tickers, rendering static values and zero translations for accessibility compliance.

### 7.3 High-Density Table & Data-Entry Motion Tokens
- **Row Insertion (`.row-slide-in`)**: `animation: rowSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1)` with subtle blue highlight background fading to transparent and automatic focus on the primary input (target dropdown in Tugas Pokok, deskripsi textarea in Tugas Tambahan).
- **Row Deletion (`.row-slide-out`)**: `animation: rowSlideOut 0.22s ease-out` with subtle scale-down and horizontal slide before DOM removal, eliminating abrupt layout jumps.
- **Dynamic Unit Badge Pop (`.badge-satuan-pop`)**: `transform: scale(1.08)` and soft tint transition on target dropdown change for instantaneous metric clarity.
- **Duplicate Collision Pulse (`.table-danger`)**: `animation: pulseDuplicate 0.4s ease-in-out` soft red pulse alerting users of duplicate entries without modal disruption.
- **Tactile Button Press (`.btn-tactile`)**: `transform: scale(0.97)` on `:active` with cubic-bezier response.

### 7.4 Evaluative Scoring Workbench Motion Tokens (`Rekap & Penilaian Kinerja`)
- **Real-Time Predikat Badge Pop (`.badge-predikat-pop`)**: `transform: scale(1.08)` and smooth transition during live scoring calculation for instantaneous qualitative feedback.
- **Executive Score Card Transition (`.score-card-transition`)**: `transition: border-color 0.3s ease, color 0.3s ease, background-color 0.3s ease` providing continuous visual feedback when typing employee scores.
- **Segmented Tab Cross-Fade (`.tab-content > .tab-pane`)**: `transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), transform 0.25s` with `translateY(4px) -> translateY(0)` on active state.

### 7.5 Global Shell, Topbar, Footer & Notification Motion Tokens
- **Compact Single-Line Navigation**: `--sidebar-width: 250px;`, `.sidebar .nav-link` font size `0.8125rem (13px)` with `white-space: nowrap;` and `text-overflow: ellipsis;` ensuring all navigation items (e.g. *"Rekap & Penilaian Kinerja"*, *"Target Kinerja Bulanan"*) strictly sit on **1 single line** in both normal and active states.
- **Sidebar Active Glow (`.sidebar .nav-link.active`)**: `background: linear-gradient(90deg, rgba(13, 110, 253, 0.12), rgba(13, 110, 253, 0.03)); border-left: 3px solid var(--ecc-primary); box-shadow: inset 3px 0 0 var(--ecc-primary);`
- **Sidebar Nav Hover Slide (`.sidebar .nav-link:hover`)**: `transform: translateX(4px)` with icon scale `transform: scale(1.14)` in $220\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)`.
- **Desktop Flyout Reveal (`@keyframes fadeInFlyout`)**: `opacity: 0 -> 1; transform: translateX(-8px) scale(0.98) -> translateX(0) scale(1)` in $200\text{ms}$.
- **Topbar Glassmorphism (`.header-promax`)**: `background: rgba(255, 255, 255, 0.88); backdrop-filter: blur(12px);` with smart scroll translateY hide/reveal in $280\text{ms}$.
- **Bell Swing Animation (`@keyframes bellSwing`)**: `rotate(0deg) -> 14deg -> -12deg -> 8deg -> 0deg` in $600\text{ms}$ on bell hover.
- **Unread Badge Heartbeat (`@keyframes notifBadgePulse`)**: `scale(1) -> scale(1.15)` with glowing red shadow pulse.
- **Notification Dropdown Spring (`@keyframes notifMenuReveal`)**: `opacity: 0 -> 1; transform: translateY(8px) scale(0.97) -> translateY(0) scale(1)` in $220\text{ms}$.
- **Unread Dot Pulse (`@keyframes unreadDotPulse`)**: `scale(1) -> scale(1.3)` with soft opacity cycle.
- **Notification Empty State Float (`@keyframes emptyFloat`)**: `translateY(0) -> translateY(-5px) -> translateY(0)` in $3\text{s}$ infinite cycle.
- **Footer Version Badge Hover (`.version-badge:hover`)**: `transform: translateY(-1.5px)` with elevation shadow in $200\text{ms}$.

### 7.6 Kepegawaian Remuneration & Performance Rekap Motion Tokens
- **Cascading Bento Stagger**: Header (`.bento-stagger-1`), KPI & Filter Grid (`.bento-stagger-2`), and Main Employee Table Card (`.bento-stagger-3`) entrance in $550\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)`.
- **KPI Summary & Modal Count-Up Tickers**: `animateValue()` using `easeOutExpo` ($700\text{ms} - 850\text{ms}$) on `#statTotalPegawai`, `#statSudahDinilai`, `#statBelumDinilai`, `#statRataRataInstansi`, and modal `#modalDetailScore`.
- **Pill Filter Tactile Tap**: `.filter-pill` with `scale(0.95)` tap response and `scale(1.02)` active elevated chip state.
- **Live Search & Filter Fade Transition (`@keyframes rowFadeIn`)**: Smooth $250\text{ms}$ `translateY(4px) -> translateY(0)` entrance for filtered employee rows.
### 7.7 Profile Management & Security UI & Motion Tokens
- **Interactive Avatar Focal Sequence**:
  - Hover & Focus: `.avatar-wrapper` scale transition (`scale(1.04)`) with `.avatar-overlay` backdrop blur `2px` in $200\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)`.
  - Photo Cross-Fade (`@keyframes avatarPhotoIn`): $300\text{ms}$ smooth scale ($0.94 \rightarrow 1.0$) and opacity transition upon FileReader local preview load.
  - Floating Camera Badge (`.avatar-badge-btn`): $44\text{px}$ touch target with active tactile press response (`scale(0.92)`).
- **Live Password Match Indicator (`@keyframes matchPopIn`)**:
  - $220\text{ms}$ micro-spring arrival ($translateY(-3px) \rightarrow 0$, $scale(0.97) \rightarrow 1.0$) providing instant affirmative feedback on password match.
- **Cascading Bento Layout**:
  - Header (`.bento-stagger-1`, delay $0.04\text{s}$), Avatar Overview Card (`.bento-stagger-2`, delay $0.10\text{s}$), and Security & Personal Info Bento Cards (`.bento-stagger-3`, delay $0.18\text{s}$) with $550\text{ms}$ entrance curve `cubic-bezier(0.16, 1, 0.3, 1)`.
### 7.8 Authentication & Session Termination UI & Motion Tokens (`Login & Logout`)
- **Login Experience & Password Toggle**:
  - Floating Card Elevation: `.card.border-0.shadow-lg.rounded-4` with smooth entrance fade ($450\text{ms}$).
  - Password Visibility Switch: `.btn-toggle-pw` toggles eye icon (`bi-eye` $\leftrightarrow$ `bi-eye-slash`) with subtle color shift.
  - Interactive Login Submit: Button active state with `.btn-tactile` and loading spinner state during authentication check.
- **Logout Lifecycle & Safety Interception**:
  - Destructive Color Token: `#dc3545` (`.text-danger`) for session termination indicator.
  - Interception Dialog: Modal SweetAlert2 with reverse button layout (*Batal* on left, *Ya, Keluar* on right) and keyboard `Escape` cancel support.
  - Anti-Double Submit Loading: `Swal.showLoading()` instantly locks the confirm button upon click, preventing duplicate POST requests.
  - Exit Continuity: Reassuring green flash alert upon landing at `/login` confirming safe session termination.

---

## 8. ♿ Accessibility (A11y) & Ergonomic Standards

The Evidence Command Center (ECC) is built to meet **WCAG 2.1 AA** compliance across all viewports:

1. **Accessible Focus-Visible Rings**:
   - Universal focus indicator: `box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15)` on `.form-control:focus`, `.form-select:focus`, and interactive controls, providing unmistakable focus feedback without harsh default browser outlines.
2. **Ergonomic Touch Targets ($\ge 44\text{px}$)**:
   - All mobile buttons, tab pills, camera badge triggers, and password visibility toggles enforce minimum touch dimensions of $44\text{px} \times 44\text{px}$ for comfortable one-handed thumb interaction.
3. **Mobile Floating Action Bar (<768px)**:
   - Long forms (such as Profile and Daily Reporting) feature a pinned bottom action bar (`.mobile-floating-bar` with `backdrop-filter: blur(12px)`) enabling instant one-tap saving without scrolling back to the bottom.
4. **Desktop Sticky Overview Sidebar ($\ge 992\text{px}$)**:
   - Persona and summary cards stay comfortably pinned (`position: sticky; top: 1.5rem;`) during long form scrolling.
5. **High-Contrast Text Hierarchy**:
   - Primary text uses deep navy `#0f172a` ensuring contrast ratios $\ge 4.5:1$ against pure white `#ffffff` and subtle background `#f8fafc`.
6. **Print-Optimized Stylesheet (`@media print`)**:
   - Clean printable A4 layout automatically suppressing non-essential interactive buttons, modals, and sticky bars for pristine physical documentation.

---

## 9. 🔔 SweetAlert2 & Notification Modal Standards (/ui-ux-pro-max)

To maintain absolute aesthetic harmony with ECC Bento Cards and eliminate disruptive modal antipatterns:

### 9.1 Confirmation & Interactive Dialog Tokens
All confirmation and interactive dialogs across ECC must adhere to the unified design tokens:
- **Popup Container**: `.rounded-4` ($20\text{px} / 1.25\text{rem}$ border-radius), `.shadow-lg`, `.border-0`, `padding: 1.75rem`.
- **Title & Typography**: `font-weight: 700`, `color: #0f172a`, `font-size: 1.2rem`, `letter-spacing: -0.02em`.
- **Action Buttons (`buttonsStyling: false`)**:
  - Primary / Confirm: `.btn.btn-primary.btn-tactile.rounded-pill.px-4.py-2.fw-semibold.shadow-sm`
  - Destructive / Logout: `.btn.btn-danger.btn-tactile.rounded-pill.px-4.py-2.fw-semibold.shadow-sm`
  - Cancel / Dismiss: `.btn.btn-secondary.btn-tactile.rounded-pill.px-4.py-2.fw-semibold.shadow-sm`
- **Reverse Button Layout**: Destructive and confirmation modals place the *Cancel* action on the left and the primary *Action* on the right for thumb ergonomics.

### 9.2 Non-Blocking Sleek Toast Notifications
Routine success notifications (such as successful session logout, draft auto-saving, or background sync) must **never** block the entire screen with modal popups. They must use non-blocking corner toasts:
```javascript
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
    customClass: {
        popup: 'rounded-4 shadow border-0'
    }
});
```

### 9.3 Resilience & Zero-Failure Native Fallback
Every modal and toast invocation must verify `typeof Swal !== 'undefined'` and supply native browser dialogs (`window.confirm()`, `window.alert()`) to guarantee 100% functionality in offline, firewalled, or CDN-delayed environments.

### 9.4 Standardized Loading Alerts & Async Export Event Pipeline
For operations with server-side computation latency (e.g. **Multi-Sheet Excel Exports**, **PDF Document Compilation**, **Bulk Database Batch Queries**), static timeouts must **NEVER** be used because they cause desynchronization between the UI popup and browser file download stream.

Always implement the **Asynchronous Fetch-to-Blob Pipeline**:
1. **Interactive Modal:** Opens SweetAlert2 with `.ecc-loading-popup` and stays persistently active while fetching bytes in the background.
2. **Tab Stability:** The browser tab does NOT enter a perpetual busy/stuck state.
3. **Blob Object URL:** Converts the completed response stream to a Blob (`window.URL.createObjectURL(blob)`) and triggers native download instantly.
4. **Synchronous Transition:** Modal transitions to a green success confirmation (`Unduhan Berhasil!`) precisely when the file is handed over to the user's filesystem.

```javascript
/**
 * ECC Standardized Asynchronous Download & Loading Alert Trigger
 * @param {HTMLElement} btnEl - The clicked button/link element
 * @param {string} typeText - E.g. 'Berkas Excel (.xlsx)' or 'Laporan PDF Resmi'
 * @param {string} defaultFilename - E.g. 'Rekap_Kinerja_ECC.xlsx'
 */
async function triggerEccAsyncDownload(btnEl, typeText, defaultFilename) {
    const url = btnEl.getAttribute('href');
    if (!url) return;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: `Menyiapkan ${typeText}...`,
            html: `
                <div class="d-flex flex-column align-items-center gap-2 my-2">
                    <div class="ecc-loading-spinner-wrapper">
                        <div class="ecc-loading-spinner"></div>
                    </div>
                    <div class="ecc-loading-title">Sedang mengompilasi data instansi...</div>
                    <span class="ecc-loading-desc">Sistem sedang merekap data target, capaian realisasi, dan tugas tambahan. File akan langsung terunduh begitu proses selesai.</span>
                    <span class="ecc-loading-badge-step"><i class="bi bi-shield-check text-primary"></i> Streaming terenkripsi & aman</span>
                </div>
            `,
            customClass: {
                popup: 'ecc-loading-popup'
            },
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: async () => {
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        throw new Error('Gagal menyiapkan berkas dari server');
                    }

                    let filename = defaultFilename;
                    const disposition = response.headers.get('Content-Disposition');
                    if (disposition && disposition.includes('filename=')) {
                        const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                        if (match && match[1]) {
                            filename = match[1].replace(/['"]/g, '').trim();
                        }
                    }

                    const blob = await response.blob();
                    const blobUrl = window.URL.createObjectURL(blob);
                    const downloadAnchor = document.createElement('a');
                    downloadAnchor.style.display = 'none';
                    downloadAnchor.href = blobUrl;
                    downloadAnchor.download = filename;
                    document.body.appendChild(downloadAnchor);
                    downloadAnchor.click();

                    setTimeout(() => {
                        window.URL.revokeObjectURL(blobUrl);
                        if (document.body.contains(downloadAnchor)) {
                            document.body.removeChild(downloadAnchor);
                        }
                    }, 2000);

                    Swal.fire({
                        icon: 'success',
                        title: 'Unduhan Berhasil!',
                        text: `Berkas ${filename} berhasil disiapkan dan diunduh.`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengunduh',
                        text: 'Terjadi kendala saat mengompilasi berkas. Silakan coba beberapa saat lagi.'
                    });
                }
            }
        });
    } else {
        window.location.href = url;
    }
}
```

---

## 10. 🎬 Motion Design & Micro-Animation Tokens (/impeccable animate)

The Evidence Command Center (ECC) uses purpose-driven motion design to make data-dense interfaces feel tactile, responsive, and alive without causing cognitive fatigue.

### 10.1 Motion Token Catalog

| Motion Token / Class | Trigger / Context | Timing & Easing | Visual Transformation & Purpose |
| :--- | :--- | :--- | :--- |
| **Bento Entrance** (`.bento-stagger-1..4`) | Initial page render | $500\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)` | Staggered upward reveal ($16\text{px} \rightarrow 0$) conveying structural hierarchy. |
| **Row Insertion** (`.row-slide-in`) | Add row click (Target/Log) | $350\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)` | Smooth slide down ($-10\text{px}$) with temporary soft blue glow to highlight newly added row. |
| **Row Removal** (`.row-slide-out`) | Delete row click | $220\text{ms}$ `ease-out` | Graceful scale decay ($1.0 \rightarrow 0.96$) and slide right ($+12\text{px}$) preventing abrupt DOM collapse. |
| **Badge Satuan Pop** (`.badge-satuan-pop`) | Target selection change | $280\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)` | Elastic scale pop ($0.85 \rightarrow 1.1 \rightarrow 1.0$) confirming dynamic unit synchronization. |
| **Duplicate Pulse** (`.table-danger`) | Form validation error | $400\text{ms}$ `ease-in-out` | Subtle breathing pulse ($5\% \rightarrow 25\% \rightarrow 12\%$) drawing immediate focus to duplicate rows. |
| **Tactile Button Press** (`.btn-tactile`) | Mouse click / Tap | $150\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)` | Physical depression feel (`transform: scale(0.97)`) providing immediate haptic satisfaction. |
| **Card Hover Lift** (`.bento-card:hover`) | Cursor hover | $250\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)` | Elevation lift (`translateY(-2.5px)`) and soft deep shadow expansion. |
| **Modal Pop-In** (`.modal.fade .modal-dialog`) | Dialog open | $250\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)` | Scale pop ($0.96 \rightarrow 1.0$) accompanied by $4\text{px}$ backdrop glassmorphic blur. |
| **Chart-to-Table Glow** (`.table-active-pulse`)| Chart segment click | $1800\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)` | Soft blue ambient illumination directing eye to filtered table row after auto-scroll. |

---

### 10.2 Strict Accessibility Standard: Reduced Motion Enforcement

In strict compliance with **WCAG 2.1 Criterion 2.3.3 (Animation from Interactions)**, all CSS keyframes, continuous scaling, and position transitions are automatically neutralized whenever the user enables "Reduce Motion" in their operating system:

```css
@media (prefers-reduced-motion: reduce) {
    .bento-card,
    .card.shadow-sm,
    .bento-stagger,
    .tab-content > .tab-pane,
    .btn-tactile,
    .btn,
    .row-slide-in,
    .row-slide-out,
    .badge-satuan-pop,
    .table-danger,
    .table-danger-pulse,
    .modal.fade .modal-dialog,
    .table-active-pulse,
    .swal2-popup,
    .swal2-styled {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
}
```

---

*Evidence Command Center (ECC) Design System — Maintained for 100% visual consistency, ergonomic excellence, and enterprise-grade user experience.*






