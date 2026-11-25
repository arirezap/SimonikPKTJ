/**
 * ECC Wilayah Cascading Dropdown Helper (Provinsi & Kabupaten/Kota Indonesia)
 * Sumber data: tabel lokal ref_wilayah (https://github.com/cahyadsn/wilayah - Kepmendagri 2025)
 * melalui endpoint internal GET /api/wilayah/all.
 *
 * Mendukung 2 mode terpadu (Select2 Bootstrap 5 Theme dengan fallback native <select>):
 * 1. Pasangan berjenjang dua arah Provinsi <-> Kabupaten/Kota (`.js-ecc-wilayah-prov` & `.js-ecc-wilayah-kab` + `data-wilayah-pair`)
 *    - Pilih Provinsi -> menyaring Kabupaten/Kota di provinsi tersebut.
 *    - Pilih Kabupaten/Kota lebih dulu -> otomatis memilih Provinsi pasangannya & mengisi `kode_wilayah` bila ada.
 * 2. Dropdown tunggal Kabupaten/Kota searchable dikelompokkan per Provinsi (`.js-ecc-wilayah-kab-only`)
 *    - Mendukung `data-allow-tags="1"` untuk field seperti Tempat Lahir (bisa pilih kota resmi atau ketik luar negeri).
 */
