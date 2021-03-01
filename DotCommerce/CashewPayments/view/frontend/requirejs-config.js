var config = {
  map: {
    "DotCommerce_CashewPayments/js/view/placeOrder": {
      "Magento_Checkout/js/view/payment/default":
        "Magento_Checkout/js/view/payment/default",
    },
    "*": {
      "Magento_Checkout/js/view/payment/default":
        "DotCommerce_CashewPayments/js/view/placeOrder",
    },
  },
};
