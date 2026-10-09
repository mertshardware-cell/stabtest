{*
 * PCHM – Startseite mit frei sortierbaren Bereichen.
 * "nova" steht für den unveränderten NOVA-Inhalt (Auswahlassistent, Startseiten-Boxen, News).
 * OPC-Bereiche (OnPage Composer) von NOVA bleiben vollständig erhalten.
 *}
{block name='page-index'}
    {pchm_list value=$Einstellungen.template.pchm_home.order|default:'nova' assign='pchmHomeOrder'}
    {if !in_array('nova', $pchmHomeOrder)}
        {$pchmHomeOrder[] = 'nova'}
    {/if}
    {foreach $pchmHomeOrder as $pchmHomeSection}
        {if $pchmHomeSection === 'nova'}
            {$smarty.block.parent}
        {elseif in_array($pchmHomeSection, ['usps', 'specials', 'categories', 'countdown', 'brands'])}
            {include file="snippets/pchm/home_{$pchmHomeSection}.tpl"}
        {/if}
    {/foreach}
{/block}
