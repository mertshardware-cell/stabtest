{*
 * PCHM – Erweiterungen des NOVA-Headers.
 * Hinweis: Child-Templates brauchen in JTL-Shop 5 kein {extends}. Der Shop verkettet diese Datei automatisch
 * mit NOVA/layout/header.tpl; hier werden nur einzelne NOVA-Blöcke ergänzt (append/prepend) oder ersetzt.
 *}

{block name='layout-header-head-resources-crit' append}
    {pchm_css_vars config=$Einstellungen.template}
{/block}

{block name='layout-header-head-resources-preload' append}
    {pchm_font_files config=$Einstellungen.template assign='pchmFontFiles'}
    {foreach $pchmFontFiles as $pchmFontFile}
        <link rel="preload" href="{$currentThemeDirFull}fonts/{$pchmFontFile}" as="font" type="font/woff2" crossorigin/>
    {/foreach}
{/block}

{block name='layout-header-head-resources' append}
    {include file='snippets/pchm/head_script.tpl'}
{/block}

{block name='layout-header-body-tag'}
    {capture name='pchmBodyTag'}{$smarty.block.parent}{/capture}
    {$pchmHeader = $Einstellungen.template.pchm_header}
    {$pchmBodyClasses = "pchm pchm-sticky-{$pchmHeader.sticky|default:'all'} pchm-mm-{$Einstellungen.template.pchm_megamenu.open_mode|default:'hover'}"}
    {if $pchmHeader.side_basket === 'Y'}{$pchmBodyClasses = "{$pchmBodyClasses} pchm-has-sidecart"}{/if}
    {if $pchmHeader.side_account === 'Y'}{$pchmBodyClasses = "{$pchmBodyClasses} pchm-has-sideaccount"}{/if}
    {$smarty.capture.pchmBodyTag|replace:'<body class="':"<body class=\"{$pchmBodyClasses} "}
{/block}

{block name='layout-header-header' prepend}
    {include file='snippets/pchm/announcement.tpl'}
{/block}

{block name='layout-header-header' append}
    {if $nSeitenTyp !== $smarty.const.PAGE_BESTELLVORGANG}
        {include file='snippets/pchm/usp_mobile.tpl'}
    {/if}
{/block}
