/**
 * NOTICE OF LICENSE
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

// modules ES6
import * as MR_Widget from '../../mondialrelay_widget';

export let removeCarrier = () => {
  for (let [id_carrier, carrier] of Object.entries(MONDIALRELAY_CARRIER_METHODS)) {
    if (
        (['ES', 'IT', 'PT'].includes(MONDIALRELAY_COUNTRY_ISO) && carrier.delivery_type !== "IP") ||
        (!['ES', 'IT', 'PT'].includes(MONDIALRELAY_COUNTRY_ISO) && carrier.delivery_type !== "MR") ||
        !MONDIALRELAY_ADDRESS_OPC
    ) {
      if ((carrier = $('[name^="delivery_option"][value="' + id_carrier + ',"]')[0])) {
        pointCarrier(carrier, remove);
      }
    }
  }
}

let pointCarrier = (carrier, callback) => {
  let deliveryOption;
  if ((deliveryOption = carrier.closest('.delivery-option') || carrier.closest('.delivery_option'))) {
    callback(deliveryOption);
  }
}

let remove = (deliveryOption) => {
  deliveryOption.remove();
}


const widget = MR_Widget.widget;

const isRelayCarrierSelected = () => {
  return MONDIALRELAY_NATIVE_RELAY_CARRIERS_IDS.includes(getSelectedCarrierId());
};

const getSelectedCarrierId = () => {
  let id = $('[name^="delivery_option"]:checked').val();
  if (typeof id !== 'undefined') {
      let new_id = id.split(',');
      id = new_id[0];
  }
  return id ? id : false;
};

const getCarrierDeliveryMode = (id_carrier) => {
  return MONDIALRELAY_CARRIER_METHODS[id_carrier]
      ? MONDIALRELAY_CARRIER_METHODS[id_carrier]['delivery_mode']
      : null;
};

$(document).ready(function() {
  removeCarrier();
});

$(document).on('click', widget.save_button, function(ev) {
  ev.preventDefault();
  ev.stopPropagation();

  widget.displayErrors(null);
  $(widget.summary_container).empty().show();
  widget.addLoader(widget.summary_container);

  $(widget).trigger('mondialrelay.saveSelectedRelay.before');

  let params = {
    ajax: true,
    action: 'saveSelectedRelay',
    mondialrelay_selectedRelay: MONDIALRELAY_SELECTED_RELAY_IDENTIFIER ? MONDIALRELAY_SELECTED_RELAY_IDENTIFIER : 0,
    mondialrelay_selectedRelay_infos : MONDIALRELAY_SELECTED_RELAY_INFOS ? MONDIALRELAY_SELECTED_RELAY_INFOS : [],
    id_carrier: getSelectedCarrierId(),
  };

  $.ajax(
  {
    type : 'POST',
    url: MONDIALRELAY_AJAX_CHECKOUT_URL,
    data : params,
    dataType: 'json',
    success: function(response) {
      if (!response) {
        this.error(response);
        return;
      }

      if (response.status == 'ok') {
        widget.savedRelay = MONDIALRELAY_SELECTED_RELAY_IDENTIFIER;
        $(widget.summary_container)
          .html(response.content.relaySummary);
        widget.hide();
        $(widget.save_container).hide();
        $(widget.widget_container).slideUp();
        $(widget.save_container).slideUp();
      } else {
        widget.savedRelay = null;
        if (response.error.length) {
          widget.displayErrors(response.error);
        }
        widget.removeLoader($(widget.summary_container));
        $(widget).trigger('mondialrelay.saveSelectedRelay.error');
        return;
      }

      $(widget).trigger('mondialrelay.saveSelectedRelay.success');
    },
    error: function(response) {
      widget.savedRelay = null;
      alert(MONDIALRELAY_SAVE_RELAY_ERROR);
      $(widget).trigger('mondialrelay.saveSelectedRelay.error');
      widget.removeLoader($(widget.summary_container));
    }
  });

  return false;
});

const updateSaveRelayButtons = () => {
  const carrierId = getSelectedCarrierId();
  if (!carrierId) return;

  const mode = getCarrierDeliveryMode(carrierId);
  if (!mode) {
    $('#mondialrelay_save-container').hide();
    return;
  }

  $('#mondialrelay_save-container').show();

  $('.mondialrelay_save-relay')
      .hide()
      .removeAttr('id');

  const $saveBtn = $(`.mondialrelay_save-relay[data-mr-mode="${mode}"]`);

  $saveBtn
      .attr('id', 'mondialrelay_save-relay')
      .show();
};

$(document).ready(() => {
  updateSaveRelayButtons();

  prestashop.on('updatedDeliveryForm', () => {
    updateSaveRelayButtons();
  });
});

export {
  isRelayCarrierSelected,
  getSelectedCarrierId,
  getCarrierDeliveryMode,
  widget,
};
