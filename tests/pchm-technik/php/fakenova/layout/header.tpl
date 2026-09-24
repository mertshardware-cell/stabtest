<head>
<style id="criticalCSS">{block name='layout-header-head-resources-crit'}/*crit*/{/block}</style>
{block name='layout-header-head-resources-preload'}<link rel="preload" href="nova.woff2">{/block}
{block name='layout-header-head-resources'}<!--resources-->{/block}
</head>
{block name='layout-header-body-tag'}
    <body class="{if $Einstellungen.template.theme.button_animated === 'Y'}btn-animated{/if}
                 {if $isMobile}is-mobile{/if} is-nova"
          data-page="{$nSeitenTyp}">
{/block}
{block name='layout-header-header'}<header id="jtl-nav-wrapper">NOVA-HEADER</header>{/block}
