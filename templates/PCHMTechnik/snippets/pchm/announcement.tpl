{block name='pchm-announcement'}
    {$pchmHeader = $Einstellungen.template.pchm_header}
    {if $pchmHeader.announcement_active === 'Y' && $nSeitenTyp !== $smarty.const.PAGE_BESTELLVORGANG}
        {$pchmAnnouncementText = $pchmHeader.announcement_text|pchm_text}
        {pchm_timestamp value=$pchmHeader.announcement_countdown assign='pchmAnnouncementEnd'}
        {if $pchmAnnouncementText !== '' && ($pchmAnnouncementEnd === 0 || $pchmAnnouncementEnd > $smarty.now)}
            <div id="pchm-announcement"
                 class="pchm-announcement d-print-none{if $pchmHeader.announcement_dismissible === 'Y'} is-dismissible{/if}"
                 role="region" aria-label="{$pchmAnnouncementText|strip_tags|escape:'html'}">
                <div class="container-fluid container-fluid-xl pchm-announcement-inner">
                    {block name='pchm-announcement-text'}
                        {if !empty($pchmHeader.announcement_link)}
                            <a class="pchm-announcement-text" href="{$pchmHeader.announcement_link|escape:'html'}">{$pchmAnnouncementText}</a>
                        {else}
                            <span class="pchm-announcement-text">{$pchmAnnouncementText}</span>
                        {/if}
                    {/block}
                    {if $pchmAnnouncementEnd > 0}
                        {include file='snippets/pchm/countdown.tpl' end=$pchmAnnouncementEnd variant='inline'}
                    {/if}
                    {if $pchmHeader.announcement_dismissible === 'Y'}
                        <button type="button" class="pchm-announcement-close" data-pchm-dismiss="announcement"
                                aria-label="{pchm_lang key='close'}" title="{pchm_lang key='close'}">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    {/if}
                </div>
            </div>
        {/if}
    {/if}
{/block}
