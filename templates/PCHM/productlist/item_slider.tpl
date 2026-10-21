{* PCHM – Countdown bis Aktionsende im Preisaktionen-Slider (nur wenn $pchmDealCountdown gesetzt ist) *}
{block name='productlist-item-slider-include-price' append}
    {if $pchmDealCountdown|default:false
        && isset($Artikel->Preise->Sonderpreis_aktiv) && $Artikel->Preise->Sonderpreis_aktiv
        && !empty($Artikel->dSonderpreisEnde_en)}
        {pchm_timestamp value=$Artikel->dSonderpreisEnde_en endOfDay=true assign='pchmDealEnd'}
        {if $pchmDealEnd > $smarty.now}
            <div class="pchm-deal-countdown">
                <span class="pchm-deal-countdown-label">{pchm_lang key='dealEndsIn'}</span>
                {include file='snippets/pchm/countdown.tpl' end=$pchmDealEnd variant='compact'}
            </div>
        {/if}
    {/if}
{/block}
