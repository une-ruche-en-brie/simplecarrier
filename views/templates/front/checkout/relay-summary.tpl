{*
 * NOTICE OF LICENSE
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *}

<div class="col-md-12 clearfix">

  <div class="pull-left">

    {if $deliveryMode == "MED"}
      <h4>{l s='Your selected Point Relais® :' mod='mondialrelay'}</h4>
    {elseif $deliveryMode == "24R"}
      <h4>{l s='Your selected Locker / Point Relais® :' mod='mondialrelay'}</h4>
    {elseif $deliveryMode == "APM"}
      <h4>{l s='Your selected Locker :' mod='mondialrelay'}</h4>
    {/if}

    <div class="col-md-12">
      {$selectedRelay->selected_relay_adr1|escape:'htmlall':'UTF-8'} {$selectedRelay->selected_relay_adr2|escape:'htmlall':'UTF-8'}
    </div>

    <div class="col-md-12">
      {$selectedRelay->selected_relay_adr3|escape:'htmlall':'UTF-8'} {$selectedRelay->selected_relay_adr4|escape:'htmlall':'UTF-8'}
    </div>

    <div class="col-md-12">
      {$selectedRelay->selected_relay_postcode|escape:'htmlall':'UTF-8'} {$selectedRelay->selected_relay_city|escape:'htmlall':'UTF-8'}
    </div>

  </div>

  {if $deliveryMode == "MED"}
    <button id="mondialrelay_change-relay" type="button" class="btn btn-primary mondialrelay_change-relay">
      <i class='icon-pencil'></i> {l s='Change Point Relais®' mod='mondialrelay'}
    </button>
  {elseif $deliveryMode == "24R"}
    <button id="mondialrelay_change-relay" type="button" class="btn btn-primary mondialrelay_change-relay">
      <i class='icon-pencil'></i> {l s='Change Locker / Point Relais®' mod='mondialrelay'}
    </button>
  {elseif $deliveryMode == "APM"}
    <button id="mondialrelay_change-relay" type="button" class="btn btn-primary mondialrelay_change-relay">
      <i class='icon-pencil'></i> {l s='Change Locker' mod='mondialrelay'}
    </button>
  {/if}

</div>