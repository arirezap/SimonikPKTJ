# 🎨 DESIGN SYSTEM & UI/UX GUIDELINES
## Evidence Command Center (ECC)
**Official Application Name:** Evidence Command Center (ECC) (formerly Simonik)  
**Version:** 1.4.0 (Enterprise Production Release - 1 September 2026)  
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
7. **Simplicity & Clear Microcopy**: Gunakan kalimat yang ringkas, simpel, padat, tidak berbelit-belit, dan hindari bahasa teknis yang kepanjangan baik pada UI labels maupun dokumentasi.

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
| **Sangat Baik (> 100% - 150%)** | `#10b981` | `#dcfce7` (`bg-success-subtle`) | `#bbf7d0` | Exceeding targets, outstanding performance |
| **Baik (>= 90% - 100%)** | `#2563eb` | `#dbeafe` (`bg-primary-subtle`) | `#bfdbfe` | Target fully achieved according to standard plan |
| **Butuh Perbaikan (> 75% - < 90%)** | `#0284c7` | `#e0f2fe` (`bg-info-subtle`) | `#bae6fd` | Standard acceptable performance, needs minor improvement |
| **Kurang (> 25% - 75%)** | `#d97706` | `#fef3c7` (`bg-warning-subtle`) | `#fde68a` | Suboptimal achievement, needs supervisory guidance |
| **Sangat Kurang (<= 25%)** | `#dc2626` | `#fee2e2` (`bg-danger-subtle`) | `#fecaca` | Critical low performance, rejected target |
| **Belum Dinilai (0% / Null)** | `#64748b` | `#f1f5f9` (`bg-secondary-subtle`) | `#e2e8f0` | Unrated target, draft state, reset evaluation state |

### 2.3 Surface & Canvas Tokens
- **App Background**: `#f8fafc` (Cool Slate Canvas)
- **Surface / Card Background**: `#ffffff` (Pure White)
- **Subtle Surface Tint**: `#f1f5f9` (Used for `.date-group-odd` zebra striping and off-canvas panels)
- **Card Border**: `1px solid rgba(0, 0, 0, 0.05)`
- **Divider Line**: `1px solid #e2e8f0`

### 2.4 📐 8-Point Grid System & Spacing Scale Tokens
To maintain absolute mathematical harmony, visual rhythm, and cognitive scannability across all enterprise modules, ECC strictly adheres to the **8-Point Grid System** (with a `4px` half-step for micro tags and badges). Arbitrary non-grid numbers (e.g. `7px`, `13px`, `62px`, `95px`) are strictly forbidden.

| Spacing Token | CSS Value | Rem Eq. | Multiplier | Standard Application in ECC |
| :--- | :--- | :--- | :--- | :--- |
| `--ecc-space-0-5` | `4px` | `0.25rem` | `0.5x` | Micro gaps, status dot offsets, mobile grid gap (`4px`), tag inner padding |
| `--ecc-space-1` | `8px` | `0.5rem` | `1.0x` (Base) | Element gap, calendar day cell padding, calendar grid gap (`8px`), header cell bottom padding |
| `--ecc-space-1-5` | `12px` | `0.75rem` | `1.5x` | Table header/cell vertical padding, guidance banner vertical padding, modal title gap |
| `--ecc-space-2` | `16px` | `1.0rem` | `2.0x` | Bento card mobile padding, filter toolbar padding, table horizontal padding, legend gaps |
| `--ecc-space-3` | `24px` | `1.5rem` | `3.0x` | Bento card desktop padding (`p-3 p-md-4`), modal body padding, executive score card padding |
| `--ecc-space-4` | `32px` | `2.0rem` | `4.0x` | Section header top margin (`mt-4`), page content bottom margin, legend capsule height (`32px`) |
| `--ecc-space-5` | `40px` | `2.5rem` | `5.0x` | Primary form action button height, standard avatar box size, modal close button height |
| `--ecc-space-6` | `48px` | `3.0rem` | `6.0x` | Mobile calendar day cell min-height, feature icon box size, primary FAB touch height |
| `--ecc-space-8` | `64px` | `4.0rem` | `8.0x` | Desktop calendar day cell min-height, medium profile avatar container |
| `--ecc-space-10` | `80px` | `5.0rem` | `10.0x` | Large profile photo avatar container |

