# Workspace Rules: Evidence Command Center (ECC)

## 1. Terminology & Branding Rules
- **Strictly use "staf"**: When referring to subordinates or team members in UI labels, documentation, and source code variables, always use **"staf"** (instead of "staf"). "Staf" is the official polished standard in this corporate environment.
  - UI labels: *"Penilaian Staf"*, *"Daftar Staf"*, *"Staf Saya"*.
  - Code variables: `$stafIdTerpilih`, `getStaf()`.
- **Strictly use "Evidence Command Center (ECC)"**: The official application name is **Evidence Command Center (ECC)** (or **ECC**). "Simonik" is the legacy application name (even though the root directory name is `SimonikPKTJ`). Always use **Evidence Command Center (ECC)** or **ECC** across all UI labels, page titles, headers, footers, system logs, documentation, and agent responses (never "Simonik").

---

## 2. Core Architecture & Routing Rules
- **Framework & Stack**: CodeIgniter 4 (PHP 8.1+), MySQL, Bootstrap 5.3, Chart.js, jQuery, SweetAlert2.
- **Explicit Route Registration (`app/Config/Routes.php`)**:
  - All private/authenticated routes **MUST** be explicitly enclosed in `['filter' => 'auth']` groups. Never leave sensitive routes unauthenticated outside filter groups.
  - All AJAX endpoints and state mutation actions (store, update, delete, approve, sync) **MUST** use `POST` (or verified `$routes->match(['get', 'post'], ...)`).
  - Never rely on legacy auto-routing for production endpoints.

---

## 3. Security & Data Integrity Standards
- **CSRF Protection**: All `<form>` elements must include `<?= csrf_field() ?>`. AJAX POST requests must send `<?= csrf_token() ?>` and receive updated `csrf_hash` in the JSON response.
- **XSS Sanitization**: Never output raw database strings into HTML views without escaping. Always wrap with `esc($var)`.
- **Password Security**: Always use `password_hash($password, PASSWORD_DEFAULT)` and avoid user enumeration by using generic error messages on authentication failure.
- **Audit Logging**: Use `log_audit()` helper from `app/Helpers/audit_helper.php` for all crucial data modifications (`CREATE`, `UPDATE`, `DELETE`, `LOGIN`, `LOGOUT`, `APPROVE`, `UNLOCK_LAPORAN`, `CANCEL_APPROVE_TARGET`).
- **Database Safety & Transactions**:
  - Wrap multi-step mutations in Database Transactions (`$db->transStart()` and `$db->transComplete()`) and `try...catch (\Exception $e)`.
  - Every new database table column must be declared in `$allowedFields` of the corresponding Model class.
  - Always sanitize decimal inputs with `str_replace(',', '.', trim((string)$val))` to support Indonesian comma notation.

---

## 4. UI/UX & Interaction Design Standards
- **ECC Design Tokens**:
  - Bento card elevation: `.card-bento-ecc` / `.card.border-0.shadow-sm.rounded-4`.
  - Accessible focus rings: `box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15)`.
  - Tactile feedback: `.btn-tactile` with active scale response `transform: scale(0.97)`.
  - Motion: Natural deceleration `cubic-bezier(0.16, 1, 0.3, 1)` with full `@media (prefers-reduced-motion: reduce)` support.
- **Mobile & Ergonomic Standards**:
  - Touch targets $\ge 44\text{px}$ for buttons and interactive controls.
  - Mobile Floating Action Bar for thumb-first form submissions on viewports $< 768\text{px}$.
  - Sticky overview sidebars on desktop viewports $\ge 992\text{px}$.
- **SweetAlert2 & JS Fallback**:
  - Always verify `typeof Swal !== 'undefined'` before invoking `Swal.fire()` and provide native browser dialog fallback (`confirm()`) so the UI functions seamlessly even if CDN assets fail to load.
