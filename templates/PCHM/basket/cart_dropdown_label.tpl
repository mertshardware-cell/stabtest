{*
 * PCHM – Side-Basket.
 * Diese Datei wird auch per AJAX (IO "getBasketItems") gerendert; dort enthält $Einstellungen keine
 * Template-Einstellungen, deshalb {pchm_config}.
 *}
{block name='basket-cart-dropdown-label'}
    {pchm_config assign='pchmConf'}
    {if $pchmConf.pchm_header.side_basket|default:'N' !== 'Y'}
        {$smarty.block.parent}
    {else}
        <li class="cart-icon-dropdown nav-item pchm-cart-trigger{if $WarenkorbArtikelAnzahl != 0} not-empty{/if}">
            {block name='pchm-cart-label-link'}
                <a class="nav-link" href="{get_static_route id='warenkorb.php'}" role="button" data-pchm-offcanvas="cart"
                   aria-controls="pchm-offcanvas-cart" aria-expanded="false" aria-label="{lang key='basket'}">
                    <i class="fas fa-shopping-cart cart-icon-dropdown-icon" aria-hidden="true">
                        {if $WarenkorbArtikelAnzahl >= 1}
                            <span class="fa-sup" title="{$WarenkorbArtikelAnzahl}">{$WarenkorbArtikelAnzahl}</span>
                        {/if}
                    </i>
                    <span class="cart-icon-dropdown-price">{$WarensummeLocalized[0]}</span>
                </a>
            {/block}
            {* Inhalt der Seitenleiste – wird von js/custom.js in #pchm-offcanvas-cart übernommen *}
            <template class="pchm-cart-template">
                {include file='snippets/pchm/sidecart_content.tpl'}
            </template>
        </li>
    {/if}
{/block}
