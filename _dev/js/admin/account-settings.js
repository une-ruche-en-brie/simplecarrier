/**
 * NOTICE OF LICENSE
 *
 * @author    202 ecommerce <tech@202-ecommerce.com>
 * @author    Mondial Relay
 * @copyright Copyright (c) Mondial Relay
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

// modules ES6
import * as MR from './global';

const settings = MONDIALRELAY_ACCOUNTSETTINGS;
const settings_api2 = MONDIALRELAY_ACCOUNTSETTINGS_API2;

$(document).on('click', '#mondialrelay_check-connection', function () {
  const button = this;
  const form = button.form;

  let params = {
  ajax: true,
  action: 'checkConnection'
  };

  settings.checkConnectionFields.forEach(field => {
  params[field] = $(form).find(`[name="${field}"]`).val();
  });

  MR.clearAjaxMessages();
  button.disabled = true;

  $.ajax({
  url: settings.accountSettingsUrl,
  method: 'POST',
  data: params,
  dataType: 'json',

  success: response => {
    MR.addAjaxMessages(response);
    MR.displayAjaxMessages();
  },

  error: response => {
    MR.addAjaxError(MONDIALRELAY_MESSAGES.unknown_error);
    MR.displayAjaxMessages();
  },

  complete: () => {
    button.disabled = false
  }
  });
});

$(document).on('click', '#mondialrelay_check-connection-api2', function () {
  const button = this;
  const form = button.form;

  let params = {
  ajax: true,
  action: 'checkConnectionApi2'
  };
  settings_api2.checkConnectionFields.forEach(field => {
  params[field] = $(form).find(`[name="${field}"]`).val();
  });

  MR.clearAjaxMessages();
  button.disabled = true;

  $.ajax({
  url: settings_api2.accountSettingsUrl,
  method: 'POST',
  data: params,
  dataType: 'json',

  success: response => {
    MR.addAjaxMessages(response);
    MR.displayAjaxMessages();
  },

  error: response => {
    MR.addAjaxError(MONDIALRELAY_MESSAGES.unknown_error);
    MR.displayAjaxMessages();
  },

  complete: () => {
    button.disabled = false
  }
  });
});

$(document).ready(() => {
  document.querySelector('.mondialrelay_home_delivery').addEventListener('change', (element) => {
    if(document.querySelector('.mondialrelay_home_delivery').value == 1){
      document.querySelectorAll('.api2_form').forEach((element) => {
        element.parentNode.parentNode.parentNode.classList.remove('hide')
      })
    } else {
      document.querySelectorAll('.api2_form').forEach((element) => {
        element.parentNode.parentNode.parentNode.classList.add('hide')
      })
    }
  })

  if(document.querySelector('.mondialrelay_home_delivery').value == 1){
    document.querySelectorAll('.api2_form').forEach((element) => {
      element.parentNode.parentNode.parentNode.classList.remove('hide')
    })
  } else {
    document.querySelectorAll('.api2_form').forEach((element) => {
      element.parentNode.parentNode.parentNode.classList.add('hide')
    })
  }
})

