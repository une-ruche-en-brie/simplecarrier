{*
 * NOTICE OF LICENSE
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *}

{if $tracking_url}
    <div class="mondial-relay-tracking">
        <div class="col-lg-6 col-md-6 col-sm-6" style="padding-left: 0;">
            <article id="mondial-relay-tracking-url" class="box" style="text-align: center">
                <a href="{$tracking_url}" target="_blank">{l s='Track my parcel on Mondial Relay – InPost' mod='mondialrelay'}</a>
            </article>
            <br/>
        </div>
    </div>
    <div class="clearfix"></div>
{/if}