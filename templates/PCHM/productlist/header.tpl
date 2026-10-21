{* PCHM – Kategoriebeschreibung oben/unten/aus, Unterkategorien ein-/ausblendbar *}
{block name='productlist-header-description-category'}
    {$pchmDescPosition = $Einstellungen.template.pchm_list.description_position|default:'top'}
    {if $pchmDescPosition === 'top'}
        <div class="pchm-category-description{if $Einstellungen.template.pchm_list.description_collapse === 'Y'} pchm-foldable{/if}">
            {$smarty.block.parent}
        </div>
    {/if}
{/block}

{block name='productlist-header-subcategories'}
    {if $Einstellungen.template.pchm_list.subcategories !== 'N'}
        {$smarty.block.parent}
    {/if}
{/block}
