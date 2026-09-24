{* Seitenleisten (Warenkorb, Konto, Neuigkeiten) – geöffnet über [data-pchm-offcanvas="…"] *}
{block name='pchm-offcanvas'}
    {$pchmHeader = $Einstellungen.template.pchm_header}
    {if $pchmHeader.side_basket === 'Y'}
        {block name='pchm-offcanvas-cart'}
            <div id="pchm-offcanvas-cart" class="pchm-offcanvas" tabindex="-1" role="dialog" aria-modal="true"
                 aria-labelledby="pchm-offcanvas-cart-title" aria-hidden="true">
                <div class="pchm-offcanvas-header">
                    <span id="pchm-offcanvas-cart-title" class="pchm-offcanvas-title">
                        <i class="fas fa-shopping-cart" aria-hidden="true"></i> {pchm_lang key='cart'}
                    </span>
                    <button type="button" class="pchm-offcanvas-close" data-pchm-close aria-label="{pchm_lang key='close'}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="pchm-offcanvas-body" data-pchm-cart-body></div>
            </div>
        {/block}
    {/if}

    {if $pchmHeader.side_account === 'Y'}
        {block name='pchm-offcanvas-account'}
            <div id="pchm-offcanvas-account" class="pchm-offcanvas" tabindex="-1" role="dialog" aria-modal="true"
                 aria-labelledby="pchm-offcanvas-account-title" aria-hidden="true">
                <div class="pchm-offcanvas-header">
                    <span id="pchm-offcanvas-account-title" class="pchm-offcanvas-title">
                        <i class="fas fa-user" aria-hidden="true"></i> {pchm_lang key='account'}
                    </span>
                    <button type="button" class="pchm-offcanvas-close" data-pchm-close aria-label="{pchm_lang key='close'}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="pchm-offcanvas-body">
                    {include file='snippets/pchm/offcanvas_account.tpl'}
                </div>
            </div>
        {/block}
    {/if}

    {if $pchmHeader.news_bell === 'Y'}
        {pchm_news limit=$pchmHeader.news_bell_count|default:5 assign='pchmNews'}
    {/if}
    {if $pchmHeader.news_bell === 'Y' && $pchmNews|count > 0}
        {block name='pchm-offcanvas-news'}
            <div id="pchm-offcanvas-news" class="pchm-offcanvas" tabindex="-1" role="dialog" aria-modal="true"
                 aria-labelledby="pchm-offcanvas-news-title" aria-hidden="true">
                <div class="pchm-offcanvas-header">
                    <span id="pchm-offcanvas-news-title" class="pchm-offcanvas-title">
                        <i class="fas fa-bell" aria-hidden="true"></i> {pchm_lang key='news'}
                    </span>
                    <button type="button" class="pchm-offcanvas-close" data-pchm-close aria-label="{pchm_lang key='close'}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="pchm-offcanvas-body">
                    <ul class="pchm-news-list list-unstyled">
                        {foreach $pchmNews as $pchmNewsItem}
                            <li class="pchm-news-item">
                                <a href="{$pchmNewsItem->getURL()}" class="pchm-news-link">
                                    {if !empty($pchmNewsItem->getPreviewImage())}
                                        {include file='snippets/image.tpl' item=$pchmNewsItem square=false srcSize='xs'
                                            sizes='80px' class='pchm-news-image' alt=$pchmNewsItem->getTitle()|escape:'html'}
                                    {/if}
                                    <span class="pchm-news-text">
                                        <span class="pchm-news-date">{$pchmNewsItem->getDateValidFrom()->format('d.m.Y')}</span>
                                        <span class="pchm-news-title">{$pchmNewsItem->getTitle()}</span>
                                        {if !empty($pchmNewsItem->getPreview())}
                                            <span class="pchm-news-preview">{$pchmNewsItem->getPreview()|strip_tags|truncate:110:'…'}</span>
                                        {/if}
                                    </span>
                                </a>
                            </li>
                        {/foreach}
                    </ul>
                    {link href="{get_static_route id='news.php'}" class="btn btn-outline-primary btn-block"}
                        {pchm_lang key='newsAll'}
                    {/link}
                </div>
            </div>
        {/block}
    {/if}

    <div class="pchm-offcanvas-backdrop" data-pchm-close></div>
{/block}
