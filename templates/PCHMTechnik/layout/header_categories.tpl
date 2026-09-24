{* PCHMTechnik – Zusatzbereich im mobilen Menü (Preisanzeige, Hotline) *}
{block name='layout-header-categories-include-include-categories-body' append}
    {$pchmHeader = $Einstellungen.template.pchm_header}
    {if $pchmHeader.price_switch === 'Y' || !empty($pchmHeader.phone)}
        <div class="pchm-mobile-nav-extra d-lg-none">
            {if $pchmHeader.price_switch === 'Y'}
                <div class="pchm-mobile-nav-row">
                    <span class="pchm-mobile-nav-label">{pchm_lang key='priceSwitchLabel'}</span>
                    {include file='snippets/pchm/price_switch.tpl'}
                </div>
            {/if}
            {if !empty($pchmHeader.phone)}
                <div class="pchm-mobile-nav-row">
                    <a href="tel:{$pchmHeader.phone|regex_replace:'/[^0-9+]/':''}" class="pchm-mobile-nav-phone">
                        <i class="fas fa-phone-alt" aria-hidden="true"></i> {$pchmHeader.phone|escape:'html'}
                    </a>
                    {$pchmHours = $pchmHeader.service_hours|pchm_text}
                    {if $pchmHours !== ''}<span class="pchm-mobile-nav-hours">{$pchmHours}</span>{/if}
                </div>
            {/if}
        </div>
    {/if}
{/block}