### 2.5 📦 Asset, Icon Box & Control Sizing Architecture
All graphical assets, icon containers, buttons, and interactive affordances must follow standardized box dimensions:

| Asset / Component Type | Dimensions | Border Radius | Padding / Specs | Usage in ECC |
| :--- | :--- | :--- | :--- | :--- |
| **Micro Status Dot** | `8px × 8px` | `50% (Circle)` | N/A | Online status, unread notification indicator |
| **Heatmap Legend Swatch** | `16px × 16px` | `4px` (`rounded-1`) | Border `1px solid` | Activity intensity swatches on calendar footer |
| **Heatmap Legend Bento Capsule** | Height `32px` | `50rem` (`pill`) | `4px 12px; gap: 10px;` | Cohesive background pill container for 5-tier swatches |
| **Interactive Callout Hint** | Height `32px` | `50rem` (`pill`) | `4px 14px; gap: 8px;` | "Klik tanggal pada kalender untuk melihat rincian" |
| **Compact Action Button** | Height `32px` | `50rem` (`pill`) | `4px 12px` | In-table Bukti links, Revisi triggers, compact filters |
| **Form Control / Select2 Dropdown** | Height `36px`–`40px` | `8px` (`rounded-3`) | `8px 12px` | Bulan/Tahun selects, Select2 Staf, number inputs |
| **Standard CTA Button** | Height `40px` | `50rem` (`pill`) | `8px 24px` | Simpan Draf, Simpan & Terbitkan, Reset Nilai |
| **Modal Header Icon Box** | `40px × 40px` | `12px` (`rounded-3`) | Center flex | Header icons on detail modals (e.g. Calendar detail) |
| **Modal Date Navigation Buttons** | `32px × 32px` | `8px` (`rounded-2`) | Center flex | Fast date navigation (`<` `>`) on detail modal |
| **Feature Feature Icon** | `48px × 48px` | `16px` (`rounded-4`) | Center flex | KPI feature icons, Dashboard summary category icons |
| **Profile Photo Avatar (SM)** | `40px × 40px` | `50% (Circle)` | Object-fit cover | Topbar profile menu, table inline user avatar |
| **Profile Photo Avatar (MD)** | `64px × 64px` | `50% (Circle)` | Object-fit cover | Team management card, evaluation subordinate header |
| **Profile Photo Avatar (LG)** | `80px × 80px` | `50% (Circle)` | Object-fit cover | Profile page focal avatar with floating camera badge |
| **Calendar Day Cell (Desktop)**| Min-Height `64px` | `8px` (`rounded-2`) | `8px 10px; gap: 8px;` | 7-column calendar activity heatmap matrix |
| **Calendar Day Cell (Mobile)** | Min-Height `48px` | `6px` (`rounded-2`) | `4px 6px; gap: 4px;` | Mobile calendar matrix on viewports $<576\text{px}$ |
| **Sidebar Container (Desktop)**| Width `256px` | Border `1px solid` | `16px 8px 32px 8px` | Enterprise primary navigation sidebar |
| **Sidebar Mini (Collapsed)**  | Width `72px` | Border `1px solid` | `16px 8px 24px 8px` | Compact icon-first sidebar mode |
| **Sidebar Nav Link Item**      | Min-Height `40px` | `8px` (`rounded-3`) | `8px 12px` | Touch-friendly primary navigation link |
| **Sidebar Sub-Link Item**     | Min-Height `32px` | `6px` (`rounded-2`) | `6px 12px 6px 20px` | Nested collapsible submenu link |

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
- **Activity Calendar Heatmap Matrix (100% Emoji-Free 8-Point Grid)**:
  - **Desktop Day Cells**: `min-height: 64px;` ($8 \times 8\text{px}$), `padding: 8px 10px; border-radius: 8px;` grid `gap: 8px;`.
  - **Mobile Day Cells**: `min-height: 48px;` ($6 \times 8\text{px}$), `padding: 4px 6px; border-radius: 6px;` grid `gap: 4px;`.
  - **5-Tier Intensity Levels**: Level 0 (`#ffffff`), Level 1 (`#f0fdf4`), Level 2 (`#dcfce7`), Level 3 (`#22c55e`), Level 4 (`#15803d`).
  - **Bento Capsule Legend Bar**: `height: 32px; padding: 4px 12px; border-radius: 50rem; gap: 10px; background: #f8fafc; border: 1px solid #e2e8f0;`, Swatches `16px × 16px` (`border-radius: 4px`).
  - **Interactive Hint Callout**: `height: 32px; padding: 4px 14px; border-radius: 50rem; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 0.75rem; font-weight: 600;`.
