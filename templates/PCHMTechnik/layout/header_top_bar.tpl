{* PCHMTechnik – USP-Leiste, Hotline und Netto/Brutto-Umschalter in der NOVA-Topbar *}
{block name='layout-header-top-bar' append}
    {if $nSeitenTyp !== $smarty.const.PAGE_BESTELLVORGANG}
        {include file='snippets/pchm/topbar.tpl'}
    {/if}
{/block}
