{* Kompakte, rotierende USP-Leiste für Smartphone/Tablet (die Topbar ist dort ausgeblendet) *}
{block name='pchm-usp-mobile'}
    {$pchmHeader = $Einstellungen.template.pchm_header}
    {if $pchmHeader.usp_topbar === 'Y'}
        {$pchmUspItems = []}
        {foreach [1, 2, 3, 4] as $pchmUspIndex}
            {$pchmUspText = $pchmHeader["usp{$pchmUspIndex}_text"]|default:''|pchm_text}
            {if $pchmUspText !== ''}
                {$pchmUspItems[] = ['icon' => $pchmHeader["usp{$pchmUspIndex}_icon"]|default:'', 'text' => $pchmUspText]}
            {/if}
        {/foreach}
        {if $pchmUspItems|count > 0}
            <div class="pchm-usp-mobile d-lg-none d-print-none" data-pchm-rotate="4000">
                {foreach $pchmUspItems as $pchmUsp}
                    <div class="pchm-usp-mobile-item{if $pchmUsp@first} is-active{/if}">
                        {if $pchmUsp.icon !== ''}<i class="{$pchmUsp.icon|escape:'html'}" aria-hidden="true"></i>{/if}
                        <span>{$pchmUsp.text}</span>
                    </div>
                {/foreach}
            </div>
        {/if}
    {/if}
{/block}
