{* PCHM – Neuigkeiten-Glocke vor dem Konto-Icon *}
{block name='layout-header-nav-icons-include-header-shop-nav-account' prepend}
    {if $Einstellungen.template.pchm_header.news_bell === 'Y'}
        {pchm_news limit=$Einstellungen.template.pchm_header.news_bell_count|default:5 assign='pchmNews'}
        {if $pchmNews|count > 0}
            {$pchmNewsLatest = $pchmNews[0]->getDateValidFrom()->getTimestamp()}
            <li class="nav-item pchm-news-bell">
                <a class="nav-link" href="#pchm-offcanvas-news" role="button" data-pchm-offcanvas="news"
                   data-pchm-news-latest="{$pchmNewsLatest}"
                   aria-controls="pchm-offcanvas-news" aria-expanded="false"
                   aria-label="{pchm_lang key='news'}" title="{pchm_lang key='news'}">
                    <i class="fas fa-bell" aria-hidden="true"><span class="fa-sup pchm-news-dot d-none" aria-hidden="true"></span></i>
                </a>
            </li>
        {/if}
    {/if}
{/block}
