{*
 * PCHM – Kopf des JTL-Konfigurators: Daten für Kompatibilitätsprüfung und Gruppen-Navigation,
 * Platz für die 3D-Vorschau (Plugin pchm_konfigurator3d) und ein eigener OPC-Bereich.
 * Die JTL-Formularfelder (item[Gruppe][], item_quantity[Item]) bleiben unverändert –
 * das 3D-Plugin und der JTL-Warenkorb lesen genau diese Felder.
 *}
{block name='pchm-cfg-top'}
    {$pchmKonfig = $Einstellungen.template.pchm_konfigurator}
    {if $pchmKonfig.compat === 'Y' || $pchmKonfig.group_nav === 'Y'}
        {pchm_cfg_data groups=$Artikel->oKonfig_arr assign='pchmCfgData'}
        <script type="application/json" id="pchm-cfg-data">{$pchmCfgData}</script>
    {/if}
    {if $pchmKonfig.group_nav === 'Y'}
        <nav id="pchm-cfg-nav" class="pchm-cfg-nav" aria-label="{pchm_lang key='cfgNav'}" hidden></nav>
    {/if}
    {if $pchmKonfig.mount_3d === 'top'}
        <div class="pchm-cfg-3d" data-pchm-3d></div>
    {/if}
    {opcMountPoint id='pchm_cfg_before_groups' title='PCHM: vor den Konfigurator-Gruppen' inContainer=false}
{/block}
