{*
 * NOTICE OF LICENSE
 *
 * @author ScaleDEV <contact@scaledev.fr>
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *}

{if $id_selected_relay}
    <div class="card mt-2 panel">
        <div class="card-header panel-heading">{l s="Mondial Relay | InPost" mod="mondialrelay"}</div>
        <div style="min-height:80px" class="card-body mondialrelay-body">
            <img style="width:80px; margin-right:5%;" src="{$module_path|escape:'htmlall':'UTF-8'}views/img/logo.jpg" class="h-auto mondialrelay_logo"/>
            {if !$already_gen}
                <button data-relay="{$id_selected_relay}" id="generateMrLabel" class="btn btn-primary">{l s="Generate label" mod="mondialrelay"}</button>
                <input type="hidden" id="mondialrelay-action" value="{$action_url}">
                {else}
                <span class="alert alert-info w-75">
                    {l s="Label already generated" mod="mondialrelay"}
                </span>
            {/if}
        </div>
    </div>
{/if}