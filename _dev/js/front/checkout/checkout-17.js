import * as MR from './checkout';

let selectedCarrierExtraContent = null;
let flagPaymentStepNeedsRefresh = null;
let isPaymentStepReachable = null;

const getCarrierExtraContent = (input) => {
  let selected = input.closest('.delivery-options__item').find('.carrier__extra-content');
  if (!selected.length) {
    selected = input.closest('.delivery-option').next('.carrier-extra-content');
  }
  return selected;
};

const setSelectedCarrierExtraContent = () => {
  selectedCarrierExtraContent = getCarrierExtraContent($('[name^="delivery_option"]:checked'));
  return selectedCarrierExtraContent;
};

const fixPrepareMondialRelay = (id_carrier) => {
  selectedCarrierExtraContent = getCarrierExtraContent($('#delivery_option_' + id_carrier));
  return selectedCarrierExtraContent;
};

const setProcessLocked = (lock) => {
  const step = $("#checkout-delivery-step");
  const button = $("button[name='confirmDeliveryOption']");

  if (lock) {
    step.removeClass('-complete');
    button.attr('disabled', true);
  } else {
    step.addClass('-complete');
    button.attr('disabled', false);
  }
};

const forceDisableMaxHeightUntilLoaded = () => {
  const wrapper = selectedCarrierExtraContent.closest(
      '.carrier__extra-content-wrapper, .carrier__extra-content-wrap, .carrier-extra-content'
  );

  if (!wrapper.length) return;

  const interval = setInterval(() => {
    const contentHeight = wrapper.find('.carrier__extra-content')[0]?.scrollHeight || 0;

    if (contentHeight > 100) {
      wrapper.each(function() {
        this.style.removeProperty('max-height');
        this.style.removeProperty('overflow');
      });

      clearInterval(interval);
    }
  }, 1000);
};

const forceDisplayMondialRelayContent = () => {
  const wrapper = selectedCarrierExtraContent.closest(
      '.carrier__extra-content-wrapper, .carrier__extra-content-wrap, .carrier-extra-content'
  );

  if (wrapper.length) {
    wrapper.each(function () {
      this.style.setProperty('display', 'block', 'important');
      this.style.removeProperty('max-height');
      this.style.removeProperty('overflow');
    });
  }

  if (selectedCarrierExtraContent.length) {
    selectedCarrierExtraContent.each(function () {
      this.style.setProperty('display', 'block', 'important');
    });
  }
};


const initializeMondialRelay = () => {
  if (MONDIALRELAY_CARRIER_METHODS.length > 0) {
    const firstCarrierId = MONDIALRELAY_CARRIER_METHODS[Object.keys(MONDIALRELAY_CARRIER_METHODS)[0]].id_carrier;

    fixPrepareMondialRelay(firstCarrierId);

    $('#mondialrelay_content').appendTo(selectedCarrierExtraContent);
    forceDisplayMondialRelayContent();
    forceDisableMaxHeightUntilLoaded();

    MR.widget.init({
      ColLivMod: MR.getCarrierDeliveryMode(firstCarrierId)
    });
  }
};

$(MR.widget).on('mondialrelay.ready', function() {
  if (MR.isRelayCarrierSelected()) {
    setSelectedCarrierExtraContent();
    forceDisplayMondialRelayContent();
    forceDisableMaxHeightUntilLoaded();

    if (isPaymentStepReachable === null) {
      isPaymentStepReachable = $("#checkout-payment-step").hasClass('-reachable');
    }
    $("#checkout-payment-step").removeClass('-reachable').addClass('-unreachable');

    $('#mondialrelay_content').appendTo(selectedCarrierExtraContent);

    if (!MR.widget.savedRelay) {
      setProcessLocked(true);
      MR.widget.show();
    } else {
      $(MR.widget.summary_container).show();
      MR.widget.hide();

      if ($("#checkout-payment-step").hasClass('-unreachable')) {
        $("#checkout-payment-step").removeClass('-unreachable').addClass('-reachable');
      }
    }

    MR.widget.init({
      ColLivMod: MR.getCarrierDeliveryMode(MR.getSelectedCarrierId())
    });
  }

  $(MR.widget.widget_container).on('click', function(ev) {
    ev.preventDefault();
    ev.stopPropagation();
  });
});

