{* Umschalter Privat (brutto) / Gewerbe (netto) – reine Anzeige, Warenkorb und Kasse bleiben unverändert. *}
{block name='pchm-price-switch'}
    <div class="pchm-price-switch" role="group" aria-label="{pchm_lang key='priceSwitchLabel'}">
        <button type="button" class="pchm-price-switch-btn" data-pchm-price-mode="gross"
                title="{pchm_lang key='priceGross'}" aria-pressed="false">
            {pchm_lang key='priceGrossShort'}
        </button>
        <button type="button" class="pchm-price-switch-btn" data-pchm-price-mode="net"
                title="{pchm_lang key='priceNet'}" aria-pressed="false">
            {pchm_lang key='priceNetShort'}
        </button>
    </div>
{/block}
