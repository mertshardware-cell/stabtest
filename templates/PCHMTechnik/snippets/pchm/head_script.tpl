{block name='pchm-head-script'}
    <script>
        window.pchmConfig = {pchm_js_config};
        (function (d, cfg) {
            {* Preisanzeige (Brutto/Netto) so früh wie möglich setzen, damit nichts springt *}
            var mode = cfg.groupNet ? 'net' : 'gross';
            try {
                var stored = cfg.priceSwitch ? window.localStorage.getItem('pchmPriceMode') : null;
                if (stored === 'net' || stored === 'gross') {
                    mode = stored;
                }
            } catch (e) {}
            d.documentElement.classList.add('pchm-price-mode-' + mode);
            if ((mode === 'net') !== !!cfg.groupNet) {
                d.documentElement.classList.add('pchm-price-alt-active');
            }
        })(document, window.pchmConfig);
    </script>
{/block}
