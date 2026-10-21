{*
 * Countdown – wird von js/custom.js sekündlich aktualisiert.
 * Parameter: end (Unix-Timestamp), variant ('inline' | 'boxed')
 *}
{block name='pchm-countdown'}
    <span class="pchm-countdown pchm-countdown-{$variant|default:'inline'}"
          data-pchm-countdown="{$end}" data-pchm-now="{$smarty.now}" role="timer" aria-live="off">
        <span class="pchm-countdown-unit"><span class="pchm-countdown-value" data-unit="d">--</span><span class="pchm-countdown-label">{pchm_lang key='days'}</span></span>
        <span class="pchm-countdown-unit"><span class="pchm-countdown-value" data-unit="h">--</span><span class="pchm-countdown-label">{pchm_lang key='hours'}</span></span>
        <span class="pchm-countdown-unit"><span class="pchm-countdown-value" data-unit="m">--</span><span class="pchm-countdown-label">{pchm_lang key='minutes'}</span></span>
        <span class="pchm-countdown-unit"><span class="pchm-countdown-value" data-unit="s">--</span><span class="pchm-countdown-label">{pchm_lang key='seconds'}</span></span>
    </span>
{/block}
