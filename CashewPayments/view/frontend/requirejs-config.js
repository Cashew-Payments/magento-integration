var config = {
  map: {
    "CashewPayments/js/view/placeOrder": {
      "Magento_Checkout/js/view/payment/default":
        "Magento_Checkout/js/view/payment/default",
    },
    "*": {
      "Magento_Checkout/js/view/payment/default":
        "CashewPayments/js/view/placeOrder",
    },
  },
};
