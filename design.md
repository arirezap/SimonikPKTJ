# 🎨 DESIGN SYSTEM & UI/UX GUIDELINES
## Evidence Command Center (ECC)
**Official Application Name:** Evidence Command Center (ECC) (formerly Simonik)  
**Version:** 1.2.0 (Enterprise Production Release)  
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
<link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=1.2.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
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

*Evidence Command Center (ECC) Design System — Maintained for 100% visual consistency, ergonomic excellence, and enterprise-grade user experience.*
