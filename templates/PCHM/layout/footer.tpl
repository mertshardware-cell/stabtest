{* PCHM – Footer-Erweiterungen (Kontakt, Zahlungs-/Versandarten, Social Media, Seitenleisten) *}

{block name='layout-footer-boxes' append}
    {include file='snippets/pchm/footer_service.tpl'}
{/block}

{block name='layout-footer-additional' append}
    {$pchmSocialExtra = $Einstellungen.template.pchm_footer.social_extra|default:''}
    {if $pchmSocialExtra !== ''}
        {pchm_list value=$pchmSocialExtra separator='||' assign='pchmSocialItems'}
        {row class="footer-social-media pchm-social-extra"}
            {col cols=12 class="footer-additional-wrapper col-auto mx-auto"}
                <ul class="list-unstyled">
                    {foreach $pchmSocialItems as $pchmSocialItem}
                        {$pchmSocialParts = explode('=', $pchmSocialItem, 2)}
                        {if $pchmSocialParts|count === 2}
                            {$pchmSocialName = trim($pchmSocialParts[0])}
                            {$pchmSocialKey = $pchmSocialName|lower|regex_replace:'/[^a-z0-9]/':''}
                            {$pchmSocialIcon = 'fas fa-link'}
                            {if $pchmSocialKey === 'discord'}{$pchmSocialIcon = 'fab fa-discord'}
                            {elseif $pchmSocialKey === 'twitch'}{$pchmSocialIcon = 'fab fa-twitch'}
                            {elseif $pchmSocialKey === 'x' || $pchmSocialKey === 'twitter'}{$pchmSocialIcon = 'fab fa-twitter'}
                            {elseif $pchmSocialKey === 'whatsapp'}{$pchmSocialIcon = 'fab fa-whatsapp'}
                            {elseif $pchmSocialKey === 'reddit'}{$pchmSocialIcon = 'fab fa-reddit-alien'}
                            {elseif $pchmSocialKey === 'steam'}{$pchmSocialIcon = 'fab fa-steam'}
                            {elseif $pchmSocialKey === 'telegram'}{$pchmSocialIcon = 'fab fa-telegram-plane'}
                            {elseif $pchmSocialKey === 'facebook'}{$pchmSocialIcon = 'fab fa-facebook-f'}
                            {elseif $pchmSocialKey === 'instagram'}{$pchmSocialIcon = 'fab fa-instagram'}
                            {elseif $pchmSocialKey === 'youtube'}{$pchmSocialIcon = 'fab fa-youtube'}
                            {elseif $pchmSocialKey === 'tiktok'}{$pchmSocialIcon = 'fab fa-tiktok'}
                            {/if}
                            <li>
                                <a href="{trim($pchmSocialParts[1])|escape:'html'}" class="btn-icon-secondary btn btn-sm"
                                   target="_blank" rel="noopener" title="{$pchmSocialName|escape:'html'}"
                                   aria-label="{lang key='visit_us_on' section='aria' printf=$pchmSocialName|escape:'html'}">
                                    <i class="{$pchmSocialIcon} fa-fw fa-lg" aria-hidden="true"></i>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/col}
        {/row}
    {/if}
{/block}

{block name='layout-footer-io-path' prepend}
    {if !$bExclusive}
        {include file='snippets/pchm/offcanvas.tpl'}
    {/if}
{/block}

{block name='layout-footer-js' append}
    {* Konfigurator-Skript nur auf Artikeln mit JTL-Konfigurator (JTL-Doku: eigene Skripte im Footer, defer) *}
    {if isset($Artikel) && !empty($Artikel->bHasKonfig) && $nSeitenTyp === $smarty.const.PAGE_ARTIKEL}
        <script defer src="{$ShopURL}/{$currentTemplateDir}js/pchm-konfigurator.js?v={$nTemplateVersion}"></script>
    {/if}
{/block}