- **Pop-Up Modal Rincian Pekerjaan (`#modalDetailLogTanggal`)**:
  - **Header Icon Box**: `40px × 40px`, `border-radius: 12px`, background `#0d6efd`, text white.
  - **Date Navigation Buttons (`<` `>`)**: `width: 32px; height: 32px; border-radius: 8px;` with active tactile press response.
  - **Clean Date Info Banner**: Pure minimal layout without redundant labels *"Tanggal Terpilih"* or *"Hari Reguler"*. Contextual pill badges only appear for National Holidays & Weekends.
  - **Bento Table Container**: `padding: 12px 16px; max-height: 440px;` ($55 \times 8\text{px}$) with smooth custom scrollbar.
  - **Action Footer Buttons**: Pill buttons (`height: 36px`).

### 7.5 Global Shell, Topbar, Footer & Notification Motion Tokens
- **Compact Single-Line Navigation**: `--sidebar-width: 256px;` ($32 \times 8\text{px}$), `--sidebar-mini-width: 72px;` ($9 \times 8\text{px}$), `.sidebar .nav-link` font size `0.8125rem (13px)` with `white-space: nowrap;` and `text-overflow: ellipsis;` ensuring all navigation items (e.g. *"Rekap & Penilaian Kinerja"*, *"Target Kinerja Bulanan"*) strictly sit on **1 single line** in both normal and active states.
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

### 7.6 Kepegawaian Monitoring & Remuneration View Standards (`Monitoring Target` & `Monitoring Penilaian`)
- **Visual Uniformity**: Identical Bento architecture across `monitoring_target.php` and `rekap_kinerja.php`:
  - **Filter Card & KPI Card**: Symmetrical $6 / 6$ grid column split (`col-lg-6 col-xl-6`).
  - **Filter Controls**: `height: 36px`, `border-radius: 8px`, `border-color: #cbd5e1`.
  - **Quick Filter Pills**: `#pillFilterGroup` with `height: 28px`, `padding: 0 12px`, `border-radius: 16px`, `gap: 8px`.
  - **Unit Kerja Column**: Standardized badge pill `<span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 text-wrap text-start" style="font-size: 0.74rem;">` across all desktop tables and mobile cards.
  - **Table Bento Cells**: `thead th` & `tbody td` with `padding: 12px 16px;`.
  - **Mobile Touch Cards**: `.mobile-pegawai-card` with `padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0;`.
- **Cascading Bento Stagger**: Header (`.bento-stagger-1`), KPI & Filter Grid (`.bento-stagger-2`), and Main Employee Table Card (`.bento-stagger-3`) entrance in $500\text{ms}$ `cubic-bezier(0.16, 1, 0.3, 1)`.
- **KPI Summary & Modal Count-Up Tickers**: `animateValue()` using `easeOutExpo` ($700\text{ms} - 850\text{ms}$) on `#statTotalPegawai`, `#statSudahDinilai`, `#statBelumDinilai`, `#statRataRataDinilai`, `#statRataRataTotal`, and modal `#modalDetailScore`.
- **Pill Filter Tactile Tap**: `.filter-pill` with `scale(0.96)` tap response and `active` elevated chip state.
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

