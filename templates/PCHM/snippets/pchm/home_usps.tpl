{block name='pchm-home-usps'}
    {if $Einstellungen.template.pchm_home.usps === 'Y'}
        {$pchmHeader = $Einstellungen.template.pchm_header}
        {$pchmUspItems = []}
        {foreach [1, 2, 3, 4] as $pchmUspIndex}
            {$pchmUspText = $pchmHeader["usp{$pchmUspIndex}_text"]|default:''|pchm_text}
            {if $pchmUspText !== ''}
                {$pchmUspItems[] = ['icon' => $pchmHeader["usp{$pchmUspIndex}_icon"]|default:'fas fa-check', 'text' => $pchmUspText]}
            {/if}
        {/foreach}
        {if $pchmUspItems|count > 0}
            <section class="pchm-home-section pchm-home-usps">
                {container fluid=true class="{if $Einstellungen.template.theme.left_sidebar === 'Y' && $boxesLeftActive}container-plus-sidebar{/if}"}
                    {row}
                        {foreach $pchmUspItems as $pchmUsp}
                            {col cols=6 lg=3 class="pchm-usp-tile-col"}
                                <div class="pchm-usp-tile">
                                    <span class="pchm-usp-tile-icon"><i class="{$pchmUsp.icon|escape:'html'}" aria-hidden="true"></i></span>
                                    <span class="pchm-usp-tile-text">{$pchmUsp.text}</span>
                                </div>
                            {/col}
                        {/foreach}
                    {/row}
                {/container}
            </section>
        {/if}
    {/if}
{/block}
