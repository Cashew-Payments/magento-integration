define([
  "uiComponent",
  "Magento_Checkout/js/model/payment/renderer-list",
], function (Component, rendererList) {
  "use strict";
  rendererList.push({
    type: "cashewpayment",
    component: "CashewPayments/js/view/payment/method-renderer/cashew",
  });
  return Component.extend({});
});
