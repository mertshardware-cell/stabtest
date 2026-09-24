/*!
 * PCHMTechnik – Funktionen des Technik-Templates für NOVA (JTL-Shop 5)
 * Wird von NOVA automatisch als letzte Datei geladen (layout/header.tpl → js/custom.js).
 * Konfiguration: window.pchmConfig (snippets/pchm/head_script.tpl)
 */
(function (window, document, $) {
    'use strict';

    if (!$) {
        return;
    }

    var cfg = window.pchmConfig || {};
    var texts = cfg.texts || {};
    var html = document.documentElement;
    var $doc = $(document);
    var DESKTOP_MIN = 992;

    var storage = {
        get: function (key) {
            try { return window.localStorage.getItem(key); } catch (e) { return null; }
        },
        set: function (key, value) {
            try { window.localStorage.setItem(key, value); } catch (e) { /* privater Modus */ }
        }
    };

    function isDesktop() {
        return window.innerWidth >= DESKTOP_MIN;
    }

    function hash(str) {
        var h = 0;
        for (var i = 0; i < str.length; i++) {
            h = ((h << 5) - h + str.charCodeAt(i)) | 0;
        }
        return String(h);
    }

    // ------------------------------------------------------------------
    // Preise lesen/formatieren (Währungsformat aus JTL)
    // ------------------------------------------------------------------
    var currency = cfg.currency || { decimal: ',', thousands: '.', symbol: '€', before: false };

    function escapeRegExp(str) {
        return String(str).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    var amountPattern = new RegExp(
        '\\d+(?:' + escapeRegExp(currency.thousands || '') + '\\d{3})*(?:' + escapeRegExp(currency.decimal || ',') + '\\d{1,2})?',
        'g'
    );

    function parseAmount(str) {
        var normalized = String(str);
        if (currency.thousands) {
            normalized = normalized.split(currency.thousands).join('');
        }
        normalized = normalized.replace(currency.decimal || ',', '.');
        var value = parseFloat(normalized);
        return isNaN(value) ? null : value;
    }

    function formatNumber(value) {
        var fixed = (Math.round(value * 100) / 100).toFixed(2).split('.');
        fixed[0] = fixed[0].replace(/\B(?=(\d{3})+(?!\d))/g, currency.thousands || '');
        return fixed.join(currency.decimal || ',');
    }

    function formatPrice(value) {
        var number = formatNumber(value);
        return currency.before ? currency.symbol + ' ' + number : number + ' ' + currency.symbol;
    }

    function firstAmount(text) {
        var match = String(text).match(amountPattern);
        return match ? parseAmount(match[0]) : null;
    }

    // ------------------------------------------------------------------
    // Seitenleisten (Offcanvas)
    // ------------------------------------------------------------------
    var Offcanvas = {
        $active: null,
        $trigger: null,

        open: function (name, trigger) {
            var $panel = $('#pchm-offcanvas-' + name);
            if (!$panel.length) {
                return false;
            }
            if (this.$active && this.$active[0] !== $panel[0]) {
                this.close(true);
            }
            if (name === 'cart') {
                syncSideCart();
            }
            if (name === 'news') {
                markNewsRead();
            }
            this.$active = $panel;
            this.$trigger = trigger ? $(trigger) : null;
            $panel.addClass('is-open').attr('aria-hidden', 'false');
            $('.pchm-offcanvas-backdrop').addClass('is-open');
            $('body').addClass('pchm-offcanvas-open');
            $('[data-pchm-offcanvas="' + name + '"]').attr('aria-expanded', 'true');
            window.setTimeout(function () { $panel.trigger('focus'); }, 50);
            $doc.trigger('pchm:offcanvas:open', [name]);
            return true;
        },

        close: function (silent) {
            if (!this.$active) {
                return;
            }
            var name = this.$active.attr('id').replace('pchm-offcanvas-', '');
            this.$active.removeClass('is-open').attr('aria-hidden', 'true');
            $('[data-pchm-offcanvas="' + name + '"]').attr('aria-expanded', 'false');
            this.$active = null;
            if (!silent) {
                $('.pchm-offcanvas-backdrop').removeClass('is-open');
                $('body').removeClass('pchm-offcanvas-open');
                if (this.$trigger && this.$trigger.length) {
                    this.$trigger.trigger('focus');
                }
            }
            this.$trigger = null;
        },

        isOpen: function (name) {
            return !!(this.$active && this.$active.attr('id') === 'pchm-offcanvas-' + name);
        }
    };

    $doc.on('click', '[data-pchm-offcanvas]', function (e) {
        var name = $(this).attr('data-pchm-offcanvas');
        if (Offcanvas.isOpen(name)) {
            e.preventDefault();
            Offcanvas.close();
            return;
        }
        if (Offcanvas.open(name, this)) {
            e.preventDefault();
        }
    }).on('click', '[data-pchm-close]', function (e) {
        e.preventDefault();
        Offcanvas.close();
    }).on('keydown', function (e) {
        if (e.key === 'Escape' && Offcanvas.$active) {
            Offcanvas.close();
        }
    });

    // ------------------------------------------------------------------
    // Side-Basket
    // ------------------------------------------------------------------
    function syncSideCart() {
        var $body = $('[data-pchm-cart-body]');
        var tpl = document.querySelector('.cart-icon-dropdown .pchm-cart-template');
        if (!$body.length || !tpl) {
            return;
        }
        $body.empty().append(document.importNode(tpl.content, true));
    }

    function initSideBasket() {
        if (!cfg.sideBasket || !$.evo || typeof $.evo.basket !== 'function') {
            return;
        }
        var proto = Object.getPrototypeOf($.evo.basket());
        var openAfterUpdate = false;

        // NOVA ersetzt nach jeder Änderung das komplette Warenkorb-Icon per AJAX – danach Seitenleiste aktualisieren
        proto.updateCart = function (type) {
            var t = type === undefined ? 0 : parseInt(type, 10);
            var selector = this.options.selector.cart.container;
            $.evo.io().call('getBasketItems', [t], this, function (error, data) {
                if (error || !data || !data.response) {
                    return;
                }
                $(selector).replaceWith(data.response.cTemplate);
                if (Offcanvas.isOpen('cart')) {
                    syncSideCart();
                }
                if (openAfterUpdate) {
                    openAfterUpdate = false;
                    Offcanvas.open('cart', $('.pchm-cart-trigger .nav-link')[0]);
                }
                $doc.trigger('pchm:cart:updated');
            });
        };

        if (cfg.sideBasketOpen) {
            var originalPushed = proto.pushedToBasket;
            proto.pushedToBasket = function (response) {
                if ($('#pchm-offcanvas-cart').length) {
                    openAfterUpdate = true;
                    return;
                }
                return originalPushed.call(this, response);
            };
        }
    }

    // ------------------------------------------------------------------
    // Netto-/Brutto-Umschalter (reine Anzeige)
    // ------------------------------------------------------------------
    var PriceSwitch = {
        mode: html.classList.contains('pchm-price-mode-net') ? 'net' : 'gross',

        altActive: function () {
            return (this.mode === 'net') !== !!cfg.groupNet;
        },

        setMode: function (mode) {
            this.mode = mode === 'net' ? 'net' : 'gross';
            html.classList.remove('pchm-price-mode-net', 'pchm-price-mode-gross');
            html.classList.add('pchm-price-mode-' + this.mode);
            html.classList.toggle('pchm-price-alt-active', this.altActive());
            storage.set('pchmPriceMode', this.mode);
            $('.pchm-price-switch-btn').each(function () {
                $(this).attr('aria-pressed', $(this).attr('data-pchm-price-mode') === PriceSwitch.mode ? 'true' : 'false');
            });
            this.renderAll();
            $doc.trigger('pchm:price-mode', [this.mode]);
        },

        convert: function (value, tax) {
            var factor = 1 + (tax / 100);
            return cfg.groupNet ? value * factor : value / factor;
        },

        render: function (alt) {
            var $alt = $(alt);
            var $price = $alt.siblings('.price').first();
            if (!$price.length) {
                $price = $alt.closest('.price_wrapper').find('.price').first();
            }
            if (!$price.length) {
                return;
            }
            $price.addClass('pchm-has-alt');
            var tax = parseFloat(String($alt.attr('data-pchm-tax')).replace(',', '.')) || 0;
            var source = $price.clone();
            source.find('.footnote-reference, meta').remove();
            var text = $.trim(source.text());
            var self = this;
            var converted = text.replace(amountPattern, function (match) {
                var value = parseAmount(match);
                return value === null ? match : formatNumber(self.convert(value, tax));
            });
            var noteKey = this.mode === 'net' ? 'priceAltNet' : 'priceAltGross';
            var note = (texts[noteKey] || '').replace('%s', String(tax).replace('.', currency.decimal || ','))
                .replace('%%', '%');
            $alt.html(
                '<span class="pchm-price-alt-value"></span><span class="pchm-price-alt-note"></span>'
            );
            $alt.find('.pchm-price-alt-value').text(converted);
            $alt.find('.pchm-price-alt-note').text(note);
            $alt.addClass('is-ready');
        },

        renderAll: function (context) {
            var self = this;
            $('.pchm-price-alt', context || document).each(function () {
                self.render(this);
            });
        },

        init: function () {
            if (!cfg.priceSwitch) {
                return;
            }
            var self = this;
            $doc.on('click', '.pchm-price-switch-btn', function () {
                self.setMode($(this).attr('data-pchm-price-mode'));
            });
            $('.pchm-price-switch-btn').each(function () {
                $(this).attr('aria-pressed', $(this).attr('data-pchm-price-mode') === self.mode ? 'true' : 'false');
            });
            this.renderAll();
            // NOVA aktualisiert Preise bei Variations-/Konfiguratorwechsel per AJAX
            $doc.on('evo:changed.article.price', function () {
                window.setTimeout(function () { self.renderAll(); }, 0);
            });
            if ('MutationObserver' in window) {
                var pending = false;
                new MutationObserver(function (mutations) {
                    for (var i = 0; i < mutations.length; i++) {
                        var target = mutations[i].target;
                        var el = target.nodeType === 1 ? target : target.parentNode;
                        if (el && el.closest && el.closest('.price_wrapper') && !el.closest('.pchm-price-alt')) {
                            if (!pending) {
                                pending = true;
                                window.requestAnimationFrame(function () {
                                    pending = false;
                                    self.renderAll();
                                });
                            }
                            return;
                        }
                    }
                }).observe(document.body, { childList: true, subtree: true, characterData: true });
            }
        }
    };

    // ------------------------------------------------------------------
    // Countdown
    // ------------------------------------------------------------------
    function initCountdowns() {
        var $items = $('[data-pchm-countdown]');
        if (!$items.length) {
            return;
        }
        var pad = function (n) { return n < 10 ? '0' + n : String(n); };
        // Differenz Server- ↔ Browseruhr einmalig je Element ausgleichen
        $items.each(function () {
            var serverNow = parseInt($(this).attr('data-pchm-now'), 10) * 1000;
            $(this).data('pchmOffset', serverNow > 0 ? serverNow - Date.now() : 0);
        });
        var tick = function () {
            $items.each(function () {
                var $el = $(this);
                if ($el.hasClass('is-expired')) {
                    return;
                }
                var end = parseInt($el.attr('data-pchm-countdown'), 10) * 1000;
                var left = Math.max(0, end - (Date.now() + ($el.data('pchmOffset') || 0)));
                var s = Math.floor(left / 1000);
                $el.find('[data-unit="d"]').text(Math.floor(s / 86400));
                $el.find('[data-unit="h"]').text(pad(Math.floor(s % 86400 / 3600)));
                $el.find('[data-unit="m"]').text(pad(Math.floor(s % 3600 / 60)));
                $el.find('[data-unit="s"]').text(pad(s % 60));
                if (left <= 0) {
                    $el.addClass('is-expired');
                    $el.closest('.pchm-home-countdown, .pchm-deal-countdown').addClass('d-none');
                }
            });
        };
        tick();
        window.setInterval(tick, 1000);
    }

    // ------------------------------------------------------------------
    // Hinweisleiste
    // ------------------------------------------------------------------
    function initAnnouncement() {
        var $bar = $('#pchm-announcement');
        if (!$bar.length) {
            return;
        }
        var key = 'pchmAnnouncement' + hash($.trim($bar.find('.pchm-announcement-text').text()));
        var dismissedUntil = parseInt(storage.get(key) || '0', 10);
        if (dismissedUntil > Date.now()) {
            $bar.addClass('is-hidden');
        }
        $doc.on('click', '[data-pchm-dismiss="announcement"]', function () {
            $bar.addClass('is-hidden');
            storage.set(key, String(Date.now() + 7 * 86400000));
        });
    }

    // ------------------------------------------------------------------
    // Mobile USP-Leiste (rotierend)
    // ------------------------------------------------------------------
    function initUspRotator() {
        $('[data-pchm-rotate]').each(function () {
            var $wrap = $(this);
            var $items = $wrap.children();
            if ($items.length < 2) {
                return;
            }
            var index = 0;
            var interval = parseInt($wrap.attr('data-pchm-rotate'), 10) || 4000;
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                interval = Math.max(interval, 8000);
            }
            window.setInterval(function () {
                $items.eq(index).removeClass('is-active');
                index = (index + 1) % $items.length;
                $items.eq(index).addClass('is-active');
            }, interval);
        });
    }

    // ------------------------------------------------------------------
    // Neuigkeiten-Glocke
    // ------------------------------------------------------------------
    function initNewsBell() {
        var $trigger = $('[data-pchm-news-latest]');
        if (!$trigger.length) {
            return;
        }
        var latest = parseInt($trigger.attr('data-pchm-news-latest'), 10) || 0;
        var seen = parseInt(storage.get('pchmNewsSeen') || '0', 10);
        if (latest > seen) {
            $trigger.find('.pchm-news-dot').removeClass('d-none');
        }
    }

    function markNewsRead() {
        var $trigger = $('[data-pchm-news-latest]');
        storage.set('pchmNewsSeen', $trigger.attr('data-pchm-news-latest') || '0');
        $trigger.find('.pchm-news-dot').addClass('d-none');
    }

    // ------------------------------------------------------------------
    // Megamenü per Klick (Desktop)
    // ------------------------------------------------------------------
    function initMegaMenuClick() {
        if (cfg.megaMenuMode !== 'click') {
            return;
        }
        var itemSelector = '#mainNavigation .navbar-nav > .dropdown';
        var hasTouch = 'ontouchstart' in window;
        // Hover-Handler von NOVA (js/app/views/header.js) entfernen
        $doc.off('mouseenter', itemSelector).off('mouseleave', itemSelector);
        if (hasTouch) {
            return; // auf Touch-Geräten öffnet NOVA ohnehin per Klick
        }
        var $backdrop = $('<div class="pchm-mm-backdrop"></div>');

        function closeAll() {
            $(itemSelector + '.show').each(function () {
                $(this).removeClass('show')
                    .children('.dropdown-menu').removeClass('show');
                $(this).children('.dropdown-toggle').attr('aria-expanded', 'false');
            });
            $backdrop.detach();
        }

        $doc.on('click', itemSelector + ' > .dropdown-toggle', function (e) {
            if (!isDesktop()) {
                return;
            }
            e.preventDefault();
            var $item = $(this).parent();
            var wasOpen = $item.hasClass('show');
            closeAll();
            if (!wasOpen) {
                $item.addClass('show').children('.dropdown-menu').addClass('show');
                $(this).attr('aria-expanded', 'true');
                $backdrop.insertBefore('#jtl-nav-wrapper');
            }
        });
        $backdrop.on('click', closeAll);
        $doc.on('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAll();
            }
        });
        $(window).on('resize', function () {
            if (!isDesktop()) {
                closeAll();
            }
        });
    }

    // ------------------------------------------------------------------
    // Suche
    // ------------------------------------------------------------------
    function initSearchPlaceholder() {
        if (cfg.searchPlaceholder) {
            $('#search-header, #search-header-mobile-top, #search-header-mobile-fixed, #search-header-desktop')
                .attr('placeholder', cfg.searchPlaceholder);
        }
    }

    // ------------------------------------------------------------------
    // Hersteller-Laufband
    // ------------------------------------------------------------------
    function initBrandMarquee() {
        $('[data-pchm-marquee]').each(function () {
            var $slider = $(this);
            var $track = $slider.find('.pchm-brand-track');
            if (!$track.length || $track[0].scrollWidth <= $slider.width()) {
                return; // passt komplett in die Breite – kein Laufband nötig
            }
            var width = $track[0].scrollWidth;
            $track.append($track.children().clone().attr('aria-hidden', 'true').find('a').attr('tabindex', '-1').end());
            $slider.css('--pchm-marquee-duration', Math.max(20, Math.round(width / 40)) + 's').addClass('is-animated');
        });
    }

    // ------------------------------------------------------------------
    // Lange Beschreibungen einklappen
    // ------------------------------------------------------------------
    function initFoldables() {
        if (!cfg.descriptionFold) {
            return;
        }
        $('.pchm-foldable').each(function () {
            var $box = $(this);
            if (this.scrollHeight <= 200) {
                return;
            }
            $box.addClass('is-folded');
            var $btn = $('<button type="button" class="btn btn-link pchm-fold-toggle"></button>')
                .text(texts.readMore || 'Weiterlesen')
                .attr('aria-expanded', 'false');
            $btn.on('click', function () {
                var folded = $box.toggleClass('is-folded').hasClass('is-folded');
                $btn.text(folded ? (texts.readMore || 'Weiterlesen') : (texts.readLess || 'Weniger anzeigen'))
                    .attr('aria-expanded', folded ? 'false' : 'true');
            });
            $box.after($btn);
        });
    }

    // ------------------------------------------------------------------
    // Filter-Seitenleiste ein-/ausklappen (Artikelliste, Desktop)
    // ------------------------------------------------------------------
    function initSidebarToggle() {
        if (!cfg.sidebarToggle || !$('#sidepanel_left').length || !$('#result-wrapper').length) {
            return;
        }
        var $body = $('body');
        if (storage.get('pchmSidebarCollapsed') === '1') {
            $body.addClass('pchm-sidebar-collapsed');
        }
        var $btn = $('<button type="button" class="btn btn-outline-secondary btn-sm pchm-sidebar-toggle d-none d-lg-inline-block"></button>')
            .html('<i class="fas fa-sliders-h" aria-hidden="true"></i> ')
            .append($('<span>').text(texts.filterToggle || 'Filter'))
            .attr('aria-controls', 'sidepanel_left')
            .attr('aria-expanded', $body.hasClass('pchm-sidebar-collapsed') ? 'false' : 'true');
        $btn.on('click', function () {
            var collapsed = $body.toggleClass('pchm-sidebar-collapsed').hasClass('pchm-sidebar-collapsed');
            storage.set('pchmSidebarCollapsed', collapsed ? '1' : '0');
            $btn.attr('aria-expanded', collapsed ? 'false' : 'true');
            $(window).trigger('resize');
        });
        $('#result-wrapper').prepend($btn);
    }

    // ------------------------------------------------------------------
    // Artikeldetails: Gesamtbetrag im Button, Sprungnavigation, Sticky-Kaufleiste
    // ------------------------------------------------------------------
    function getDetailPrice() {
        var $price = $('#product-offer .price_wrapper .price').first();
        if (html.classList.contains('pchm-price-alt-active')) {
            var $alt = $('#product-offer .pchm-price-alt-value').first();
            if ($alt.length && $.trim($alt.text()) !== '') {
                return $.trim($alt.text());
            }
        }
        var $clone = $price.clone();
        $clone.find('.footnote-reference, meta').remove();
        return $.trim($clone.text());
    }

    function initButtonTotal() {
        if (!cfg.buttonTotal) {
            return;
        }
        var $form = $('#buy_form');
        var $button = $form.find('button[name="inWarenkorb"][type="submit"]').not('.btn-hidden-default').first();
        if (!$button.length) {
            return;
        }
        var $total = $('<span class="pchm-btn-total" aria-live="polite"></span>');
        $button.find('.btn-basket-check').append($total);
        if (!$total.parent().length) {
            $button.append($total);
        }
        var update = function () {
            var unit = firstAmount(getDetailPrice());
            var qty = parseFloat(String($form.find('input[name="anzahl"]').first().val() || '1').replace(',', '.')) || 1;
            if (unit === null || unit <= 0) {
                $total.text('');
                return;
            }
            $total.text(formatPrice(unit * qty));
        };
        $form.on('change input', 'input[name="anzahl"]', update);
        $doc.on('click', '#quantity-grp button', function () { window.setTimeout(update, 0); });
        $doc.on('pchm:price-mode', update);
        $doc.on('evo:changed.article.price', function () { window.setTimeout(update, 10); });
        update();
    }

    function stickyOffset() {
        var header = document.getElementById('jtl-nav-wrapper');
        if (!header) {
            return 0;
        }
        var style = window.getComputedStyle(header);
        return (style.position === 'sticky' || style.position === 'fixed') ? header.offsetHeight : 0;
    }

    function initJumpNav() {
        var $nav = $('#pchm-jumpnav');
        if (!cfg.tabJumpNav || !$nav.length) {
            return;
        }
        var $list = $nav.find('.pchm-jumpnav-list');
        var entries = [];
        // Tabs (Desktop) bzw. Akkordeon (Mobil)
        $('#product-tabs a[data-toggle="tab"], #product-tabs .nav-link').each(function () {
            var $a = $(this);
            if (!$a.attr('href') || $a.closest('.pchm-jumpnav').length) {
                return;
            }
            entries.push({ label: $.trim($a.text()), tab: $a, target: $('.tab-navigation').first() });
        });
        if (!entries.length) {
            $('[id^="tabAccordion"] > .card > .card-header, [id^="tabAccordion"] .card-header[data-toggle="collapse"]').each(function () {
                var $h = $(this);
                entries.push({ label: $.trim($h.text()), collapse: $h, target: $h });
            });
        }
        // Produkt-Slider unterhalb (Zubehör, Ähnliche Artikel …)
        $('#slider-partslist, [id^="slider-xsell"], #slider-related').each(function () {
            var $s = $(this);
            var label = $.trim($s.find('.hr-sect').first().text());
            if (label) {
                entries.push({ label: label, target: $s });
            }
        });
        if (entries.length < 2) {
            return;
        }
        $.each(entries, function (i, entry) {
            var $a = $('<a href="#"></a>').text(entry.label);
            $a.on('click', function (e) {
                e.preventDefault();
                if (entry.tab) {
                    entry.tab.tab('show');
                }
                if (entry.collapse) {
                    $(entry.collapse.attr('data-target')).collapse('show');
                }
                $list.find('a').removeClass('is-active');
                $a.addClass('is-active');
                var top = entry.target.offset().top - stickyOffset() - $nav.outerHeight() - 10;
                window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
            });
            $list.append($('<li></li>').append($a));
        });
        document.documentElement.style.setProperty('--pchm-sticky-offset', stickyOffset() + 'px');
        $(window).on('resize', function () {
            document.documentElement.style.setProperty('--pchm-sticky-offset', stickyOffset() + 'px');
        });
        $nav.prop('hidden', false);
    }

    function initBuyBar() {
        var $bar = $('#pchm-buybar');
        var anchor = document.querySelector('#product-offer .product-info-inner') || document.getElementById('product-offer');
        if (!cfg.stickyBuyBar || !$bar.length || !anchor || !('IntersectionObserver' in window)) {
            return;
        }
        var $price = $bar.find('[data-pchm-buybar-price]');
        var updatePrice = function () { $price.text(getDetailPrice()); };
        updatePrice();
        $doc.on('pchm:price-mode', updatePrice);
        $doc.on('evo:changed.article.price', function () { window.setTimeout(updatePrice, 10); });

        new IntersectionObserver(function (entries) {
            var entry = entries[0];
            // sichtbar, sobald der Kaufbereich nach oben aus dem Bild gescrollt ist
            var show = !entry.isIntersecting && entry.boundingClientRect.top < 0;
            $bar.toggleClass('is-visible', show).attr('aria-hidden', show ? 'false' : 'true');
            $('body').toggleClass('pchm-buybar-visible', show);
        }, { threshold: 0 }).observe(anchor);

        $bar.on('click', '[data-pchm-buybar-action]', function (e) {
            var action = $(this).attr('data-pchm-buybar-action');
            var $buy = $('#buy_form button[name="inWarenkorb"][type="submit"]').not('.btn-hidden-default').first();
            if (action === 'buy' && $buy.length && !$buy.prop('disabled')) {
                e.preventDefault();
                $buy.trigger('click');
                return;
            }
            e.preventDefault();
            var target = $(this).attr('href') || '#product-offer';
            var $target = $(target).length ? $(target) : $('#product-offer');
            window.scrollTo({ top: Math.max(0, $target.offset().top - stickyOffset() - 10), behavior: 'smooth' });
        });
    }

    // ------------------------------------------------------------------
    // Artikeldetails: „In den Warenkorb" per AJAX (wie in der NOVA-Artikelliste), danach Side-Basket
    // Ausgenommen: Konfigurator, Uploads, Gutscheine, Bearbeiten einer Konfiguration
    // ------------------------------------------------------------------
    function initAjaxBuyForm() {
        var $form = $('#buy_form');
        if (!cfg.sideBasket || !$form.length || !$.evo || typeof $.evo.basket !== 'function') {
            return;
        }
        var excluded = $('#product-configurator').length
            || $form.find('input[type="file"], [name="pdp_unique"], [name="kEditKonfig"], [name*="oucher"]').length
            || !$form.find('input[name="anzahl"]').length
            || typeof $form.serializeObject !== 'function';
        if (excluded) {
            return;
        }
        $form.on('submit', function (e) {
            var submitter = e.originalEvent && e.originalEvent.submitter;
            if (e.isDefaultPrevented() || !submitter || submitter.name !== 'inWarenkorb') {
                return; // andere Buttons (z. B. Wunschzettel) und ungültige Formulare normal behandeln
            }
            if (typeof this.checkValidity === 'function' && !this.checkValidity()) {
                return;
            }
            e.preventDefault();
            var data = $form.serializeObject();
            if (data.VariKindArtikel) {
                data.a = data.VariKindArtikel;
            }
            $.evo.basket().addToBasket($form, data);
        });
    }

    // ------------------------------------------------------------------
    // Start (nach NOVA: DOMContentLoaded feuert erst nach allen defer-/Modul-Skripten)
    // ------------------------------------------------------------------
    $(function () {
        initSideBasket();
        PriceSwitch.init();
        initCountdowns();
        initAnnouncement();
        initUspRotator();
        initNewsBell();
        initMegaMenuClick();
        initSearchPlaceholder();
        initBrandMarquee();
        initFoldables();
        initSidebarToggle();
        initButtonTotal();
        initJumpNav();
        initBuyBar();
        initAjaxBuyForm();
    });

    window.pchmTechnik = {
        offcanvas: Offcanvas,
        priceSwitch: PriceSwitch,
        syncSideCart: syncSideCart,
        formatPrice: formatPrice
    };
})(window, document, window.jQuery);
