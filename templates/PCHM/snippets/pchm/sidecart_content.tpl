{block name='pchm-sidecart-content'}
    {block name='pchm-sidecart-free-shipping'}
        {pchm_free_shipping assign='pchmFree'}
        {if $pchmFree.active && $WarenkorbArtikelAnzahl > 0}
            <div class="pchm-free-shipping{if $pchmFree.reached} is-reached{/if}">
                <div class="pchm-free-shipping-text">
                    <i class="fas fa-truck" aria-hidden="true"></i>
                    {if $pchmFree.reached}
                        {pchm_lang key='freeShippingDone'}
                    {else}
                        {pchm_lang key='freeShippingLeft' printf=$pchmFree.missingLocalized}
                    {/if}
                </div>
                <div class="pchm-free-shipping-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"
                     aria-valuenow="{$pchmFree.percent}">
                    <span style="width: {$pchmFree.percent}%"></span>
                </div>
            </div>
        {/if}
    {/block}
    {block name='pchm-sidecart-include-cart-dropdown'}
        {include file='basket/cart_dropdown.tpl'}
    {/block}
{/block}
