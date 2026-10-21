{* PCHM – Konto/Login als Seitenleiste statt Dropdown (Inhalt: snippets/pchm/offcanvas_account.tpl) *}
{block name='layout-header-shop-nav-account'}
    {if $Einstellungen.template.pchm_header.side_account === 'Y'}
        {$pchmLoggedIn = JTL\Session\Frontend::getCustomer()->getID() > 0}
        <li class="nav-item account-icon-dropdown pchm-account-trigger">
            <a class="nav-link" href="{get_static_route id='jtl.php' secure=true}" role="button"
               data-pchm-offcanvas="account" aria-controls="pchm-offcanvas-account" aria-expanded="false"
               aria-label="{lang key='myAccount'}" title="{lang key='myAccount'}">
                <span class="fas {if $pchmLoggedIn}fa-user-check{else}fa-user{/if}" aria-hidden="true"></span>
            </a>
        </li>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
