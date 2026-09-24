{* PCHMTechnik – Vertrauens-Box, Dokumente, Tab-Sprungnavigation und Sticky-Kaufleiste auf der Artikeldetailseite *}

{block name='productdetails-details-info' append}
    {if empty($smarty.get.quickView)}
        {include file='snippets/pchm/product_trust.tpl'}
        {include file='snippets/pchm/product_documents.tpl'}
    {/if}
{/block}

{block name='productdetails-details-include-tabs' prepend}
    {if $Einstellungen.template.pchm_details.tab_jumpnav === 'Y' && empty($smarty.get.quickView)}
        {* wird von js/custom.js aus den vorhandenen Tabs/Abschnitten befüllt *}
        <nav id="pchm-jumpnav" class="pchm-jumpnav d-print-none" aria-label="{pchm_lang key='jumpTo'}" hidden>
            {container class="{if $Einstellungen.template.theme.left_sidebar === 'Y' && $boxesLeftActive}container-plus-sidebar{/if}"}
                <ul class="pchm-jumpnav-list list-unstyled"></ul>
            {/container}
        </nav>
    {/if}
{/block}

{block name='productdetails-details-content-not-quickview' append}
    {if $Einstellungen.template.pchm_details.sticky_buybar === 'Y' && empty($smarty.get.quickView)}
        {include file='snippets/pchm/sticky_buybar.tpl'}
    {/if}
{/block}
