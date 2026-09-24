{* Countdown-Block mit Parallax-Hintergrund – blendet sich nach Ablauf automatisch aus *}
{block name='pchm-home-countdown'}
    {$pchmHome = $Einstellungen.template.pchm_home}
    {if $pchmHome.countdown === 'Y'}
        {pchm_timestamp value=$pchmHome.countdown_end|default:'' assign='pchmCountdownEnd'}
        {if $pchmCountdownEnd > $smarty.now}
            {$pchmCountdownImage = $pchmHome.countdown_image|default:''|trim}
            {if $pchmCountdownImage !== '' && !preg_match('#^(https?:)?//#', $pchmCountdownImage)}
                {$pchmCountdownImage = "{$ShopURL}/{$pchmCountdownImage|ltrim:'/'}"}
            {/if}
            <section class="pchm-home-section pchm-home-countdown{if $pchmCountdownImage !== ''} has-image{/if}"
                     {if $pchmCountdownImage !== ''}style="--pchm-parallax-image: url('{$pchmCountdownImage|escape:'html'}')"{/if}
                     data-pchm-parallax>
                <div class="pchm-home-countdown-inner">
                    {$pchmCountdownTitle = $pchmHome.countdown_title|pchm_text}
                    {$pchmCountdownText = $pchmHome.countdown_text|pchm_text}
                    {$pchmCountdownButton = $pchmHome.countdown_button|pchm_text}
                    {if $pchmCountdownTitle !== ''}<div class="pchm-home-countdown-title h2">{$pchmCountdownTitle}</div>{/if}
                    {if $pchmCountdownText !== ''}<p class="pchm-home-countdown-text">{$pchmCountdownText}</p>{/if}
                    {include file='snippets/pchm/countdown.tpl' end=$pchmCountdownEnd variant='boxed'}
                    {if $pchmCountdownButton !== '' && !empty($pchmHome.countdown_link)}
                        <a class="btn btn-primary btn-lg pchm-home-countdown-button" href="{$pchmHome.countdown_link|escape:'html'}">
                            {$pchmCountdownButton}
                        </a>
                    {/if}
                </div>
            </section>
        {/if}
    {/if}
{/block}
