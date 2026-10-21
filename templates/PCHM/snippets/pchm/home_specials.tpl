{* Preisaktionen-Slider (Sonderangebote/Top-Angebote/Neuheiten/Bestseller) mit optionalem Countdown je Artikel *}
{block name='pchm-home-specials'}
    {$pchmHome = $Einstellungen.template.pchm_home}
    {if $pchmHome.specials === 'Y'}
        {$pchmSpecialsCount = $pchmHome.specials_count|default:12|intval}
        {get_product_list kSuchspecial=$pchmHome.specials_source|default:2|intval nLimit=$pchmSpecialsCount cAssign='pchmSpecials'}
        {if !empty($pchmSpecials)}
            {$pchmSpecialsTitle = $pchmHome.specials_title|pchm_text}
            <section class="pchm-home-section pchm-home-specials">
                {container class="product-slider-wrapper {if $Einstellungen.template.theme.left_sidebar === 'Y' && $boxesLeftActive}container-plus-sidebar{/if}" fluid=true}
                    {include file='snippets/product_slider.tpl'
                        productlist=$pchmSpecials
                        title=$pchmSpecialsTitle
                        hideOverlays=true
                        titleContainer=true
                        class='pchm-specials-slider'
                        id='pchm-specials-slider'
                        pchmDealCountdown=($pchmHome.specials_countdown === 'Y')}
                {/container}
            </section>
        {/if}
    {/if}
{/block}
