{* Hersteller-Slider (Logos aus der JTL-Herstellerverwaltung) *}
{block name='pchm-home-brands'}
    {$pchmHome = $Einstellungen.template.pchm_home}
    {if $pchmHome.brands === 'Y'}
        {get_manufacturers assign='pchmManufacturers' limit=$pchmHome.brands_count|default:24|intval}
        {if !empty($pchmManufacturers)}
            <section class="pchm-home-section pchm-home-brands{if $pchmHome.brands_grayscale === 'Y'} is-grayscale{/if}">
                {container fluid=true class="{if $Einstellungen.template.theme.left_sidebar === 'Y' && $boxesLeftActive}container-plus-sidebar{/if}"}
                    {$pchmBrandsTitle = $pchmHome.brands_title|pchm_text}
                    {if $pchmBrandsTitle !== ''}
                        <div class="hr-sect h2">{$pchmBrandsTitle}</div>
                    {/if}
                    <div class="pchm-brand-slider" data-pchm-marquee>
                        <ul class="pchm-brand-track list-unstyled">
                            {foreach $pchmManufacturers as $pchmManufacturer}
                                <li class="pchm-brand-item">
                                    {link href=$pchmManufacturer->getURL() title=$pchmManufacturer->getName()|escape:'html' class="pchm-brand-link"}
                                        {if !empty($pchmManufacturer->getImage(\JTL\Media\Image::SIZE_XS))
                                            && strpos($pchmManufacturer->getImage(\JTL\Media\Image::SIZE_XS), 'keinBild') === false}
                                            {include file='snippets/image.tpl'
                                                item=$pchmManufacturer
                                                square=false
                                                fluid=false
                                                srcSize='sm'
                                                sizes='160px'
                                                class='pchm-brand-logo'
                                                alt=$pchmManufacturer->getName()|escape:'html'}
                                        {else}
                                            <span class="pchm-brand-name">{$pchmManufacturer->getName()}</span>
                                        {/if}
                                    {/link}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/container}
            </section>
        {/if}
    {/if}
{/block}
