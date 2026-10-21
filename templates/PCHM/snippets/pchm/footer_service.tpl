{* Footer-Servicezeile: Kontakt, Zahlungsarten, Versandpartner, freier Inhalt *}
{block name='pchm-footer-service'}
    {$pchmFooter = $Einstellungen.template.pchm_footer}
    {$pchmHeader = $Einstellungen.template.pchm_header}
    {pchm_list value=$pchmFooter.payments|default:'' assign='pchmPayments'}
    {pchm_list value=$pchmFooter.carriers|default:'' assign='pchmCarriers'}
    {if $pchmFooter.contact === 'Y' || $pchmPayments|count > 0 || $pchmCarriers|count > 0 || !empty($pchmFooter.custom_html)}
        <div class="pchm-footer-service">
            {row}
                {if $pchmFooter.contact === 'Y'}
                    {block name='pchm-footer-contact'}
                        {col cols=12 md=6 lg=4 class="pchm-footer-contact"}
                            {$pchmContactTitle = $pchmFooter.contact_title|pchm_text}
                            {if $pchmContactTitle === ''}{pchm_lang key='contact' assign='pchmContactTitle'}{/if}
                            <div class="productlist-filter-headline">{$pchmContactTitle}</div>
                            <ul class="list-unstyled">
                                {if !empty($pchmHeader.phone)}
                                    <li class="pchm-footer-phone">
                                        <i class="fas fa-phone-alt fa-fw" aria-hidden="true"></i>
                                        <a href="tel:{$pchmHeader.phone|regex_replace:'/[^0-9+]/':''}">{$pchmHeader.phone|escape:'html'}</a>
                                        {$pchmHours = $pchmHeader.service_hours|pchm_text}
                                        {if $pchmHours !== ''}<span class="pchm-footer-hours">{$pchmHours}</span>{/if}
                                    </li>
                                {/if}
                                {if !empty($pchmHeader.email)}
                                    <li>
                                        <i class="fas fa-envelope fa-fw" aria-hidden="true"></i>
                                        <a href="mailto:{$pchmHeader.email|escape:'html'}">{$pchmHeader.email|escape:'html'}</a>
                                    </li>
                                {/if}
                                {if !empty($pchmFooter.address)}
                                    <li class="pchm-footer-address">
                                        <i class="fas fa-map-marker-alt fa-fw" aria-hidden="true"></i>
                                        <address>{$pchmFooter.address|escape:'html'|replace:'|':'<br>'}</address>
                                    </li>
                                {/if}
                            </ul>
                        {/col}
                    {/block}
                {/if}
                {if $pchmPayments|count > 0}
                    {block name='pchm-footer-payments'}
                        {col cols=12 md=6 lg=4 class="pchm-footer-payments"}
                            <div class="productlist-filter-headline">{pchm_lang key='paymentMethods'}</div>
                            <ul class="pchm-badges list-unstyled">
                                {foreach $pchmPayments as $pchmPayment}
                                    {include file='snippets/pchm/brand_badge.tpl' key=$pchmPayment}
                                {/foreach}
                            </ul>
                        {/col}
                    {/block}
                {/if}
                {if $pchmCarriers|count > 0}
                    {block name='pchm-footer-carriers'}
                        {col cols=12 md=6 lg=4 class="pchm-footer-carriers"}
                            <div class="productlist-filter-headline">{pchm_lang key='shippingPartners'}</div>
                            <ul class="pchm-badges list-unstyled">
                                {foreach $pchmCarriers as $pchmCarrier}
                                    {include file='snippets/pchm/brand_badge.tpl' key=$pchmCarrier}
                                {/foreach}
                            </ul>
                        {/col}
                    {/block}
                {/if}
            {/row}
            {if !empty($pchmFooter.custom_html)}
                {block name='pchm-footer-custom-html'}
                    <div class="pchm-footer-custom">{$pchmFooter.custom_html}</div>
                {/block}
            {/if}
        </div>
    {/if}
{/block}
