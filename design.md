# 🎨 DESIGN SYSTEM & UI/UX GUIDELINES
## Evidence Command Center (ECC)
**Official Application Name:** Evidence Command Center (ECC) (formerly Simonik)  
**Design Philosophy:** Modern Enterprise Bento Grid, Visual Excellence, Micro-Interactions, Mobile-First Touch Experience, and High-Performance Ergonomics.

---

## 1. 🌟 Core Design Philosophy & Principles

The Evidence Command Center (ECC) UI/UX is built to deliver a **world-class, high-density enterprise dashboard** that balances executive readability with rapid data entry for operational staff.

### Key Pillars:
1. **Bento Box Architecture**: Information is organized into clean, rounded, elevated surface containers (`.bento-card`) rather than rigid bordered boxes.
2. **Visual Hierarchy & High Contrast**: Clear typography weights and soft contextual tints (`-subtle` badges and slate zebra striping) guide the eye to actionable items instantly.
3. **No Placeholders & No Unpolished Elements**: Pure CSS spinners, dynamic initials fallback for avatars, and standardized icon sets.
4. **Mobile First & Touch Friendly**: Dual-view paradigm where wide desktop tables seamlessly collapse into touch cards on smartphones (<768px), maintaining minimum 44px touch targets.
5. **Corporate Tone & Strict Terminology**: Strictly use **"staf"** across all labels, buttons, and variables (never use "staf").

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
| **Success (Baik)** | `#198754` | `#dcfce7` (`bg-success-subtle`) | `#bbf7d0` | Approved targets, score $\ge 90$, submitted reports, CREATE action |
| **Info (Standar)** | `#0284c7` | `#e0f2fe` (`bg-info-subtle`) | `#bae6fd` | Standard score ($75 - 89.9$), LOGIN/LOGOUT logs, Cuti Bersama |
| **Warning (Perhatian)** | `#d97706` | `#fef3c7` (`bg-warning-subtle`) | `#fde68a` | Needs attention score ($60 - 74.9$), pending approval, UPDATE action |
| **Danger (Kurang)** | `#dc2626` | `#fee2e2` (`bg-danger-subtle`) | `#fecaca` | Score $< 60$, rejected status, delete actions, national holidays |
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

### 4.2 Clean White Sidebar & Smart Topbar
- **Sidebar**: High-contrast white background (`bg-white border-end`), active item highlighted with soft blue pill, and collapsible multi-level navigation.
- **Smart Topbar (`.header-promax`)**:
  - Automatically hides on downward scroll (`> 60px`) to maximize vertical data viewport on laptops and tablets.
  - Smoothly slides down when user scrolls upward.
  - Contains User Initial Avatar, Notification Dropdown with dynamic CSRF handling, and role badge.

### 4.3 Form Inputs & Number Controls
- **Standard Inputs**: Borderless light inputs (`bg-light border-0 rounded-3`) with active focus ring `box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15)`.
- **Score & Metric Inputs (`.col-nilai`)**:
  - Fixed horizontal space: `min-width: 175px !important; width: 175px !important;`.
  - Number spinner arrows suppressed for clean, non-cluttered numeric entry:
    ```css
    .input-nilai-capaian::-webkit-outer-spin-button,
    .input-nilai-capaian::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    ```

### 4.4 Date Grouping Zebra Striping
In Activity Log & Performance Evaluation tables:
- **Odd Date Blocks (`.date-group-odd`)**: High-contrast soft slate shading (`background-color: #f1f5f9 !important; --bs-table-bg: #f1f5f9 !important;`).
- **Even Date Blocks (`.date-group-even`)**: Pure crisp white (`background-color: #ffffff !important; --bs-table-bg: #ffffff !important;`).
- Eliminates visual noise by relying purely on cell background differentiation without heavy divider borders.

### 4.5 Performance Rating & Pill Badges
Standardized performance badges across Rekap Kepegawaian, Penilaian Kinerja, and Dashboard:
- `Score >= 90`: **Baik** (`bg-success-subtle text-success border border-success-subtle rounded-pill`)
- `Score 75 - 89.9`: **Baik** (`bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill`)
- `Score 60 - 74.9`: **Butuh Perhatian** (`bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill`)
- `Score < 60`: **Sangat Kurang** (`bg-danger-subtle text-danger border border-danger-subtle rounded-pill`)
- `Score 0 / Unrated`: **Belum Dinilai** (`bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill`)

---

## 5. 📱 Mobile & Responsive Experience Guidelines

### 5.1 Dual-View Presentation Pattern
For dense tabular data (Penilaian Kinerja, Rekap Kepegawaian, Activity Log):
- **Desktop ($\ge 768\text{px}$)**: Full desktop table with sticky header (`.desktop-table-view`).
- **Mobile ($< 768\text{px}$)**: Collapsible Touch Card List (`.mobile-cards-view`) presenting name, NIP, avatar, target count, and evaluation scores in stacked touch-friendly cards.

### 5.2 Mobile Touch Targets
- All action buttons and tabs must maintain a minimum touch target size of **$44\text{px} \times 44\text{px}$**.
- Horizontal tab strips on mobile use pill containers (`#penilaianTabs.nav-tabs`) with equal flex distribution (`flex: 1`) and touch scrolling.

### 5.3 Document & A4 Print Scaling
- Printable documents (Kontrak Kinerja & Pakta Integritas) render at strict $210\text{mm}$ (A4) width for desktop PDF export.
- On screens $< 768\text{px}$, responsive scaling enforces `.paper-container { width: 100% !important; font-size: 10pt !important; }` to eliminate horizontal scroll overflow.

---

## 6. ⚡ Performance & Asset Delivery (v1.1 Standards)

### 6.1 Automatic Cache Busting
To ensure client browsers immediately receive styling updates without requiring manual cache clearing:
```html
<link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=1.1.' . filemtime(FCPATH . 'assets/css/style.css')) ?>">
```
Coupled with HTTP no-cache headers in `main.php`:
```html
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
```

### 6.2 Asynchronous Micro-Interactions
- **AJAX Dynamic CSRF**: All interactive modals and AJAX endpoints update the meta tag `X-CSRF-TOKEN` on every JSON response.
- **SweetAlert2 with Fallback**: All destructive actions (delete user, delete log, cancel approval) use SweetAlert2 with a native `window.confirm()` fallback for offline or CDN-delayed environments.

---

*Evidence Command Center (ECC) Design System — Maintained for 100% visual consistency and enterprise-grade user experience.*
