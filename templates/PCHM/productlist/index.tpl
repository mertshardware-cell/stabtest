{* PCHM – Artikel pro Zeile je Gerät (Galerie) und Kategoriebeschreibung unter der Artikelliste *}
{block name='productlist-index-include-productlist-header-grid-gallery'}
    {$smarty.block.parent}
    {$pchmList = $Einstellungen.template.pchm_list}
    {$pchmColsMobile = $pchmList.cols_mobile|default:2|intval}
    {$pchmColsTablet = $pchmList.cols_tablet|default:3|intval}
    {$pchmColsDesktop = $pchmList.cols_desktop|default:4|intval}
    {if $pchmColsMobile > 0 && $pchmColsTablet > 0 && $pchmColsDesktop > 0}
        {assign var=grid value=12 / $pchmColsMobile}
        {assign var=gridsm value=12 / $pchmColsMobile}
        {assign var=gridmd value=12 / $pchmColsTablet}
        {assign var=gridxl value=12 / $pchmColsDesktop}
    {/if}
{/block}

{block name='productlist-index-include-productlist-footer' append}
    {if $Einstellungen.template.pchm_list.description_position === 'bottom'
        && $Einstellungen.navigationsfilter.kategorie_beschreibung_anzeigen === 'Y'
        && isset($oNavigationsinfo) && $oNavigationsinfo->getCategory() !== null
        && $oNavigationsinfo->getCategory()->getDescription()|strlen > 0
        && (!isset($Suchergebnisse) || $Suchergebnisse->getPages()->getCurrentPage() <= 1)}
        <div class="desc pchm-category-description pchm-category-description-bottom{if $Einstellungen.template.pchm_list.description_collapse === 'Y'} pchm-foldable{/if}">
            <p>{$oNavigationsinfo->getCategory()->getDescription()}</p>
        </div>
    {/if}
{/block}
