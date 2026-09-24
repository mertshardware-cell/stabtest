{*
 * Sticky-Kaufleiste – erscheint, sobald der Kaufbereich aus dem Bild gescrollt ist.
 * Preis wird von js/custom.js aus #product-offer übernommen (inkl. Variations-/Konfiguratorpreis).
 *}
{block name='pchm-sticky-buybar'}
    <div id="pchm-buybar" class="pchm-buybar d-print-none" aria-hidden="true">
        {container class="pchm-buybar-inner"}
            {if !empty($Artikel->getImage(\JTL\Media\Image::SIZE_XS))}
                <img class="pchm-buybar-image" src="{$Artikel->getImage(\JTL\Media\Image::SIZE_XS)}" alt="" width="48" height="48" loading="lazy">
            {/if}
            <div class="pchm-buybar-info">
                <span class="pchm-buybar-name">{$Artikel->cName}</span>
                <span class="pchm-buybar-price" data-pchm-buybar-price></span>
            </div>
            {if $Artikel->bHasKonfig}
                <a href="#product-configurator" class="btn btn-primary pchm-buybar-button" data-pchm-buybar-action="scroll">
                    {lang key='configure'} <i class="fas fa-cogs" aria-hidden="true"></i>
                </a>
            {elseif $Artikel->inWarenkorbLegbar == 1}
                <button type="button" class="btn btn-primary pchm-buybar-button" data-pchm-buybar-action="buy">
                    {lang key='addToCart'} <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                </button>
            {else}
                <a href="#product-offer" class="btn btn-outline-primary pchm-buybar-button" data-pchm-buybar-action="scroll">
                    {lang key='details'}
                </a>
            {/if}
        {/container}
    </div>
{/block}
