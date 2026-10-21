{* Zahlungs-/Versand-Badge. Parameter: key (z. B. paypal, visa, klarna, dhl …). Unbekannte Werte werden als Text angezeigt. *}
{block name='pchm-brand-badge'}
    {$pchmBadgeKey = $key|lower|regex_replace:'/[^a-z0-9]/':''}
    {pchm_lang key='badgePrepayment' assign='pchmLblPrepayment'}
    {pchm_lang key='badgeInvoice' assign='pchmLblInvoice'}
    {pchm_lang key='badgeFinancing' assign='pchmLblFinancing'}
    {pchm_lang key='badgeCash' assign='pchmLblCash'}
    {pchm_lang key='badgeFreight' assign='pchmLblFreight'}
    {$pchmBadges = [
        'paypal'      => ['icon' => 'fab fa-cc-paypal',      'label' => 'PayPal'],
        'visa'        => ['icon' => 'fab fa-cc-visa',        'label' => 'Visa'],
        'mastercard'  => ['icon' => 'fab fa-cc-mastercard',  'label' => 'Mastercard'],
        'amex'        => ['icon' => 'fab fa-cc-amex',        'label' => 'American Express'],
        'applepay'    => ['icon' => 'fab fa-cc-apple-pay',   'label' => 'Apple Pay'],
        'googlepay'   => ['icon' => 'fab fa-google-pay',     'label' => 'Google Pay'],
        'amazonpay'   => ['icon' => 'fab fa-cc-amazon-pay',  'label' => 'Amazon Pay'],
        'klarna'      => ['icon' => '',                      'label' => 'Klarna'],
        'sepa'        => ['icon' => '',                      'label' => 'SEPA'],
        'sofort'      => ['icon' => '',                      'label' => 'Sofort'],
        'giropay'     => ['icon' => '',                      'label' => 'giropay'],
        'prepayment'  => ['icon' => 'fas fa-university',     'label' => $pchmLblPrepayment],
        'vorkasse'    => ['icon' => 'fas fa-university',     'label' => $pchmLblPrepayment],
        'invoice'     => ['icon' => 'fas fa-file-invoice',   'label' => $pchmLblInvoice],
        'rechnung'    => ['icon' => 'fas fa-file-invoice',   'label' => $pchmLblInvoice],
        'financing'   => ['icon' => 'fas fa-percent',        'label' => $pchmLblFinancing],
        'cash'        => ['icon' => 'fas fa-money-bill-wave','label' => $pchmLblCash],
        'dhl'         => ['icon' => 'fab fa-dhl',            'label' => 'DHL'],
        'ups'         => ['icon' => 'fab fa-ups',            'label' => 'UPS'],
        'fedex'       => ['icon' => 'fab fa-fedex',          'label' => 'FedEx'],
        'dpd'         => ['icon' => '',                      'label' => 'DPD'],
        'gls'         => ['icon' => '',                      'label' => 'GLS'],
        'hermes'      => ['icon' => '',                      'label' => 'Hermes'],
        'go'          => ['icon' => '',                      'label' => 'GO! Express'],
        'spedition'   => ['icon' => 'fas fa-truck-moving',   'label' => $pchmLblFreight]
    ]}
    {if isset($pchmBadges[$pchmBadgeKey])}
        {$pchmBadge = $pchmBadges[$pchmBadgeKey]}
    {else}
        {$pchmBadge = ['icon' => '', 'label' => $key]}
    {/if}
    <li class="pchm-badge pchm-badge-{$pchmBadgeKey}" title="{$pchmBadge.label|escape:'html'}">
        {if $pchmBadge.icon !== ''}
            <i class="{$pchmBadge.icon} fa-2x" aria-hidden="true"></i><span class="sr-only">{$pchmBadge.label|escape:'html'}</span>
        {else}
            <span class="pchm-badge-text">{$pchmBadge.label|escape:'html'}</span>
        {/if}
    </li>
{/block}