## 11. 📅 Ergonomic Datepicker, Toolbar Controls & Calendar Status Dots

To ensure fast and error-free daily logging across various display sizes:
- **Date Button Affordance (`.btn-flatpickr-ecc`)**:
  - Always uses crisp white background (`#ffffff`), solid border (`1px solid #cbd5e1`), and dark slate text (`#334155`) to clearly indicate that the element is an interactive button, not static text.
  - Height standardized to `35px` with font size `0.76rem` to prevent long Indonesian date strings (e.g., *"Senin, 31 Agustus 2026"*) from wrapping or truncating.
- **Flatpickr Datepicker Weekend & Holiday Visual Tokens**:
  - **Active / Arrived Dates (Today & Past - `dateStr <= todayStr`)**:
    - Renders in vibrant bold red (`#ef4444`, `font-weight: 700`, `opacity: 1`, `cursor: pointer`).
  - **Disabled / Future Dates (`.flatpickr-disabled` - `dateStr > todayStr`)**:
    - Renders in soft muted red (`#fca5a5`, `opacity: 0.35`, `font-weight: 400`, `cursor: not-allowed`, `transform: none`). Does not look like an active clickable button.
- **Calendar Status Dots**:
  - 🟢 **Terkirim (`.dot-terkirim` / `#10b981`)**: Indicates daily log has been finalized and submitted to supervisor.
  - 🟡 **Draf (`.dot-draft` / `#f59e0b`)**: Indicates report is saved as draft and requires submission.
  - 🔴 **Kosong / Belum Diisi (`.dot-empty` / `#ef4444`)**: Indicates working day with zero recorded activities.

---

## 12. 🛠️ Web-Based Maintenance Mode & System Settings UX Standards

- **Interactive Switch Card (`.setting-card.active-maintenance`)**:
  - Amber border accent (`2px solid #f59e0b`) and elevated shadow (`0 8px 24px rgba(245, 158, 11, 0.12)`) when maintenance mode is active.
  - Dynamic status badge: 🟢 `Sistem Aktif Normal` $\leftrightarrow$ 🟡 `Mode Pemeliharaan AKTIF`.
- **Top Bar Persistent Administrator Banner (`.maintenance-alert-banner`)**:
  - Renders a non-intrusive amber gradient alert (`#fef3c7` $\rightarrow$ `#fde68a`) with pulsating indicator dot across all Admin pages when maintenance is enabled.
- **Client Maintenance Page (`public/maintenance.html`)**:
  - Clean Bento card layout with automated 30-second countdown timer and instant refresh trigger.
  - Serves HTTP `503 Service Unavailable` with `Retry-After: 30` headers for optimal SEO and caching protection.

---

## 13. 🔢 High-Precision Decimal Inputs (`DECIMAL(10,4)`) & 0–150% Scale Standards

- **Tabular Font Feature (`.input-decimal-4`)**:
  - Enforces `font-variant-numeric: tabular-nums` and `font-feature-settings: "tnum"` so decimal digits remain perfectly vertically aligned in financial and performance tables.
  - Suppresses native browser up/down number spin buttons to prevent layout disruption and accidental mouse-drag alteration.
- **Mouse Wheel Protection**:
  - Prevents scrolling through numbers by unfocusing (`.blur()`) on mouse wheel event over number inputs.
- **Unified 0–150% Scoring Scale**:
  - Both Rencana Hasil Kerja (RHK) and Tugas Tambahan share the uniform score spectrum of `0% - 150%` with automated real-time badge recoloring and client-side bounding checks.

---

## 14. 🏢 13-Tier Organizational Hierarchy Badges & Skeleton Loaders

