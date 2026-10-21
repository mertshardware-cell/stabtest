{block name='pchm-product-trust'}
    {$pchmDetails = $Einstellungen.template.pchm_details}
    {if $pchmDetails.trust_box === 'Y'}
        {$pchmTrustLines = []}
        {foreach [1, 2, 3, 4] as $pchmTrustIndex}
            {$pchmTrustText = $pchmDetails["trust{$pchmTrustIndex}"]|default:''|pchm_text}
            {if $pchmTrustText !== ''}{$pchmTrustLines[] = $pchmTrustText}{/if}
        {/foreach}
        {if $pchmTrustLines|count > 0}
            <ul class="pchm-product-trust list-unstyled">
                {foreach $pchmTrustLines as $pchmTrustLine}
                    <li><i class="fas fa-check-circle" aria-hidden="true"></i> {$pchmTrustLine}</li>
                {/foreach}
            </ul>
        {/if}
    {/if}
{/block}
