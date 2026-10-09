/*!
 * PCHM – Konfigurator: Gruppen-Navigation, Kompatibilitätsprüfung, Netzteil-Empfehlung.
 *
 * Wird nur auf Artikeln mit JTL-Konfigurator geladen (layout/footer.tpl, Block layout-footer-js).
 * Liest ausschließlich: #pchm-cfg-data (JSON aus Bootstrap::cfgData) und die JTL-Formularfelder
 * item[Gruppe][]. Preis, Warenkorb und Formular von JTL werden nicht verändert – einzige Ausnahme:
 * bei echten Konflikten kann das Absenden verhindert werden (Einstellung „compat_block_cart“).
 *
 * Die Regeln entsprechen api/_hardware.php der PCHM Companion App (pchm_check_compatibility,
 * pchm_estimated_psu_need). Grundsatz von dort: Fehlt ein Wert, ist das „ungeprüft“ – nie „passt“.
 */
(function (window, document) {
    'use strict';

    // ------------------------------------------------------------------
    // Regeln (DOM-frei, in tests/pchm/js/konfigurator-rules.test.mjs geprüft)
    // ------------------------------------------------------------------
    var Rules = {
        BASE_LOAD_WATTS: 120, // PCHM_BASE_LOAD_WATTS
        PSU_HEADROOM: 1.4,    // PCHM_PSU_HEADROOM

        value: function (profile, type, field) {
            var entry = profile[type];
            if (!entry || !entry.spec) {
                return null;
            }
            var v = entry.spec[field];
            return v === undefined || v === null || v === '' ? null : v;
        },

        // (CPU-TDP + GPU-TDP + Grundlast) * Reserve; null, wenn keine TDP bekannt ist
        psuNeed: function (profile, candidate) {
            var cpu = Number(Rules.value(profile, 'CPU', 'tdp_watts') || 0);
            var gpu = Number(Rules.value(profile, 'GPU', 'tdp_watts') || 0);
            if (candidate && candidate.tdp_watts) {
                if (candidate.component_type === 'GPU') { gpu = Number(candidate.tdp_watts); }
                if (candidate.component_type === 'CPU') { cpu = Number(candidate.tdp_watts); }
            }
            if (!cpu && !gpu) {
                return null;
            }
            return Math.ceil((cpu + gpu + Rules.BASE_LOAD_WATTS) * Rules.PSU_HEADROOM);
        },

        same: function (a, b) {
            return String(a).toUpperCase() === String(b).toUpperCase();
        },

        /**
         * Prüft einen Kandidaten gegen das übrige Profil.
         * @return {{status: string, blockers: string[], warnings: string[], checks: string[]}}
         */
        check: function (profile, candidate, t) {
            t = t || {};
            var f = function (key, a, b) {
                return String(t[key] || key).replace('{0}', a).replace('{1}', b);
            };
            var type = candidate.component_type;
            var blockers = [];
            var warnings = [];
            var checks = [];
            var a;
            var b;

            // Sockel: CPU gegen Mainboard (wie _hardware.php) und Mainboard gegen CPU
            if (type === 'CPU' || type === 'Mainboard') {
                var cpuSocket = type === 'CPU' ? candidate.socket : Rules.value(profile, 'CPU', 'socket');
                var boardSocket = type === 'Mainboard' ? candidate.socket : Rules.value(profile, 'Mainboard', 'socket');
                var other = type === 'CPU' ? 'Mainboard' : 'CPU';
                if (profile[other]) {
                    if (cpuSocket && boardSocket) {
                        if (Rules.same(cpuSocket, boardSocket)) {
                            checks.push(f('cfgSocketOk', cpuSocket));
                        } else {
                            blockers.push(f('cfgSocketBad', cpuSocket, boardSocket));
                        }
                    } else {
                        warnings.push(f('cfgSocketUnknown'));
                    }
                }
            }

            // RAM-Typ: RAM gegen Mainboard und Mainboard gegen RAM
            if (type === 'RAM' || type === 'Mainboard') {
                var ram = type === 'RAM' ? candidate.ram_type : Rules.value(profile, 'RAM', 'ram_type');
                var boardRam = type === 'Mainboard' ? candidate.ram_type : Rules.value(profile, 'Mainboard', 'ram_type');
                if (profile[type === 'RAM' ? 'Mainboard' : 'RAM']) {
                    if (ram && boardRam) {
                        if (Rules.same(ram, boardRam)) {
                            checks.push(f('cfgRamOk', ram));
                        } else {
                            blockers.push(f('cfgRamBad', ram, boardRam));
                        }
                    } else {
                        warnings.push(f('cfgRamUnknown'));
                    }
                }
            }

            // Grafikkartenlänge: GPU gegen Gehäuse und Gehäuse gegen GPU
            if (type === 'GPU' || type === 'Gehäuse') {
                a = type === 'GPU' ? candidate.length_mm : Rules.value(profile, 'GPU', 'length_mm');
                b = type === 'Gehäuse' ? candidate.max_gpu_length_mm : Rules.value(profile, 'Gehäuse', 'max_gpu_length_mm');
                if (profile[type === 'GPU' ? 'Gehäuse' : 'GPU']) {
                    if (a && b) {
                        if (Number(a) <= Number(b)) {
                            checks.push(f('cfgGpuOk', a, b));
                        } else {
                            blockers.push(f('cfgGpuBad', a, b));
                        }
                    } else {
                        warnings.push(f('cfgGpuUnknown'));
                    }
                }
            }

            // Kühler-Bauhöhe: Kühlung gegen Gehäuse und Gehäuse gegen Kühlung
            if (type === 'Kühlung' || type === 'Gehäuse') {
                a = type === 'Kühlung' ? candidate.height_mm : Rules.value(profile, 'Kühlung', 'height_mm');
                b = type === 'Gehäuse' ? candidate.max_cooler_height_mm : Rules.value(profile, 'Gehäuse', 'max_cooler_height_mm');
                // Wasserkühlungen haben keine Bauhöhe – nur prüfen, wenn auf einer Seite ein Wert vorliegt
                if (profile[type === 'Kühlung' ? 'Gehäuse' : 'Kühlung'] && (a || b)) {
                    if (a && b) {
                        if (Number(a) <= Number(b)) {
                            checks.push(f('cfgCoolerOk', a, b));
                        } else {
                            blockers.push(f('cfgCoolerBad', a, b));
                        }
                    } else {
                        warnings.push(f('cfgCoolerUnknown'));
                    }
                }
            }

            // Netzteil: betrifft CPU-, GPU- und Netzteil-Wechsel
            if (type === 'CPU' || type === 'GPU' || type === 'Netzteil') {
                var psu = type === 'Netzteil' ? candidate.psu_watts : Rules.value(profile, 'Netzteil', 'psu_watts');
                var need = Rules.psuNeed(profile, type === 'Netzteil' ? null : candidate);
                if (type === 'Netzteil' || profile['Netzteil']) {
                    if (psu && need) {
                        if (Number(psu) >= need) {
                            checks.push(f('cfgPsuOk', psu, need));
                        } else {
                            blockers.push(f('cfgPsuBad', psu, need));
                        }
                    } else if (type !== 'Netzteil' || profile['CPU'] || profile['GPU']) {
                        warnings.push(f('cfgPsuUnknown'));
                    }
                }
            }

            return {
                status: blockers.length ? 'blocked' : (checks.length ? 'ok' : 'unknown'),
                blockers: blockers,
                warnings: warnings,
                checks: checks
            };
        },

        // Profil aus der aktuellen Auswahl: {Typ: {id, name, spec}}
        profile: function (data, selectedIds) {
            var profile = {};
            selectedIds.forEach(function (id) {
                var item = data.items[id];
                if (item && item.type && !profile[item.type]) {
                    profile[item.type] = { id: id, name: item.name, spec: item.spec || {} };
                }
            });
            return profile;
        },

        candidate: function (item) {
            var c = {};
            var spec = item.spec || {};
            Object.keys(spec).forEach(function (k) { c[k] = spec[k]; });
            c.component_type = item.type;
            return c;
        },

        // Prüfung der gesamten Auswahl (jedes gewählte Teil gegen den Rest)
        evaluate: function (data, selectedIds, t) {
            var profile = Rules.profile(data, selectedIds);
            var seen = {};
            var result = { blockers: [], warnings: [], checks: [], psuNeed: Rules.psuNeed(profile, null) };
            var add = function (list, msgs) {
                msgs.forEach(function (m) {
                    if (!seen[m]) { seen[m] = true; list.push(m); }
                });
            };
            Object.keys(profile).forEach(function (type) {
                var r = Rules.check(profile, Rules.candidate(data.items[profile[type].id]), t);
                add(result.blockers, r.blockers);
                add(result.warnings, r.warnings);
                add(result.checks, r.checks);
            });
            // Hinweise, die durch eine bestandene Prüfung erledigt sind, nicht doppelt zeigen
            result.status = result.blockers.length ? 'blocked' : (result.warnings.length ? 'unknown' : 'ok');
            return result;
        }
    };

    window.PCHMKonfiguratorRules = Rules;

    // ------------------------------------------------------------------
    // Oberfläche
    // ------------------------------------------------------------------
    var $ = window.jQuery;
    var cfg = (window.pchmConfig && window.pchmConfig.konfigurator) || {};
    var texts = (window.pchmConfig && window.pchmConfig.texts) || {};

    function fmt(key, a, b) {
        return String(texts[key] || key).replace('{0}', a).replace('{1}', b);
    }

    function readData() {
        var el = document.getElementById('pchm-cfg-data');
        if (!el) {
            return null;
        }
        try {
            var raw = JSON.parse(el.textContent);
            var data = { groups: raw.groups || [], items: {} };
            data.groups.forEach(function (g) {
                Object.keys(g.items || {}).forEach(function (id) {
                    var item = g.items[id];
                    item.group = g.id;
                    // Der Typ der Gruppe (Funktionsattribut/Gruppenname) schlägt den Artikelnamen
                    item.type = g.type || item.type || null;
                    data.items[id] = item;
                });
            });
            return data;
        } catch (e) {
            return null;
        }
    }

    function selectedIds() {
        var ids = [];
        var form = document.getElementById('buy_form') || document;
        form.querySelectorAll('input[name^="item["]:checked').forEach(function (el) {
            ids.push(String(el.value));
        });
        form.querySelectorAll('select[name^="item["]').forEach(function (el) {
            Array.prototype.forEach.call(el.selectedOptions || [], function (o) {
                if (o.value) { ids.push(String(o.value)); }
            });
        });
        return ids;
    }

    function itemElements(id) {
        var list = [];
        var input = document.getElementById('item' + id);
        if (input) {
            list.push(input.tagName === 'OPTION' ? input : (input.closest('.custom-control, .cfg-swatch, .config-item') || input));
        }
        document.querySelectorAll('.config-item[data-id="' + id + '"]').forEach(function (el) {
            if (list.indexOf(el) < 0) { list.push(el); }
        });
        return list;
    }

    function stickyOffset() {
        var header = document.getElementById('jtl-nav-wrapper');
        if (!header) { return 0; }
        var pos = window.getComputedStyle(header).position;
        return pos === 'sticky' || pos === 'fixed' ? header.offsetHeight : 0;
    }

    function renderNav(data, ids) {
        var nav = document.getElementById('pchm-cfg-nav');
        if (!nav || !cfg.groupNav) {
            return;
        }
        var chosen = 0;
        var html = '<ol class="pchm-cfg-steps">';
        data.groups.forEach(function (g) {
            var names = [];
            Object.keys(g.items || {}).forEach(function (id) {
                if (ids.indexOf(id) >= 0) { names.push(g.items[id].name); }
            });
            var required = g.min > 0;
            var done = names.length > 0;
            if (done) { chosen++; }
            html += '<li class="pchm-cfg-step' + (done ? ' is-done' : '') + (!done && required ? ' is-required' : '') + '">'
                + '<a href="#cfg-grp-' + g.id + '" data-pchm-cfg-goto="' + g.id + '">'
                + '<span class="pchm-cfg-step-name"></span>'
                + '<span class="pchm-cfg-step-value"></span></a></li>';
        });
        html += '</ol>';
        nav.innerHTML = '<div class="pchm-cfg-progress"></div>' + html;
        var steps = nav.querySelectorAll('.pchm-cfg-step');
        data.groups.forEach(function (g, i) {
            var names = [];
            Object.keys(g.items || {}).forEach(function (id) {
                if (ids.indexOf(id) >= 0) { names.push(g.items[id].name); }
            });
            steps[i].querySelector('.pchm-cfg-step-name').textContent = g.name;
            steps[i].querySelector('.pchm-cfg-step-value').textContent = names.length
                ? names.join(', ')
                : (g.min > 0 ? texts.cfgRequired || 'Pflicht' : texts.cfgNotChosen || '–');
        });
        nav.querySelector('.pchm-cfg-progress').textContent = fmt('cfgProgress', chosen, data.groups.length);
        nav.hidden = false;
    }

    function compatBox() {
        var box = document.getElementById('pchm-cfg-compat');
        if (box) {
            return box;
        }
        box = document.createElement('div');
        box.id = 'pchm-cfg-compat';
        box.className = 'pchm-cfg-compat';
        box.setAttribute('aria-live', 'polite');
        var sidebar = document.getElementById('product-configuration-sidebar');
        var nav = document.getElementById('pchm-cfg-nav');
        if (sidebar) {
            sidebar.insertBefore(box, sidebar.firstChild);
        } else if (nav && nav.parentNode) {
            nav.parentNode.insertBefore(box, nav.nextSibling);
        } else {
            var container = document.getElementById('cfg-container');
            if (!container) { return null; }
            container.insertBefore(box, container.firstChild);
        }
        return box;
    }

    function renderCompat(data, ids) {
        if (!cfg.compat) {
            return null;
        }
        var result = Rules.evaluate(data, ids, texts);
        var box = compatBox();
        if (box) {
            var html = '<div class="pchm-cfg-compat-head"><strong></strong><span class="pchm-cfg-compat-status"></span></div><ul></ul>';
            box.innerHTML = html;
            box.className = 'pchm-cfg-compat is-' + result.status + (box.classList.contains('is-attention') ? ' is-attention' : '');
            box.querySelector('strong').textContent = texts.cfgCompatTitle || 'Kompatibilität';
            var status = result.status === 'blocked'
                ? fmt('cfgCompatBlocked', result.blockers.length)
                : (result.status === 'unknown' ? fmt('cfgCompatUnknown', result.warnings.length) : fmt('cfgCompatOk'));
            box.querySelector('.pchm-cfg-compat-status').textContent = status;
            var ul = box.querySelector('ul');
            var addLine = function (cls, text) {
                var li = document.createElement('li');
                li.className = cls;
                li.textContent = text;
                ul.appendChild(li);
            };
            result.blockers.forEach(function (m) { addLine('is-blocked', m); });
            result.warnings.forEach(function (m) { addLine('is-unknown', m); });
            result.checks.forEach(function (m) { addLine('is-ok', m); });
            if (cfg.psuHint && result.psuNeed) {
                addLine('is-psu', fmt('cfgPsuNeed', result.psuNeed));
            }
        }
        return result;
    }

    function markItems(data, ids) {
        if (!cfg.compat) {
            return;
        }
        var profile = Rules.profile(data, ids);
        Object.keys(data.items).forEach(function (id) {
            var item = data.items[id];
            var els = itemElements(id);
            var selected = ids.indexOf(id) >= 0;
            var r = null;
            if (item.type && !selected) {
                var rest = {};
                Object.keys(profile).forEach(function (t) { if (t !== item.type) { rest[t] = profile[t]; } });
                r = Rules.check(rest, Rules.candidate(item), texts);
            }
            var blocked = !!(r && r.status === 'blocked');
            els.forEach(function (el) {
                el.classList.toggle('pchm-cfg-blocked', blocked);
                var note = el.querySelector && el.querySelector(':scope > .pchm-cfg-note');
                if (el.tagName === 'OPTION') {
                    if (!el.dataset.pchmLabel) { el.dataset.pchmLabel = el.textContent; }
                    el.textContent = blocked ? el.dataset.pchmLabel.trim() + ' – ' + (texts.cfgItemBlocked || 'Passt nicht') : el.dataset.pchmLabel;
                } else if (blocked) {
                    if (!note) {
                        note = document.createElement('div');
                        note.className = 'pchm-cfg-note';
                        el.appendChild(note);
                    }
                    note.textContent = (texts.cfgItemBlocked || 'Passt nicht') + ': ' + r.blockers.join(' ');
                } else if (note) {
                    note.remove();
                }
                if (cfg.compatMode === 'disable') {
                    var input = el.tagName === 'OPTION' ? el : (el.querySelector('input') || document.getElementById('item' + id));
                    if (input && !selected) {
                        if (blocked) {
                            if (!input.disabled) { input.dataset.pchmDisabled = '1'; input.disabled = true; }
                        } else if (input.dataset.pchmDisabled === '1') {
                            input.disabled = false;
                            delete input.dataset.pchmDisabled;
                        }
                    }
                }
            });
        });
    }

    function init() {
        var data = readData();
        if (!data || !data.groups.length) {
            return;
        }
        var lastResult = null;
        var update = function () {
            var ids = selectedIds();
            renderNav(data, ids);
            lastResult = renderCompat(data, ids);
            markItems(data, ids);
            document.querySelectorAll('#buy_form button[name="inWarenkorb"]').forEach(function (btn) {
                btn.classList.toggle('pchm-cfg-cart-blocked', !!(cfg.blockCart && lastResult && lastResult.status === 'blocked'));
            });
        };
        document.addEventListener('change', function (e) {
            if (e.target && e.target.name && e.target.name.indexOf('item[') === 0) {
                update();
            }
        });
        document.addEventListener('click', function (e) {
            var link = e.target.closest && e.target.closest('[data-pchm-cfg-goto]');
            if (!link) { return; }
            var group = document.getElementById('cfg-grp-' + link.getAttribute('data-pchm-cfg-goto'));
            if (!group) { return; }
            e.preventDefault();
            var nav = document.getElementById('pchm-cfg-nav');
            var top = group.getBoundingClientRect().top + window.pageYOffset - stickyOffset() - (nav ? nav.offsetHeight : 0) - 8;
            window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
        });
        // Absenden bei Konflikt verhindern (Capture-Phase: vor JTL/NOVA)
        document.addEventListener('submit', function (e) {
            if (e.target && e.target.id === 'buy_form' && cfg.blockCart && lastResult && lastResult.status === 'blocked') {
                var submitter = e.submitter;
                if (submitter && submitter.name && submitter.name !== 'inWarenkorb') {
                    return;
                }
                e.preventDefault();
                e.stopImmediatePropagation();
                var box = document.getElementById('pchm-cfg-compat');
                if (box) {
                    box.classList.add('is-attention');
                    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    window.setTimeout(function () { box.classList.remove('is-attention'); }, 1600);
                }
                if ($ && $.evo && $.evo.extended) {
                    $.evo.extended().showNotify({ text: texts.cfgCartBlocked || '', title: texts.cfgCompatTitle || '' });
                }
            }
        }, true);
        update();
        // NOVA setzt bei „Konfiguration bearbeiten“ Werte nachträglich
        window.setTimeout(update, 400);
        window.PCHMKonfigurator = { update: update, data: data };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window, document);