- **Hierarchical Tier Badges**:
  - **Tier 1 (Direktur)**: Deep Indigo (`#1e1b4b` / `#312e81`)
  - **Tier 2 (Wakil Direktur 1-3)**: Navy Blue (`#1e3a8a` / `#1d4ed8`)
  - **Tier 3 (Kabag / Katim / Kapus / Kanit)**: Deep Teal (`#0f766e` / `#14b8a6`)
  - **Tier 4 (Dosen / Tenaga Fungsional)**: Slate Gray (`#475569` / `#64748b`)
  - **Tier 5 (Staf Pelaksana)**: Neutral Subtle (`#f1f5f9` / `#cbd5e1`)
- **Async Skeleton Shimmer Loader (`.skeleton-shimmer`)**:
  - Displays a continuous $1.5\text{s}$ shimmer animation (`#f1f5f9` $\leftrightarrow$ `#e2e8f0`) during asynchronous AJAX modal queries, eliminating jarring layout shifts (*Cumulative Layout Shift = 0*).

---

## 15. 📊 Executive Multi-Sheet Excel & Landscape A4 PDF Document Engine Standards

- **Spreadsheet Multi-Sheet Hierarchy**:
  - **Sheet 1 (Ringkasan Eksekutif)**: Executive summary KPI metrics and 12-month performance matrix.
  - **Sheet 2 (Audit Trail Rincian)**: Detailed breakdown per employee, indicator, target vs realization, gaps, and evidence links.
- **Formal Print PDF Layout (Dompdf Landscape A4)**:
  - Official institution letterhead (Kop Surat Politeknik Keselamatan Transportasi Jalan).
  - Page-budgeted tables with repeated table headers on page breaks (`<thead>` repeat).
  - High-contrast typography and digital signature validation blocks.

---

## 16. ♿ Accessibility Standards & Screen Reader Labels (`aria-label`)

In compliance with modern enterprise web accessibility:
- **Export Action Buttons**:
  - Excel: `aria-label="Unduh Rekapitulasi Kinerja Excel Multi-Sheet Lengkap"`
  - PDF: `aria-label="Unduh Rekapitulasi Kinerja PDF Resmi A4 Landscape"`
- **Filter Toolbar Selects**:
  - Periode Bulan: `aria-label="Pilih Periode Bulan Rekap"`
  - Tahun Input: `aria-label="Input Tahun Rekap"`
  - Unit Kerja: `aria-label="Pilih Filter Unit Kerja"`
  - Role Filter: `aria-label="Pilih Filter Kategori Role"`
- **Live Search Boxes**:
  - `aria-label="Pencarian cepat nama pegawai, NIP, jabatan, atau unit kerja"`
- **Color Contrast Ratio**:
  - All text elements maintain a minimum contrast ratio of $\ge 4.5:1$ against surface backgrounds for optimal readability.

---

## 17. 🛡️ Multi-Role Authorization UX & Dynamic Navigation States

- **Role-Aware Navigation Badges (`render_role_badge()`)**:
  - Consistent visual pills representing the system roles (`Superadmin`, `Direktur`, `Wakil Direktur`, `Kabag`, `Manajemen`, `SPM`, `Kepegawaian`, `Pegawai / Staf`, `Tugas Belajar`).
- **Restricted Kepegawaian Menu Tree**:
  - The **Kepegawaian** menu tree on the sidebar (Monitoring Target & Monitoring Penilaian Kinerja) is strictly restricted to: `direktur`, `wadir`, `kabag` (`kabag_aak`, `kabag_kuk`), `kepegawaian`, and `admin`.
- **Target Approval Prerequisite Guard**:
  - Supervisors cannot input performance scores until the subordinate's monthly targets are fully approved (`status_approval = 'disetujui'`). Inputs are rendered locked with an alert notification.
- **Direktur Auto-Approval UX**:
  - Targets created under the `direktur` role are automatically approved and retain self-revision capabilities at all times.
- **Reset Evaluation Mechanism**:
  - The "Reset Nilai" action prompts a SweetAlert2 confirmation dialog and immediately resets all scores to `NULL` (reverting status to "Belum Dinilai").

---

*Evidence Command Center (ECC) Design System — Maintained for 100% visual consistency, ergonomic excellence, and enterprise-grade user experience.*
