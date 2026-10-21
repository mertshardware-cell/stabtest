{block name='pchm-topbar'}
    {$pchmHeader = $Einstellungen.template.pchm_header}
    {if $pchmHeader.price_switch === 'Y'}
        {block name='pchm-topbar-price-switch'}
            <div class="pchm-topbar-switch">
                {include file='snippets/pchm/price_switch.tpl'}
            </div>
        {/block}
    {/if}
    {block name='pchm-topbar-usps'}
        <ul class="pchm-topbar-usps nav">
            {if $pchmHeader.usp_topbar === 'Y'}
                {foreach [1, 2, 3, 4] as $pchmUspIndex}
                    {$pchmUspText = $pchmHeader["usp{$pchmUspIndex}_text"]|default:''|pchm_text}
                    {if $pchmUspText !== ''}
                        <li class="nav-item pchm-usp">
                            {if !empty($pchmHeader["usp{$pchmUspIndex}_icon"])}
                                <i class="{$pchmHeader["usp{$pchmUspIndex}_icon"]|escape:'html'} pchm-usp-icon" aria-hidden="true"></i>
                            {/if}
                            <span class="pchm-usp-text">{$pchmUspText}</span>
                        </li>
                    {/if}
                {/foreach}
            {/if}
            {if $pchmHeader.hotline_topbar === 'Y' && !empty($pchmHeader.phone)}
                <li class="nav-item pchm-hotline">
                    <a href="tel:{$pchmHeader.phone|regex_replace:'/[^0-9+]/':''}" class="pchm-hotline-link">
                        <i class="fas fa-phone-alt" aria-hidden="true"></i>
                        <span class="sr-only">{pchm_lang key='hotline'}:</span>
                        {$pchmHeader.phone|escape:'html'}
                    </a>
                    {$pchmHours = $pchmHeader.service_hours|pchm_text}
                    {if $pchmHours !== ''}
                        <span class="pchm-hotline-hours">({$pchmHours})</span>
                    {/if}
                </li>
            {/if}
        </ul>
    {/block}
{/block}
