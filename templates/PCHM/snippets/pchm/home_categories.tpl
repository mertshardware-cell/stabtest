{block name='pchm-home-categories'}
    {$pchmHome = $Einstellungen.template.pchm_home}
    {if $pchmHome.categories === 'Y'}
        {pchm_categories ids=$pchmHome.categories_ids|default:'' limit=$pchmHome.categories_count|default:8|intval assign='pchmCategories'}
        {if $pchmCategories|count > 0}
            <section class="pchm-home-section pchm-home-categories">
                {container fluid=true class="{if $Einstellungen.template.theme.left_sidebar === 'Y' && $boxesLeftActive}container-plus-sidebar{/if}"}
                    {$pchmCategoriesTitle = $pchmHome.categories_title|pchm_text}
                    {if $pchmCategoriesTitle !== ''}
                        <div class="hr-sect h2">{$pchmCategoriesTitle}</div>
                    {/if}
                    {row class="pchm-category-tiles"}
                        {foreach $pchmCategories as $pchmCategory}
                            {col cols=6 md=4 lg=3 class="pchm-category-tile-col"}
                                {link href=$pchmCategory->getURL() class="pchm-category-tile" title=$pchmCategory->getName()|escape:'html'}
                                    <span class="pchm-category-tile-image">
                                        {include file='snippets/image.tpl'
                                            item=$pchmCategory
                                            square=false
                                            srcSize='sm'
                                            sizes='(min-width: 992px) 25vw, (min-width: 768px) 33vw, 50vw'
                                            alt=$pchmCategory->getName()|escape:'html'}
                                    </span>
                                    <span class="pchm-category-tile-name">{$pchmCategory->getShortName()}</span>
                                {/link}
                            {/col}
                        {/foreach}
                    {/row}
                {/container}
            </section>
        {/if}
    {/if}
{/block}