$(document).on('change', '#js-delivery [name^="delivery_option"]', function() {
  if (!MR.isRelayCarrierSelected()) {
    if (selectedCarrierExtraContent) {
      selectedCarrierExtraContent.hide();
      selectedCarrierExtraContent = null;
    }
    setProcessLocked(false);
    MR.widget.resetSelectedRelay();
    MR.widget.resetSavedRelay(true);

    if (flagPaymentStepNeedsRefresh) {
      $("#checkout-payment-step .content").append(flagPaymentStepNeedsRefresh);
      flagPaymentStepNeedsRefresh = null;
    }
    if (isPaymentStepReachable !== null && isPaymentStepReachable) {
      $("#checkout-payment-step").addClass('-reachable').removeClass('-unreachable');
    }

    return;
  }

  flagPaymentStepNeedsRefresh = $('.js-cart-payment-step-refresh').clone();
  $('.js-cart-payment-step-refresh').remove();

  if (isPaymentStepReachable === null) {
    isPaymentStepReachable = $("#checkout-payment-step").hasClass('-reachable');
  }
  $("#checkout-payment-step").removeClass('-reachable').addClass('-unreachable');

  if (!MR.widget.savedRelay) {
    setProcessLocked(true);
    MR.widget.show();
  } else if (!$(this).is(MR.widget.selected_relay_input)) {
    MR.widget.hide();
  }

  if (selectedCarrierExtraContent) {
    selectedCarrierExtraContent.hide();
  }

  if (MR.widget.widget_current_params != null) {
    const oldDeliveryMode = MR.widget.widget_current_params.ColLivMod;
    const newDeliveryMode = MR.getCarrierDeliveryMode(MR.getSelectedCarrierId());

    if (oldDeliveryMode !== newDeliveryMode) {
      MR.widget.resetSelectedRelay();
      MR.widget.resetSavedRelay();
    }
  }

  setSelectedCarrierExtraContent();
  $('#mondialrelay_content').appendTo(selectedCarrierExtraContent);
  forceDisplayMondialRelayContent();
  forceDisableMaxHeightUntilLoaded();
  MR.widget.initOrUpdate({
    ColLivMod: MR.getCarrierDeliveryMode(MR.getSelectedCarrierId())
  });
});

$(document).on('submit', '#js-delivery', function(ev) {
  if (MR.isRelayCarrierSelected() && !MR.widget.savedRelay) {
    ev.preventDefault();
    ev.stopPropagation();

    setSelectedCarrierExtraContent();
    $('#mondialrelay_content').appendTo(selectedCarrierExtraContent);
    forceDisplayMondialRelayContent();
    forceDisableMaxHeightUntilLoaded();

    MR.widget.initOrUpdate({
      ColLivMod: MR.getCarrierDeliveryMode(MR.getSelectedCarrierId())
    });

    alert(MONDIALRELAY_NO_SELECTION_ERROR);
    return false;
  }
  return true;
});

prestashop.on('updatedDeliveryForm', function() {
  MR.widget.savedRelay = MONDIALRELAY_SELECTED_RELAY_IDENTIFIER;
  initializeMondialRelay();
});

$('#js-delivery').on('change', '#mondialrelay_content *', function(ev) {
  ev.preventDefault();
  ev.stopPropagation();
});

$(MR.widget).on('mondialrelay.saveSelectedRelay.before', function() {
  setProcessLocked(true);
  $("[name^='delivery_option']")
      .attr('readonly', true)
      .on('click.mondialrelay.lock', function(ev) {
        ev.preventDefault();
        ev.stopPropagation();
      });
});

$(MR.widget).on('mondialrelay.saveSelectedRelay.success', function() {
  setProcessLocked(false);
  $("[name^='delivery_option']")
      .attr('readonly', false)
      .off('click.mondialrelay.lock');
});

$(MR.widget).on('mondialrelay.saveSelectedRelay.error', function() {
  setProcessLocked(true);
  $("[name^='delivery_option']")
      .attr('readonly', false)
      .off('click.mondialrelay.lock');
});

window.mondialrelayWidget = MR.widget;
