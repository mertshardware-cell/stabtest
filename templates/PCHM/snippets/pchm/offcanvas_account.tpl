{block name='pchm-offcanvas-account-content'}
    {if JTL\Session\Frontend::getCustomer()->getID() === 0}
        {block name='pchm-offcanvas-account-login'}
            {form action="{get_static_route id='jtl.php' secure=true}" method="post" class="jtl-validate pchm-account-login" slide=true}
                <fieldset>
                    <legend class="pchm-offcanvas-subtitle">{lang key='loginForRegisteredCustomers' section='checkout'}</legend>
                    {formgroup label-for="pchm_email_quick" label={lang key='emailadress'}}
                        {input type="email" name="email" id="pchm_email_quick" required=true placeholder=" " autocomplete="email"}
                    {/formgroup}
                    {formgroup label-for="pchm_password_quick" label={lang key='password'}}
                        {input type="password" name="passwort" id="pchm_password_quick" required=true placeholder=" "
                            autocomplete="current-password"}
                    {/formgroup}
                    {if isset($showLoginCaptcha) && $showLoginCaptcha}
                        {formgroup class="simple-captcha-wrapper"}
                            {captchaMarkup getBody=true}
                        {/formgroup}
                    {/if}
                    {input type="hidden" name="login" value="1"}
                    {if !empty($oRedirect->cURL)}
                        {foreach $oRedirect->oParameter_arr as $oParameter}
                            {input type="hidden" name=$oParameter->Name value=$oParameter->Wert}
                        {/foreach}
                        {input type="hidden" name="r" value=$oRedirect->nRedirect}
                        {input type="hidden" name="cURL" value=$oRedirect->cURL}
                    {/if}
                    {button type="submit" block=true variant="primary"}{lang key='login'}{/button}
                </fieldset>
            {/form}
            <p class="pchm-account-forgot">
                {link href="{get_static_route id='pass.php'}" rel="nofollow"}{lang key='forgotPassword'}{/link}
            </p>
            <hr>
            <p class="pchm-offcanvas-subtitle">{lang key='newHere'}</p>
            {link href="{get_static_route id='registrieren.php'}" rel="nofollow" class="btn btn-outline-primary btn-block"}
                {pchm_lang key='register'}
            {/link}
        {/block}
    {else}
        {block name='pchm-offcanvas-account-links'}
            {get_static_route id='jtl.php' secure=true assign='pchmAccountURL'}
            <p class="pchm-offcanvas-subtitle">
                {lang key='hello'} {JTL\Session\Frontend::getCustomer()->cVorname|escape:'html'} {JTL\Session\Frontend::getCustomer()->cNachname|escape:'html'}
            </p>
            <ul class="pchm-account-links list-unstyled">
                <li><a href="{$pchmAccountURL}"><i class="fas fa-tachometer-alt fa-fw" aria-hidden="true"></i> {lang key='myAccount'}</a></li>
                <li><a href="{$pchmAccountURL}?bestellungen=1"><i class="fas fa-box fa-fw" aria-hidden="true"></i> {lang key='myOrders'}</a></li>
                <li><a href="{$pchmAccountURL}?editRechnungsadresse=1"><i class="fas fa-id-card fa-fw" aria-hidden="true"></i> {lang key='myPersonalData'}</a></li>
                <li><a href="{$pchmAccountURL}?editLieferadresse=1"><i class="fas fa-truck fa-fw" aria-hidden="true"></i> {lang key='myShippingAddresses'}</a></li>
                {if $Einstellungen.global.global_rma_enabled|default:'N' === 'Y'}
                    <li><a href="{$pchmAccountURL}?returns=1"><i class="fas fa-undo fa-fw" aria-hidden="true"></i> {lang key='myReturns' section='rma'}</a></li>
                {/if}
                {if $Einstellungen.global.global_wunschliste_anzeigen === 'Y'}
                    <li><a href="{$pchmAccountURL}#my-wishlists"><i class="fas fa-heart fa-fw" aria-hidden="true"></i> {lang key='myWishlists'}</a></li>
                {/if}
            </ul>
            <hr>
            {link href="{$pchmAccountURL}?logout=1" class="btn btn-outline-secondary btn-block account-icon-dropdown-logout"}
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i> {lang key='logOut'}
            {/link}
        {/block}
    {/if}
{/block}
