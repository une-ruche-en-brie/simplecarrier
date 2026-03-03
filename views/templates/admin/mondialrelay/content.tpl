{*
 * NOTICE OF LICENSE
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *}

 {* Note : This file was copied to views/templates/admin/mondialrelay_advanced_settings/content.tpl *}
{block name="mondialrelay_content-prepend"}
  {if $with_mondialrelay_header}
    <div id="mondialrelay_header" class="panel clearfix">
      <div>
        <img src="{$module_path|escape:'htmlall':'UTF-8'}views/img/logo.jpg" class="w-100 h-auto pull-left mondialrelay_logo"/>
      </div>
      <div>
        <p>
          {l s='[1] To accompany you, we\'re pulling out all the stops!  Mondial Relay - InPost, the leader in out-of-home delivery in Europe [/1]' mod='mondialrelay' tags=['<strong>']}<br/>
          {l s='Mondial Relay - InPost, everyone knows. It is parcel delivery specialist for over 25 years through its distribution solutions in Pick up Store, Lockers and European Home delivery.' mod='mondialrelay'}<br/>
          {l s='However, it is also more than 9 European delivery countries, more than 150 000 e-merchants who trust us with their parcels, more than 45 000 Pick up points across Europe, more innovations to facilitate daily life and more convenience and security for customers at the best price.' mod='mondialrelay'} <br/>
        </p>

        <div class="hidden-xs">
          <p>
            {l s='Offer your customers a simple, safe and practical as well as economical delivery service:' mod='mondialrelay'} <br/>
            {l s='[1] Good value for money [/1] : No more high parcel delivery costs! With Mondial Relay - InPost, consumers benefit from an unbeatable quality/price ratio' mod='mondialrelay' tags=['<strong>']} <br/>
            {l s='[1] Simple [/1] : we have only one idea in mind "to facilitate everyone\'s life". Sending or receiving a parcel with Mondial Relay is as easy as a smile.' mod='mondialrelay' tags=['<strong>']} <br/>
            {l s='[1] Safe and practical [/1] : Mondial Relay - InPost takes care of your shipments. Thanks to rigorous tracking, customers can follow their parcel and collect it from our Pick up point and Lockers using a PIN or QR Code.' mod='mondialrelay' tags=['<strong>']}
          </p>

          <p>
            {l s='Mondial Relay is evaluated every day by hundreds of customers thanks to the Avis Vérifiés, certified by AFNOR (French ISO organisation), which gives a score of 9.1/10' mod='mondialrelay'} <br/>
            <a href="https://www.mondialrelay.fr/solutionspro/decouvrez-votre-offre/" target="_blank">{l s=' Discover Mondial Relay – InPost offers' mod='mondialrelay'}</a>
          </p>
        </div>

        <div class="visible-xs">
          <p>
            {l s='Give your customers a cheap, easy, safe and convenient delivery offer.' mod='mondialrelay'}
          </p>

          <p>
            <a href="https://www.mondialrelay.fr/solutionspro/decouvrez-votre-offre/" target="_blank">{l s=' Discover Mondial Relay offers' mod='mondialrelay'}</a>
          </p>
        </div>
      </div>
      
    </div>
  {/if}
{/block}

<prestashop-accounts style="margin-bottom: 15px;"></prestashop-accounts>

<div id="prestashop-cloudsync" style="margin-bottom: 15px;"></div>

{if $with_mondialrelay_header}
  {block name="help_guide"}
    {if $help_link != 'AdminMondialrelayHelp'}
    <div class="alert alert-info">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      {include '../mondialrelay_help/steps.tpl'}
    </div>
    {/if}
  {/block}
{/if}

{block name="mondialrelay_content-body"}
  {include 'content.tpl'}
{/block}

{block name="mondialrelay_content-append"}
{/block}

{if isset($urlAccountsCdn)}
  <script src="{$urlAccountsCdn|escape:'htmlall':'UTF-8'}" rel=preload></script>
  
  <script>
      /*********************
      * PrestaShop Account *
      * *******************/
      window?.psaccountsVue?.init();
  </script>
  {/if}

{if isset($urlCloudsync)}
    <script src="{$urlCloudsync|escape:'htmlall':'UTF-8'}"></script>

    <script>
        const cdc = window.cloudSyncSharingConsent;

        cdc.init('#prestashop-cloudsync');
        cdc.on('OnboardingCompleted', (isCompleted) => {
            console.log('OnboardingCompleted', isCompleted);

        });
        cdc.isOnboardingCompleted((isCompleted) => {
            console.log('Onboarding is already Completed', isCompleted);
        });
    </script>
{/if}