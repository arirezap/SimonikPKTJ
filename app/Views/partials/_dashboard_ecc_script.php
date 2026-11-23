<?php
/**
 * Script bersama untuk Dashboard Admin & Staf (DRY):
 * - Konfigurasi global Chart.js (Font Inter + prefers-reduced-motion)
 * - Helper escapeHtml, formatAngkaId, parseAngkaId, formatNamaTitleCase, animateValue, getPredikatScoreClass
 * - Inisialisasi Radar Chart ECC, persistensi tab prodi, dan pembaruan AJAX updateEccData()
 */
$eccFrom = $eccFrom ?? 'admin';
?>
<script src="<?= base_url('assets/vendor/chartjs/chart.umd.min.js') ?>"></script>
<script>
window.eccDash = (function () {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif";
        Chart.defaults.color = '#475569';
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatAngkaId(val, decimals = 2) {
        const num = Number(val);
        if (!Number.isFinite(num)) return decimals > 0 ? (0).toLocaleString('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) : '0';
        return num.toLocaleString('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function parseAngkaId(str) {
        if (str === null || str === undefined) return 0;
        const cleaned = String(str).replace(/[^0-9,.-]/g, '').replace(/\./g, '').replace(',', '.');
        const num = parseFloat(cleaned);
        return Number.isFinite(num) ? num : 0;
    }

    function formatNamaTitleCase(raw) {
        if (!raw) return '-';
        const str = String(raw).trim();
        if (!str) return '-';
        const parts = str.split(',');
        const main = parts[0].trim().toLowerCase().replace(/\b([a-z])/g, m => m.toUpperCase());
        if (parts.length > 1) {
            return main + ', ' + parts.slice(1).map(p => p.trim()).join(', ');
        }
        return main;
    }

    function animateValue(elementId, start, end, duration = 650, suffix = '', decimals = 0) {
        const obj = document.getElementById(elementId);
        if (!obj) return;
        const safeStart = Number.isFinite(Number(start)) ? Number(start) : 0;
        const safeEnd = Number.isFinite(Number(end)) ? Number(end) : 0;

        if (prefersReducedMotion || duration <= 0) {
            obj.innerHTML = formatAngkaId(safeEnd, decimals) + suffix;
            return;
        }

        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const easeProgress = 1 - Math.pow(2, -10 * progress);
            const current = safeStart + (safeEnd - safeStart) * easeProgress;

            obj.innerHTML = formatAngkaId(current, decimals) + suffix;

            if (progress < 1) {
                window.requestAnimationFrame(step);
            } else {
                obj.innerHTML = formatAngkaId(safeEnd, decimals) + suffix;
            }
        };
        window.requestAnimationFrame(step);
    }

    function getPredikatScoreClass(rata, dinilai = 1) {
        if (!dinilai || Number(rata) === 0) return 'dash-score-neutral';
        const val = Number(rata);
        if (val > 100) return 'dash-score-success';
        if (val > 90) return 'dash-score-primary';
        if (val > 75) return 'dash-score-info';
        if (val > 25) return 'dash-score-warning';
        return 'dash-score-danger';
    }

    function getStatusPegawaiBadge(status) {
        if (status === 'mutasi_keluar') {
            return ' <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Mutasi</span>';
        }
        if (status === 'pensiun') {
            return ' <span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">Pensiun</span>';
        }
        return '';
    }

    function splitLabel(label, maxLength = 16) {
        const text = String(label || '');
        if (text.length <= maxLength) return text;
        const words = text.split(' ');
        const lines = [];
        let currentLine = words[0] || '';
        for (let i = 1; i < words.length; i++) {
            if (currentLine.length + 1 + words[i].length <= maxLength) {
                currentLine += ' ' + words[i];
            } else {
                lines.push(currentLine);
                currentLine = words[i];
            }
        }
        lines.push(currentLine);
        return lines;
    }

    function getClickedRadarLabel(clickEvent, chart) {
        if (!chart || !chart.scales || !chart.scales.r) return null;
        const pointLabelItems = chart.scales.r._pointLabelItems;
        if (!pointLabelItems || pointLabelItems.length === 0) return null;
        const pos = Chart.helpers.getRelativePosition(clickEvent, chart);
        let closestIndex = -1;
        let minDistance = Infinity;
        for (let i = 0; i < pointLabelItems.length; i++) {
            const item = pointLabelItems[i];
            const dist = Math.hypot(pos.x - item.x, pos.y - item.y);
            if (dist < minDistance) {
                minDistance = dist;
                closestIndex = i;
            }
        }
        if (closestIndex > -1) {
            const item = pointLabelItems[closestIndex];
            const width = item.options?.bounds?.width || 80;
            if (minDistance < (width / 2) + 20) return closestIndex;
        }
        return null;
    }

    function chartAnimationOption() {
        return prefersReducedMotion ? false : { duration: 650, easing: 'easeOutQuart' };
    }

    function renderStandarLinks(prodiId, labels, labelIds, tahun, eccFrom) {
        const wrap = document.getElementById('eccLinks-' + prodiId);
        if (!wrap) return;
        const prodiSlug = String(prodiId || '').toLowerCase();
        let html = '';
        (labels || []).forEach((lbl, idx) => {
            const lid = (labelIds || [])[idx];
            if (!lid) return;
            const href = `<?= site_url('ecc/detailStandar') ?>/${encodeURIComponent(lid)}/${encodeURIComponent(prodiSlug)}/${encodeURIComponent(tahun)}?from=${encodeURIComponent(eccFrom)}`;
            html += `<a class="dash-ecc-standar-link" href="${href}">${escapeHtml(lbl)}</a>`;
        });
        wrap.innerHTML = html;
    }

    let _registry = null;
    let _eccFrom = '<?= esc($eccFrom, 'js') ?>';

    function initEccRadar(prodiData, selectedTahun, registry) {
        _registry = registry;

        // Persistensi Tab Prodi Aktif
        const activeTabKey = 'ecc_active_prodi_tab';
        const savedTabTarget = localStorage.getItem(activeTabKey);
        if (savedTabTarget) {
            const targetBtn = document.querySelector(`button[data-bs-target="${savedTabTarget}"]`);
            if (targetBtn && typeof bootstrap !== 'undefined') {
                const tabObj = bootstrap.Tab.getInstance(targetBtn) || new bootstrap.Tab(targetBtn);
                tabObj.show();
            }
        }
        document.querySelectorAll('#prodiTab button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', event => {
                const target = event.target.getAttribute('data-bs-target');
                if (target) localStorage.setItem(activeTabKey, target);
            });
        });

        if (typeof Chart === 'undefined') return;

        for (const [id, data] of Object.entries(prodiData || {})) {
            const canvasId = 'radarChart-' + id;
            const ctx = document.getElementById(canvasId);
            if (!ctx) continue;

            const existing = Chart.getChart(ctx);
            if (existing) existing.destroy();

            const wrappedLabels = (data.chart_labels || []).map(lbl => splitLabel(lbl, 16));
            registry[canvasId] = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: wrappedLabels,
                    labelIds: data.chart_label_ids || [],
                    prodi: data.id_prodi,
                    tahun: selectedTahun,
                    datasets: [{
                        label: 'Skor ' + data.nama_prodi,
                        data: data.chart_data || [],
                        fill: true,
                        backgroundColor: 'rgba(30, 64, 175, 0.14)',
                        borderColor: '#1e40af',
                        pointBackgroundColor: '#1e40af',
                        pointBorderColor: '#ffffff',
                        pointHoverBackgroundColor: '#ffffff',
                        pointHoverBorderColor: '#1e40af',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: chartAnimationOption(),
                    layout: { padding: 16 },
                    scales: {
                        r: {
                            angleLines: { display: true, color: '#e2e8f0', lineWidth: 1 },
                            min: 0,
                            max: 100,
                            grid: { color: '#e2e8f0', lineWidth: 1 },
                            pointLabels: {
                                display: true,
                                color: '#0f172a',
                                font: { size: 12, weight: '600', lineHeight: 1.25 },
                                padding: 8
                            },
                            ticks: { display: false, stepSize: 25 }
                        }
                    },
                    plugins: { legend: { display: false } },
                    onHover: (event, activeElements, chart) => {
                        const idx = getClickedRadarLabel(event, chart);
                        if (event.native && event.native.target) {
                            event.native.target.style.cursor = (idx !== null) ? 'pointer' : 'default';
                        }
                    },
                    onClick: (e, elements, chart) => {
                        const idx = getClickedRadarLabel(e, chart);
                        if (idx !== null && chart.config.data.labelIds && chart.config.data.labelIds[idx]) {
                            const labelId = chart.config.data.labelIds[idx];
                            const prodi = String(chart.config.data.prodi || '').toLowerCase();
                            const tahun = chart.config.data.tahun;
                            window.location.href = `<?= site_url('ecc/detailStandar') ?>/${encodeURIComponent(labelId)}/${encodeURIComponent(prodi)}/${encodeURIComponent(tahun)}?from=${encodeURIComponent(_eccFrom)}`;
                        }
                    }
                }
            });
        }
    }

    async function updateEccData() {
        const tahunSelect = document.getElementById('tahun_ecc');
        if (!tahunSelect) return;
        const tahunEcc = tahunSelect.value;
        try {
            const response = await fetch(`<?= site_url('dashboard') ?>?ajax_type=ecc&tahun_ecc=${encodeURIComponent(tahunEcc)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            if (data && data.prodiData && _registry) {
                for (const [id, prodi] of Object.entries(data.prodiData)) {
                    const canvasId = 'radarChart-' + id;
                    if (_registry[canvasId]) {
                        const chart = _registry[canvasId];
                        chart.data.labels = (prodi.chart_labels || []).map(lbl => splitLabel(lbl, 16));
                        chart.data.labelIds = prodi.chart_label_ids || [];
                        chart.data.tahun = tahunEcc;
                        chart.data.datasets[0].data = prodi.chart_data || [];
                        chart.update();
                    }
                    renderStandarLinks(id, prodi.chart_labels || [], prodi.chart_label_ids || [], tahunEcc, _eccFrom);
                }
            }
        } catch (err) {
            console.error('Gagal memuat data ECC:', err);
        }
    }

    return {
        prefersReducedMotion,
        escapeHtml,
        formatAngkaId,
        parseAngkaId,
        formatNamaTitleCase,
        animateValue,
        getPredikatScoreClass,
        getStatusPegawaiBadge,
        chartAnimationOption,
        initEccRadar,
        updateEccData
    };
})();

window.updateEccData = window.eccDash.updateEccData;
</script>
