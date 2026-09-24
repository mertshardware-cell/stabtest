{* Dokumente (Mediendateien vom Typ PDF/Sonstiges) direkt neben dem Kaufbereich *}
{block name='pchm-product-documents'}
    {if $Einstellungen.template.pchm_details.documents === 'Y' && !empty($Artikel->oMedienDatei_arr)}
        {$pchmDocuments = []}
        {foreach $Artikel->oMedienDatei_arr as $pchmMedia}
            {if ($pchmMedia->nMedienTyp == 5 || $pchmMedia->nMedienTyp == 4)
                && (!empty($pchmMedia->cPfad) || !empty($pchmMedia->cURL))}
                {$pchmDocuments[] = $pchmMedia}
            {/if}
        {/foreach}
        {if $pchmDocuments|count > 0}
            <div class="pchm-product-documents">
                <div class="pchm-product-documents-title">
                    <i class="fas fa-file-alt" aria-hidden="true"></i> {pchm_lang key='documents'}
                </div>
                <ul class="list-unstyled">
                    {foreach $pchmDocuments as $pchmDocument}
                        {if !empty($pchmDocument->cPfad)}
                            {$pchmDocumentUrl = "{$ShopURL}/{$smarty.const.PFAD_MEDIAFILES}{$pchmDocument->cPfad}"}
                        {else}
                            {$pchmDocumentUrl = $pchmDocument->cURL}
                        {/if}
                        <li>
                            <a href="{$pchmDocumentUrl}" target="_blank" rel="noopener">
                                <i class="fas {if $pchmDocument->nMedienTyp == 5}fa-file-pdf{else}fa-download{/if} fa-fw" aria-hidden="true"></i>
                                {$pchmDocument->cName}
                            </a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        {/if}
    {/if}
{/block}