(function (window, document) {
    'use strict';

    if (window.EccWilayah) {
        return;
    }

    var CACHE_KEY = 'ecc_wilayah_lvl12_2025_v1';
    var PROV_ALIASES = {
        'dki jakarta': '31',
        'jakarta': '31',
        'di yogyakarta': '34',
        'diy': '34',
        'yogyakarta': '34',
        'bangka belitung': '19',
        'babel': '19',
        'kepri': '21',
        'ntb': '52',
        'ntt': '53',
        'papua pegunungan tengah': '95',
        'nad': '11',
        'nanggroe aceh darussalam': '11'
    };

    var dataset = null;
    var loadPromise = null;
    var pairRegistry = {};

    function getApiUrl() {
        if (window.ECC_WILAYAH_API_URL) {
            return window.ECC_WILAYAH_API_URL;
        }
        var sc = document.querySelector('script[data-wilayah-api]');
        if (sc) {
            return sc.getAttribute('data-wilayah-api');
        }
        return '/api/wilayah/all';
    }

    function normStr(v) {
        return String(v || '')
            .trim()
            .toLowerCase()
            .replace(/\s+/g, ' ');
    }

    function normKabName(v) {
        return normStr(v)
            .replace(/^kab\.\s*/i, 'kabupaten ')
            .replace(/^kab\s+/i, 'kabupaten ');
    }

    function hasSelect2() {
        return !!(window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.select2 === 'function');
    }

    function loadData() {
        if (dataset) {
            return Promise.resolve(dataset);
        }
        if (loadPromise) {
            return loadPromise;
        }

        try {
            var cached = window.sessionStorage ? window.sessionStorage.getItem(CACHE_KEY) : null;
            if (cached) {
                var parsed = JSON.parse(cached);
                if (parsed && Array.isArray(parsed.provinsi) && parsed.provinsi.length >= 30 && Array.isArray(parsed.kabupaten) && parsed.kabupaten.length >= 400) {
                    dataset = parsed;
                    return Promise.resolve(dataset);
                }
            }
        } catch (e) {}

        loadPromise = fetch(getApiUrl(), { headers: { 'Accept': 'application/json' } })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (json) {
                if (json && json.status && Array.isArray(json.provinsi) && Array.isArray(json.kabupaten)) {
                    dataset = {
                        provinsi: json.provinsi,
                        kabupaten: json.kabupaten
                    };
                    try {
                        if (window.sessionStorage) {
                            window.sessionStorage.setItem(CACHE_KEY, JSON.stringify(dataset));
                        }
                    } catch (e) {}
                    return dataset;
                }
                throw new Error('Invalid wilayah payload');
            })
            .catch(function () {
                dataset = { provinsi: [], kabupaten: [] };
                return dataset;
            });

        return loadPromise;
    }

    function findProvinsi(input) {
        if (!dataset || !input) return null;
        var raw = String(input).trim();
        if (!raw) return null;
        var low = normStr(raw);
        var aliasCode = PROV_ALIASES[low] || null;
        var stripped = low.replace(/^provinsi\s+/i, '');
        if (!aliasCode && stripped !== low) {
            aliasCode = PROV_ALIASES[stripped] || null;
        }

        for (var i = 0; i < dataset.provinsi.length; i++) {
            var p = dataset.provinsi[i];
            if (p.kode === raw || (aliasCode && p.kode === aliasCode)) {
                return p;
            }
            var pLow = normStr(p.nama);
            if (pLow === low || pLow === stripped) {
                return p;
            }
        }
        return null;
    }

    function findKabupaten(input, provKode) {
        if (!dataset || !input) return null;
        var raw = String(input).trim();
        if (!raw) return null;
        var nTarget = normKabName(raw);
        var fallbackMatch = null;

        for (var i = 0; i < dataset.kabupaten.length; i++) {
            var k = dataset.kabupaten[i];
            if (provKode && k.provinsi_kode !== provKode) {
                continue;
            }
            if (k.kode === raw) {
                return k;
            }
            var nCur = normKabName(k.nama);
            if (nCur === nTarget) {
                return k;
            }
            // Pencocokan longgar jika data lama hanya menulis "Tegal" / "Semarang"
            if (!fallbackMatch && (nCur === 'kota ' + nTarget || nCur === 'kabupaten ' + nTarget)) {
                fallbackMatch = k;
            }
        }
        return fallbackMatch;
    }

    function initSelect2On(el) {
        if (!hasSelect2() || !el) return;
        var $el = window.jQuery(el);
        if ($el.data('select2')) {
            $el.select2('destroy');
        }

        var placeholder = el.getAttribute('data-placeholder') || '-- Pilih --';
        var allowTags   = el.getAttribute('data-allow-tags') === '1';
        var parentSel   = el.getAttribute('data-dropdown-parent') || '';
        var $parent     = null;
        if (parentSel) {
            var pNode = document.querySelector(parentSel);
            if (pNode) $parent = window.jQuery(pNode);
        } else {
            var closestModal = el.closest ? el.closest('.modal') : null;
            if (closestModal) $parent = window.jQuery(closestModal);
        }

        var cfg = {
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholder,
            allowClear: el.getAttribute('data-allow-clear') !== '0',
            tags: allowTags,
            language: {
                noResults: function () { return 'Wilayah tidak ditemukan'; },
                searching: function () { return 'Mencari wilayah...'; }
            }
        };
        if ($parent && $parent.length) {
            cfg.dropdownParent = $parent;
        }
        $el.select2(cfg);
    }

    function refreshSelect2(el) {
        if (!hasSelect2() || !el) return;
        var $el = window.jQuery(el);
        if ($el.data('select2')) {
            $el.trigger('change.select2');
        }
    }

    function populateProvinsiSelect(selectEl, selectedVal) {
        if (!selectEl || !dataset) return null;
        var placeholder = selectEl.getAttribute('data-placeholder') || '-- Pilih Provinsi --';
        var match = findProvinsi(selectedVal);
        var valToSelect = match ? match.nama : String(selectedVal || '').trim();

        selectEl.innerHTML = '';
        var emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = placeholder;
        selectEl.appendChild(emptyOpt);

        var foundExact = false;
        dataset.provinsi.forEach(function (p) {
            var opt = document.createElement('option');
            opt.value = p.nama;
            opt.textContent = p.nama;
            opt.dataset.kode = p.kode;
            if (valToSelect && p.nama === valToSelect) {
                opt.selected = true;
                foundExact = true;
            }
            selectEl.appendChild(opt);
        });

        if (valToSelect && !foundExact) {
            var customOpt = document.createElement('option');
            customOpt.value = valToSelect;
            customOpt.textContent = valToSelect;
            customOpt.selected = true;
            selectEl.appendChild(customOpt);
        }

        return match;
    }

    function populateKabupatenSelect(selectEl, provKode, selectedVal) {
        if (!selectEl || !dataset) return null;
        var placeholder = selectEl.getAttribute('data-placeholder') || '-- Pilih Kabupaten / Kota --';
        var match = findKabupaten(selectedVal, provKode || null);
        var valToSelect = match ? match.nama : String(selectedVal || '').trim();

        selectEl.innerHTML = '';
        var emptyOpt = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = placeholder;
        selectEl.appendChild(emptyOpt);

        var foundExact = false;

        if (provKode) {
            dataset.kabupaten.forEach(function (k) {
                if (k.provinsi_kode !== provKode) return;
                var opt = document.createElement('option');
                opt.value = k.nama;
                opt.textContent = k.nama;
                opt.dataset.kode = k.kode;
                opt.dataset.provKode = k.provinsi_kode;
                opt.dataset.provNama = k.provinsi_nama;
                if (valToSelect && k.nama === valToSelect) {
                    opt.selected = true;
                    foundExact = true;
                }
                selectEl.appendChild(opt);
            });
        } else {
            // Kelompokkan 514 Kabupaten/Kota per Provinsi dengan <optgroup> agar cepat dicari
            var groups = {};
            dataset.provinsi.forEach(function (p) {
                var og = document.createElement('optgroup');
                og.label = p.nama;
                groups[p.kode] = og;
            });
            dataset.kabupaten.forEach(function (k) {
                var og = groups[k.provinsi_kode];
                if (!og) return;
                var opt = document.createElement('option');
                opt.value = k.nama;
                opt.textContent = k.nama;
                opt.dataset.kode = k.kode;
                opt.dataset.provKode = k.provinsi_kode;
                opt.dataset.provNama = k.provinsi_nama;
                if (valToSelect && k.nama === valToSelect) {
                    opt.selected = true;
                    foundExact = true;
                }
                og.appendChild(opt);
            });
            dataset.provinsi.forEach(function (p) {
                if (groups[p.kode] && groups[p.kode].children.length > 0) {
                    selectEl.appendChild(groups[p.kode]);
                }
            });
        }

        if (valToSelect && !foundExact) {
            var customOpt = document.createElement('option');
            customOpt.value = valToSelect;
            customOpt.textContent = valToSelect;
            customOpt.selected = true;
            selectEl.appendChild(customOpt);
        }

        return match;
    }

    function updateKodeTarget(kabEl, kodeVal) {
        if (!kabEl) return;
        var targetSel = kabEl.getAttribute('data-kode-target');
        if (!targetSel) return;
        var targetEl = document.querySelector(targetSel);
        if (targetEl) {
            targetEl.value = kodeVal || '';
        }
    }

    function bindPair(pairKey, provEl, kabEl) {
        var syncing = false;
        var initProvRaw = provEl.getAttribute('data-current-value') || provEl.value || '';
        var initKabRaw  = kabEl.getAttribute('data-current-value') || kabEl.value || '';

        var provMatch = findProvinsi(initProvRaw);
        var kabMatch  = findKabupaten(initKabRaw, provMatch ? provMatch.kode : null);
        if (!provMatch && kabMatch) {
            provMatch = findProvinsi(kabMatch.provinsi_kode);
        }

        populateProvinsiSelect(provEl, provMatch ? provMatch.nama : initProvRaw);
        populateKabupatenSelect(kabEl, provMatch ? provMatch.kode : null, kabMatch ? kabMatch.nama : initKabRaw);

        if (kabMatch) {
            var kodeSel = kabEl.getAttribute('data-kode-target');
            var kodeEl = kodeSel ? document.querySelector(kodeSel) : null;
            if (kodeEl && !kodeEl.value) {
                kodeEl.value = kabMatch.kode;
            }
        }

        initSelect2On(provEl);
        initSelect2On(kabEl);

        function onProvChange() {
            if (syncing) return;
            syncing = true;
            try {
                var pObj = findProvinsi(provEl.value);
                var pKode = pObj ? pObj.kode : null;
                var curKab = kabEl.value || '';
                var validKab = curKab ? findKabupaten(curKab, pKode) : null;
                populateKabupatenSelect(kabEl, pKode, validKab ? validKab.nama : '');
                if (!validKab) {
                    kabEl.value = '';
                    updateKodeTarget(kabEl, '');
                } else {
                    updateKodeTarget(kabEl, validKab.kode);
                }
                refreshSelect2(kabEl);
            } finally {
                syncing = false;
            }
        }

        function onKabChange() {
            if (syncing) return;
            syncing = true;
            try {
                var curKab = kabEl.value || '';
                if (!curKab) {
                    updateKodeTarget(kabEl, '');
                    return;
                }
                var curProvObj = findProvinsi(provEl.value);
                var kObj = findKabupaten(curKab, curProvObj ? curProvObj.kode : null) || findKabupaten(curKab, null);
                if (kObj) {
                    updateKodeTarget(kabEl, kObj.kode);
                    if (!curProvObj || curProvObj.kode !== kObj.provinsi_kode) {
                        populateProvinsiSelect(provEl, kObj.provinsi_nama);
                        refreshSelect2(provEl);
                        populateKabupatenSelect(kabEl, kObj.provinsi_kode, kObj.nama);
                        refreshSelect2(kabEl);
                    }
                }
            } finally {
                syncing = false;
            }
        }

        if (hasSelect2()) {
            window.jQuery(provEl).on('change.eccWilayah', onProvChange);
            window.jQuery(kabEl).on('change.eccWilayah', onKabChange);
        } else {
            provEl.addEventListener('change', onProvChange);
            kabEl.addEventListener('change', onKabChange);
        }

        pairRegistry[pairKey] = {
            provEl: provEl,
            kabEl: kabEl,
            setValues: function (provVal, kabVal, kodeOverride) {
                syncing = true;
                try {
                    var pObj = findProvinsi(provVal);
                    var kObj = findKabupaten(kabVal || kodeOverride, pObj ? pObj.kode : null) || findKabupaten(kabVal || kodeOverride, null);
                    if (!pObj && kObj) {
                        pObj = findProvinsi(kObj.provinsi_kode);
                    }
                    populateProvinsiSelect(provEl, pObj ? pObj.nama : (provVal || ''));
                    populateKabupatenSelect(kabEl, pObj ? pObj.kode : null, kObj ? kObj.nama : (kabVal || ''));
                    refreshSelect2(provEl);
                    refreshSelect2(kabEl);
                    var finalKode = kodeOverride || (kObj ? kObj.kode : '');
                    updateKodeTarget(kabEl, finalKode);
                } finally {
                    syncing = false;
                }
            },
            reset: function () {
                this.setValues('', '', '');
            }
        };
    }

    function bindSingleKab(kabEl) {
        var initVal = kabEl.getAttribute('data-current-value') || kabEl.value || '';
        var match   = findKabupaten(initVal, null);
        populateKabupatenSelect(kabEl, null, match ? match.nama : initVal);
        initSelect2On(kabEl);
    }

    function initAll(root) {
        var scope = root || document;
        return loadData().then(function () {
            var provNodes = scope.querySelectorAll('select.js-ecc-wilayah-prov[data-wilayah-pair]');
            provNodes.forEach(function (provEl) {
                if (provEl.dataset.eccWilayahBound === '1') return;
                var pairKey = provEl.getAttribute('data-wilayah-pair');
                var kabEl = scope.querySelector('select.js-ecc-wilayah-kab[data-wilayah-pair="' + pairKey + '"]');
                provEl.dataset.eccWilayahBound = '1';
                if (kabEl) {
                    kabEl.dataset.eccWilayahBound = '1';
                    bindPair(pairKey, provEl, kabEl);
                } else {
                    populateProvinsiSelect(provEl, provEl.getAttribute('data-current-value') || provEl.value || '');
                    initSelect2On(provEl);
                }
            });

            var singleKabs = scope.querySelectorAll('select.js-ecc-wilayah-kab-only');
            singleKabs.forEach(function (kabEl) {
                if (kabEl.dataset.eccWilayahBound === '1') return;
                kabEl.dataset.eccWilayahBound = '1';
                bindSingleKab(kabEl);
            });
        });
    }

    window.EccWilayah = {
        load: loadData,
        initAll: initAll,
        setPairValues: function (pairKey, provVal, kabVal, kodeOverride) {
            return loadData().then(function () {
                if (pairRegistry[pairKey]) {
                    pairRegistry[pairKey].setValues(provVal, kabVal, kodeOverride);
                }
            });
        },
        resetPair: function (pairKey) {
            if (pairRegistry[pairKey]) {
                pairRegistry[pairKey].reset();
            }
        },
        findProvinsi: findProvinsi,
        findKabupaten: findKabupaten
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initAll(document); });
    } else {
        initAll(document);
    }
})(window, document);
