{* PCHM – einstellbare Tiefe des Megamenüs + „Alle anzeigen“-Link auf dem Desktop *}
{block name='snippets-categories-mega-recursive-max-subsub-items'}
    {$pchmDepth = $Einstellungen.template.pchm_megamenu.depth|default:2|intval}
    {if $pchmDepth < 1}{$pchmDepth = 2}{/if}
    {$max_subsub_items="{if $isMobile}5{else}{$pchmDepth}{/if}"}
{/block}

{block name='snippets-categories-mega-recursive-child-categories' append}
    {if $firstChild && $Einstellungen.template.pchm_megamenu.show_all_link === 'Y'}
        <li class="nav-item pchm-mm-show-all d-none d-lg-block">
            {link href=$mainCategory->getURL() class="nav-link"}
                {pchm_lang key='showAll'} <i class="fas fa-angle-right" aria-hidden="true"></i>
            {/link}
        </li>
    {/if}
{/block}
