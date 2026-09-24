{*
 * PCHMTechnik – Netto/Brutto-Umschalter.
 * Der NOVA-Preis bleibt unverändert (Kundengruppe). Für die jeweils andere Anzeige wird ein zweites Element
 * ausgegeben, das js/custom.js aus dem sichtbaren Preis berechnet – auch nach Variations-/Konfiguratorwechsel.
 *}
{block name='productdetails-price-snippets' prepend}
    {if $Einstellungen.template.pchm_header.price_switch === 'Y'}
        <div class="pchm-price-alt" data-pchm-tax="{$Artikel->fMwSt|default:0}"></div>
    {/if}
{/block}

{block name='productdetails-price-detail-bulk-price-body'}
    {if $Einstellungen.template.pchm_header.price_switch === 'Y'}
        {foreach $Artikel->staffelPreis_arr as $bulkPrice}
            {if $bulkPrice.nAnzahl > 0}
                <tr class="bulk-price-{$bulkPrice.nAnzahl}">
                    <td>{$bulkPrice.nAnzahl}</td>
                    <td>
                        <span class="bulk-price pchm-vat-gross">{$bulkPrice.cPreisLocalized[0]}</span><span class="bulk-price pchm-vat-net">{$bulkPrice.cPreisLocalized[1]}</span><span class="footnote-reference">*</span>
                    </td>
                    {if !empty($bulkPrice.cBasePriceLocalized)}
                        <td class="bulk-base-price">
                            <span class="pchm-vat-gross">{$bulkPrice.cBasePriceLocalized[0]}</span><span class="pchm-vat-net">{$bulkPrice.cBasePriceLocalized[1]}</span>
                        </td>
                    {/if}
                </tr>
            {/if}
        {/foreach}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
